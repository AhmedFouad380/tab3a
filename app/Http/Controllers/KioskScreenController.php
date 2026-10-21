<?php

namespace App\Http\Controllers;

use App\Models\KioskMachine;
use App\Models\KioskSession;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PricingRule;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Smalot\PdfParser\Parser;

class KioskScreenController extends Controller
{
    /**
     * Display the full-screen interactive Terminal for the Kiosk Machine
     */
    public function show(string $machineCode)
    {
        $kiosk = KioskMachine::with('branch')
            ->where('machine_code', $machineCode)
            ->firstOrFail();

        // Get or create current active session
        $session = $this->getActiveSession($kiosk);

        $connectUrl = request()->getSchemeAndHttpHost().'/kiosk-connect/'.$session->session_token;

        $qrSvg = QrCode::size(280)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($connectUrl);

        $appName = SystemSetting::where('key', 'app_name')->first()?->value['ar'] ?? 'منصة طباعة';

        return view('kiosk.screen', compact('kiosk', 'session', 'qrSvg', 'connectUrl', 'appName'));
    }

    /**
     * Live Polling endpoint for the Kiosk Screen
     */
    public function screenStatus(string $machineCode): JsonResponse
    {
        $kiosk = KioskMachine::where('machine_code', $machineCode)->first();
        if (! $kiosk) {
            return response()->json(['error' => 'Kiosk not found'], 404);
        }

        // Get active session
        $session = $this->getActiveSession($kiosk);

        $connectUrl = request()->getSchemeAndHttpHost().'/kiosk-connect/'.$session->session_token;
        $qrSvg = (string) QrCode::size(280)->errorCorrection('H')->margin(1)->generate($connectUrl);

        return response()->json([
            'machine_code' => $kiosk->machine_code,
            'machine_name' => $kiosk->name['ar'] ?? $kiosk->machine_code,
            'status' => $kiosk->status,
            'paper_a4' => $kiosk->paper_tray_a4_sheets,
            'paper_a3' => $kiosk->paper_tray_a3_sheets,
            'black_toner' => $kiosk->black_toner_level,
            'session' => [
                'id' => $session->id,
                'token' => $session->session_token,
                'status' => $session->status,
                'file_name' => $session->file_name,
                'detected_page_count' => $session->detected_page_count,
                'copies_count' => $session->copies_count,
                'total_sheets' => $session->total_sheets,
                'color_mode' => $session->color_mode,
                'side_mode' => $session->side_mode,
                'total_price' => (float) $session->total_price,
            ],
            'qr_svg' => $qrSvg,
            'connect_url' => $connectUrl,
        ]);
    }

    /**
     * Mobile Web View: Opened when customer scans QR Code with smartphone
     */
    public function connect(string $token)
    {
        // Support either machine qr_token or session_token
        $session = KioskSession::where('session_token', $token)->first();

        if (! $session) {
            $kiosk = KioskMachine::where('qr_token', $token)->firstOrFail();
            $session = KioskSession::create([
                'kiosk_machine_id' => $kiosk->id,
                'session_token' => 'SESS-'.strtoupper(bin2hex(random_bytes(4))),
                'status' => 'connected',
                'connected_at' => now(),
                'expires_at' => now()->addMinutes(10),
            ]);
        } else {
            $kiosk = $session->kiosk;
            if ($session->status === 'waiting_scan') {
                $session->update([
                    'status' => 'connected',
                    'connected_at' => now(),
                ]);
            }
        }

        $maxFileSizeMb = $kiosk->getEffectiveMaxFileSizeMb();
        $minOrderPrice = $kiosk->getEffectiveMinOrderPrice();
        $maxSheets = $kiosk->getEffectiveMaxSheets();

        return view('kiosk.mobile-connect', compact('kiosk', 'session', 'maxFileSizeMb', 'minOrderPrice', 'maxSheets'));
    }

    /**
     * Mobile File Upload & Calculation
     */
    public function mobileUpload(Request $request, string $token): JsonResponse
    {
        $session = KioskSession::where('session_token', $token)->firstOrFail();
        $kiosk = $session->kiosk;

        $maxMb = $kiosk->getEffectiveMaxFileSizeMb();
        $maxKb = $maxMb * 1024;

        $request->validate([
            'file' => "required|file|mimes:pdf,jpg,jpeg,png|max:{$maxKb}",
            'copies' => 'nullable|integer|min:1|max:50',
            'color_mode' => 'nullable|in:black_and_white,color',
            'side_mode' => 'nullable|in:single_sided,double_sided',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $fileSizeBytes = $file->getSize();

        $path = $file->store('kiosk_uploads', 'public');
        $fullPath = storage_path('app/public/'.$path);

        $detectedPages = 1;
        if ($extension === 'pdf') {
            try {
                $parser = new Parser;
                $pdf = $parser->parseFile($fullPath);
                $pages = $pdf->getPages();
                $detectedPages = count($pages) ?: 1;
            } catch (\Throwable $e) {
                $detectedPages = 1;
            }
        }

        $copies = (int) ($request->copies ?? 1);
        $colorMode = $request->color_mode ?? 'black_and_white';
        $sideMode = $request->side_mode ?? 'single_sided';

        $sheetsPerCopy = $sideMode === 'double_sided' ? (int) ceil($detectedPages / 2) : $detectedPages;
        $totalSheets = $sheetsPerCopy * $copies;

        // Constraint 1: Check maximum sheets
        $maxSheetsAllowed = $kiosk->getEffectiveMaxSheets();
        if ($totalSheets > $maxSheetsAllowed) {
            return response()->json([
                'success' => false,
                'message' => "عدد الأوراق المطلوب ({$totalSheets} ورقة) يتجاوز الحد الأقصى للطباعة الفورية ({$maxSheetsAllowed} ورقة).",
            ], 422);
        }

        // Constraint 2: Check available paper in tray
        if ($totalSheets > $kiosk->paper_tray_a4_sheets) {
            return response()->json([
                'success' => false,
                'message' => "لا يتوفر ورق كافٍ بالماكينة ({$kiosk->paper_tray_a4_sheets} ورقة متاحة والمطلوب {$totalSheets}).",
            ], 422);
        }

        // Pricing calculation
        $rule = PricingRule::where('paper_size', 'A4')
            ->where('color_mode', $colorMode)
            ->where('side_mode', $sideMode)
            ->where('is_active', true)
            ->first();

        $unitPrice = $rule ? (float) $rule->price_per_page : ($colorMode === 'color' ? 1.50 : 0.50);
        $subtotal = ($unitPrice * $detectedPages) * $copies;

        // Constraint 3: Minimum order price
        $minPrice = $kiosk->getEffectiveMinOrderPrice();
        if ($subtotal < $minPrice) {
            $subtotal = $minPrice;
        }

        $taxAmount = round($subtotal * 0.15, 2);
        $totalAmount = round($subtotal + $taxAmount, 2);

        $session->update([
            'status' => 'file_uploaded',
            'file_name' => $originalName,
            'file_path' => $path,
            'file_size_bytes' => $fileSizeBytes,
            'detected_page_count' => $detectedPages,
            'copies_count' => $copies,
            'total_sheets' => $totalSheets,
            'paper_size' => 'A4',
            'color_mode' => $colorMode,
            'side_mode' => $sideMode,
            'total_price' => $totalAmount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم استلام الملف وتحديث الشاشة بنجاح',
            'data' => [
                'file_name' => $originalName,
                'detected_pages' => $detectedPages,
                'copies' => $copies,
                'total_sheets' => $totalSheets,
                'subtotal' => $subtotal,
                'tax' => $taxAmount,
                'total_price' => $totalAmount,
            ],
        ]);
    }

    /**
     * Mobile Payment confirmation -> changes session to 'printing' and creates order
     */
    public function mobilePay(Request $request, string $token): JsonResponse
    {
        $session = KioskSession::where('session_token', $token)->firstOrFail();
        $kiosk = $session->kiosk;

        if (! $session->file_path) {
            return response()->json(['success' => false, 'message' => 'يرجى رفع ملف أولاً'], 422);
        }

        // Change session to awaiting_payment briefly or straight to printing
        $session->update(['status' => 'printing']);

        // Decrement paper tray
        $newSheets = max(0, $kiosk->paper_tray_a4_sheets - $session->total_sheets);
        $kiosk->update([
            'paper_tray_a4_sheets' => $newSheets,
            'is_paper_low' => $newSheets < 50,
            'is_paper_empty' => $newSheets == 0,
        ]);

        // Determine User (Authenticated or Kiosk Walk-in Guest)
        $userId = auth()->id();
        if (! $userId) {
            $guestUser = User::first() ?? User::create([
                'name' => 'عميل طباعة فورية',
                'email' => 'kiosk-guest-'.uniqid().'@taba3a.com',
                'phone' => '050'.rand(1000000, 9999999),
                'password' => bcrypt(uniqid()),
            ]);
            $userId = $guestUser->id;
        }

        // Create official Order record
        $order = Order::create([
            'order_number' => 'KSK-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3))),
            'user_id' => $userId,
            'order_type' => 'self_printing',
            'branch_id' => $kiosk->branch_id,
            'kiosk_machine_id' => $kiosk->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => $request->payment_method ?? 'card',
            'subtotal' => round($session->total_price / 1.15, 2),
            'tax_amount' => round($session->total_price - ($session->total_price / 1.15), 2),
            'total_amount' => $session->total_price,
            'printer_job_id' => 'JOB-'.uniqid(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'original_file_name' => $session->file_name,
            'file_path' => $session->file_path,
            'file_extension' => pathinfo($session->file_name, PATHINFO_EXTENSION),
            'file_size_bytes' => $session->file_size_bytes ?? 1024,
            'detected_page_count' => $session->detected_page_count,
            'pages_to_print_count' => $session->detected_page_count,
            'page_range_selection' => 'all',
            'paper_size' => $session->paper_size,
            'color_mode' => $session->color_mode,
            'side_mode' => $session->side_mode,
            'copies_count' => $session->copies_count,
            'total_sheets_needed' => $session->total_sheets,
            'unit_price_per_page' => round($session->total_price / max(1, $session->detected_page_count), 2),
            'total_item_price' => $session->total_price,
        ]);

        $session->update([
            'order_id' => $order->id,
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم الدفع بنجاح وجاري الطباعة الآن',
            'order_number' => $order->order_number,
        ]);
    }

    /**
     * Complete and Reset session back to waiting_scan
     */
    public function completeAndReset(Request $request, string $token): JsonResponse
    {
        $session = KioskSession::where('session_token', $token)->firstOrFail();
        $targetStatus = in_array($request->input('status'), ['completed', 'cancelled', 'expired'])
            ? $request->input('status')
            : 'completed';

        $session->update([
            'status' => $targetStatus,
            'completed_at' => now(),
        ]);

        // Create new standby session for the next customer
        $newSession = KioskSession::create([
            'kiosk_machine_id' => $session->kiosk_machine_id,
            'session_token' => 'SESS-'.strtoupper(bin2hex(random_bytes(4))),
            'status' => 'waiting_scan',
            'expires_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'success' => true,
            'new_token' => $newSession->session_token,
        ]);
    }

    /**
     * Get or create a valid active session, expiring any old or stuck sessions
     */
    private function getActiveSession(KioskMachine $kiosk): KioskSession
    {
        // 1. Expire past sessions whose expires_at is in the past
        KioskSession::where('kiosk_machine_id', $kiosk->id)
            ->whereIn('status', ['waiting_scan', 'connected', 'file_uploaded', 'awaiting_payment', 'printing'])
            ->where('expires_at', '<', now())
            ->update([
                'status' => 'expired',
                'completed_at' => now(),
            ]);

        // 2. If a printing session was initiated more than 3 minutes ago, consider it completed
        KioskSession::where('kiosk_machine_id', $kiosk->id)
            ->where('status', 'printing')
            ->where('updated_at', '<', now()->subMinutes(3))
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

        // 3. Find latest active non-expired session
        $session = KioskSession::where('kiosk_machine_id', $kiosk->id)
            ->whereIn('status', ['waiting_scan', 'connected', 'file_uploaded', 'awaiting_payment', 'printing'])
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        // 4. Create new fresh session if none exists
        if (! $session) {
            $session = KioskSession::create([
                'kiosk_machine_id' => $kiosk->id,
                'session_token' => 'SESS-'.strtoupper(bin2hex(random_bytes(4))),
                'status' => 'waiting_scan',
                'expires_at' => now()->addMinutes(10),
            ]);
        }

        return $session;
    }
}
