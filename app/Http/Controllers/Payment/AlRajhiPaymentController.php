<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\KioskSession;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Services\Payment\AlRajhiPaymentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AlRajhiPaymentController extends Controller
{
    public function __construct(
        protected AlRajhiPaymentService $alRajhiService
    ) {}

    /**
     * Start Al Rajhi Checkout session for an Order.
     */
    public function checkout(Request $request, Order $order): RedirectResponse|View
    {
        // Prevent paying for already paid orders
        if ($order->payment_status === 'paid') {
            return redirect()->route('admin.orders.invoice', $order)
                ->with('info', 'هذا الطلب مسدد بالفعل.');
        }

        try {
            $responseUrl = route('payment.alrajhi.callback');
            $errorUrl = route('payment.alrajhi.error');

            $result = $this->alRajhiService->initiatePayment($order, $responseUrl, $errorUrl);

            if (! $result['success'] || empty($result['payment_url'])) {
                Log::warning('Al Rajhi checkout initiation failed', [
                    'order_id' => $order->id,
                    'error' => $result['error'],
                ]);

                return view('payments.alrajhi.failed', [
                    'order' => $order,
                    'errorMessage' => $result['error'] ?? 'فشل الاتصال ببوابة دفع الراجحي.',
                ]);
            }

            return redirect()->away($result['payment_url']);

        } catch (Exception $e) {
            Log::error('Al Rajhi checkout exception', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            return view('payments.alrajhi.failed', [
                'order' => $order,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle incoming callback from Al Rajhi Bank (POST).
     */
    public function callback(Request $request): View|RedirectResponse
    {
        $encryptedTrandata = $request->input('trandata');

        if (empty($encryptedTrandata)) {
            Log::warning('Al Rajhi callback received with missing trandata', [
                'params' => $request->all(),
            ]);

            return view('payments.alrajhi.failed', [
                'order' => null,
                'errorMessage' => 'لم يتم استلام بيانات المعاملة المشفرة من البنك.',
            ]);
        }

        $verification = $this->alRajhiService->verifyCallback($encryptedTrandata);
        $trackId = $verification['track_id'];
        $raw = $verification['raw'] ?? [];
        $orderId = $raw['udf1'] ?? null;

        $order = null;
        if (! empty($trackId)) {
            $order = Order::where('order_number', $trackId)->first();
        }
        if (! $order && ! empty($orderId)) {
            $order = Order::find($orderId);
        }

        if (! $order) {
            Log::error('Al Rajhi callback: Order not found', [
                'trackId' => $trackId,
                'orderId' => $orderId,
            ]);

            return view('payments.alrajhi.failed', [
                'order' => null,
                'errorMessage' => "لم يتم العثور على الطلب رقم: {$trackId}",
            ]);
        }

        if ($verification['is_paid']) {
            DB::transaction(function () use ($order, $verification, $raw) {
                // Update Order Status
                $newStatus = $order->order_type === 'self_printing' ? 'completed' : 'processing';

                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'alrajhi',
                    'status' => in_array($order->status, ['pending', 'cancelled']) ? $newStatus : $order->status,
                ]);

                // If self printing, update the kiosk session to 'printing'
                if ($order->order_type === 'self_printing') {
                    $session = KioskSession::where('order_id', $order->id)
                        ->orWhere(function ($query) use ($order) {
                            if ($order->kiosk_machine_id) {
                                $query->where('kiosk_machine_id', $order->kiosk_machine_id)
                                    ->whereIn('status', ['waiting_scan', 'connected', 'file_uploaded', 'awaiting_payment']);
                            }
                        })
                        ->latest()
                        ->first();

                    if ($session) {
                        $session->update([
                            'status' => 'printing',
                            'order_id' => $order->id,
                            'completed_at' => now(),
                        ]);
                    }
                }

                // Create or update Payment record
                Payment::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'gateway' => 'alrajhi',
                    ],
                    [
                        'user_id' => $order->user_id,
                        'transaction_id' => $verification['transaction_id'] ?? 'ARB-'.uniqid(),
                        'amount' => (float) ($verification['amount'] ?? $order->total_amount),
                        'currency' => 'SAR',
                        'status' => 'successful',
                        'paid_at' => now(),
                        'payment_method_type' => 'card',
                        'gateway_response' => $raw,
                    ]
                );

                // Add to Order Status History
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'comment' => [
                        'ar' => "تم السداد بنجاح عبر بنك الراجحي - رقم العملية: {$verification['transaction_id']}",
                        'en' => "Paid successfully via Al Rajhi Bank - Txn: {$verification['transaction_id']}",
                    ],
                ]);

                // Send notification to customer
                if ($order->user_id) {
                    AppNotification::create([
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'type' => 'payment_success',
                        'title' => [
                            'ar' => 'تم استلام دفعتك بنجاح! 💳',
                            'en' => 'Payment received successfully! 💳',
                        ],
                        'body' => [
                            'ar' => "تم تأكيد دفع مبلغ {$order->total_amount} ر.س للطلب رقم {$order->order_number} عبر بنك الراجحي.",
                            'en' => "Your payment of SAR {$order->total_amount} for order #{$order->order_number} has been confirmed.",
                        ],
                    ]);
                }
            });

            Log::info('Al Rajhi Payment Confirmed Successfully', [
                'order_number' => $order->order_number,
                'txn_id' => $verification['transaction_id'],
            ]);

            return view('payments.alrajhi.success', compact('order', 'verification'));
        }

        // Payment failed or declined
        Log::warning('Al Rajhi Payment Declined / Failed', [
            'order_number' => $order->order_number,
            'result' => $verification['result_code'],
            'error' => $verification['error'],
        ]);

        Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'transaction_id' => $verification['transaction_id'] ?? 'ARB-FAIL-'.uniqid(),
            'gateway' => 'alrajhi',
            'amount' => (float) ($verification['amount'] ?? $order->total_amount),
            'currency' => 'SAR',
            'status' => 'failed',
            'gateway_response' => $raw,
        ]);

        return view('payments.alrajhi.failed', [
            'order' => $order,
            'errorMessage' => $verification['error'] ?? 'تم رفض العملية من قبل البنك.',
        ]);
    }

    /**
     * Handle incoming error/cancellation from Al Rajhi Bank.
     */
    public function error(Request $request): View
    {
        $encryptedTrandata = $request->input('trandata');
        $errorMessage = 'تم إلغاء عملية الدفع أو تعذر إكمالها.';
        $order = null;

        if (! empty($encryptedTrandata)) {
            $verification = $this->alRajhiService->verifyCallback($encryptedTrandata);
            $trackId = $verification['track_id'];
            if (! empty($trackId)) {
                $order = Order::where('order_number', $trackId)->first();
            }
            if (! empty($verification['error'])) {
                $errorMessage = $verification['error'];
            }
        }

        return view('payments.alrajhi.failed', [
            'order' => $order,
            'errorMessage' => $errorMessage,
        ]);
    }

    /**
     * API Endpoint: Initiate Al Rajhi payment for Mobile Application / Native Webview.
     */
    public function apiInitiate(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== auth()->id() && ! auth()->user()?->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح بالوصول لهذا الطلب.',
            ], 403);
        }

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'الطلب مدفوع مسبقاً.',
            ], 422);
        }

        $responseUrl = route('payment.alrajhi.callback');
        $errorUrl = route('payment.alrajhi.error');

        $result = $this->alRajhiService->initiatePayment($order, $responseUrl, $errorUrl);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'تعذر بدء عملية الدفع مع بنك الراجحي.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'payment_url' => $result['payment_url'],
            'payment_id' => $result['payment_id'],
            'order_number' => $order->order_number,
            'total_amount' => (float) $order->total_amount,
        ]);
    }
}
