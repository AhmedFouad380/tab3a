<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة إلكترونية - طلب {{ $order->order_number }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        :root {
            --primary: #005bb5;
            --primary-dark: #004085;
            --primary-light: #e8f2fc;
            --text-dark: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --border-dark: #cbd5e1;
            --bg-page: #f1f5f9;
            --card-white: #ffffff;
            --success: #059669;
            --success-light: #ecfdf5;
            --danger: #dc2626;
            --danger-light: #fef2f2;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', system-ui, -apple-system, sans-serif;
        }

        .num-font {
            font-family: 'Plus Jakarta Sans', 'Cairo', sans-serif;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-body);
            min-height: 100vh;
            padding: 30px 15px 50px;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Top Action Bar (Hidden when printing) */
        .action-bar {
            max-width: 860px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        .action-bar-left, .action-bar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        /* Invoice Sheet Paper */
        .invoice-sheet {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            padding: 44px 48px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            border: 1px solid var(--border-color);
            position: relative;
        }

        /* Header section */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 24px;
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 24px;
        }

        .header-brand {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .brand-logo-img {
            max-height: 52px;
            width: auto;
            max-width: 260px;
            object-fit: contain;
            display: block;
            margin-bottom: 6px;
        }

        .brand-title {
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--primary);
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-subtitle {
            font-size: 0.88rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .brand-meta {
            margin-top: 8px;
            font-size: 0.82rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .invoice-badge-wrap {
            text-align: left;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }

        .tax-badge {
            background: var(--primary-light);
            color: var(--primary-dark);
            border: 1px solid #bfdbfe;
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .invoice-number-main {
            font-size: 1.25rem;
            font-weight: 900;
            color: var(--text-dark);
            margin-top: 4px;
        }

        .invoice-date-main {
            font-size: 0.82rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* Order Type Banner (Distinctive for Pre-Order vs Kiosk) */
        .order-type-banner {
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .order-type-banner.self-printing {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #93c5fd;
            color: #1e40af;
        }

        .order-type-banner.pre-order {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #86efac;
            color: #166534;
        }

        .banner-icon-title {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .banner-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .banner-text-title {
            font-size: 1.05rem;
            font-weight: 800;
            line-height: 1.3;
        }

        .banner-text-desc {
            font-size: 0.84rem;
            opacity: 0.88;
            font-weight: 600;
            margin-top: 2px;
        }

        .banner-pill-code {
            background: #ffffff;
            padding: 6px 14px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            white-space: nowrap;
        }

        /* Order & Customer Details Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 18px 22px;
            margin-bottom: 26px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .info-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
        }

        .info-val {
            font-size: 0.95rem;
            color: var(--text-dark);
            font-weight: 800;
        }

        .info-val.highlight {
            color: var(--primary);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 800;
            width: fit-content;
        }

        .status-paid {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .status-unpaid {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Items Table */
        .table-section {
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }

        .items-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 800;
            font-size: 0.82rem;
            padding: 12px 14px;
            text-align: right;
            border-bottom: 1px solid var(--border-color);
        }

        .items-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-dark);
            font-size: 0.88rem;
            vertical-align: middle;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .items-table tbody tr:hover {
            background-color: #fafbfc;
        }

        .item-spec-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 4px;
        }

        .spec-tag {
            background: #f1f5f9;
            color: #334155;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 5px;
            border: 1px solid #e2e8f0;
        }

        .spec-tag.color {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .spec-tag.finishing {
            background: #fdf4ff;
            color: #a21caf;
            border-color: #f5d0fe;
        }

        /* Summary & ZATCA QR Bottom Split */
        .bottom-split {
            display: grid;
            grid-template-columns: 1fr 1.35fr;
            gap: 24px;
            align-items: flex-start;
            margin-top: 10px;
            padding-top: 14px;
        }

        /* ZATCA QR Block */
        .zatca-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .zatca-qr-svg {
            width: 110px;
            height: 110px;
            background: #ffffff;
            padding: 4px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .zatca-qr-svg svg {
            width: 100% !important;
            height: 100% !important;
        }

        .zatca-text-wrap {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .zatca-title {
            font-size: 0.88rem;
            font-weight: 800;
            color: var(--text-dark);
        }

        .zatca-desc {
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .zatca-badge-ok {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #047857;
            font-weight: 700;
            font-size: 0.75rem;
            margin-top: 4px;
        }

        /* Financial Totals Card */
        .totals-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 18px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }

        .totals-row.final {
            background: var(--primary);
            color: #ffffff;
            padding: 14px 18px;
            font-size: 1.15rem;
            font-weight: 900;
            border-bottom: none;
        }

        .totals-label {
            font-weight: 700;
        }

        .totals-value {
            font-weight: 800;
        }

        /* Footer Terms */
        .invoice-footer {
            margin-top: 32px;
            padding-top: 18px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* Watermark stamp for teacher orders */
        .teacher-stamp {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 800;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                color: #000000 !important;
            }

            .action-bar {
                display: none !important;
            }

            .invoice-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }

            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }

            .totals-row.final {
                background: #005bb5 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .order-type-banner {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .tax-badge, .status-pill, .spec-tag {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
@php
    $localize = function ($value, string $default = '') {
        if (is_array($value)) {
            return $value['ar'] ?? $value['en'] ?? (reset($value) ?: $default);
        }
        return is_string($value) && trim($value) !== '' ? $value : $default;
    };

    $branchName = $localize($order->branch?->name, 'الفرع الرئيسي');
    $kioskBranchName = $localize($order->kiosk?->branch?->name, $branchName);
    $kioskLocation = $localize($order->kiosk?->location_description, '');
    $customerName = $localize($order->user?->name, 'عميل التطبيق');
@endphp

    <!-- Action Bar (Buttons) -->
    <div class="action-bar">
        <div class="action-bar-left">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="fa-solid fa-print"></i>
                <span>طباعة الفاتورة (Print)</span>
            </button>
            <a href="{{ route('filament.admin.resources.orders.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-right"></i>
                <span>العودة للطلبات</span>
            </a>
        </div>
        <div class="action-bar-right">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 700;">
                طلب رقم: <strong class="num-font" style="color: var(--text-dark);">{{ $order->order_number }}</strong>
            </span>
        </div>
    </div>

    <!-- Invoice Sheet (Printable A4) -->
    <div class="invoice-sheet">

        <!-- Header -->
        <header class="invoice-header">
            <div class="header-brand">
                @if(!empty($companyLogoUrl))
                    <div class="brand-logo-wrap">
                        <img src="{{ $companyLogoUrl }}" alt="{{ $companyName }}" class="brand-logo-img">
                    </div>
                @else
                    <div class="brand-title">
                        <i class="fa-solid fa-print"></i>
                        <span>{{ $companyName }}</span>
                    </div>
                    <div class="brand-subtitle num-font">{{ $companyNameEn }}</div>
                @endif
                
                <div class="brand-meta">
                    <div><strong>الرقم الضريبي (VAT ID):</strong> <span class="num-font">{{ $vatNumber }}</span></div>
                    <div><strong>السجل التجاري (C.R):</strong> <span class="num-font">{{ $crNumber }}</span></div>
                    <div><strong>خدمة العملاء:</strong> <span class="num-font" dir="ltr">{{ $supportPhone }}</span></div>
                </div>
            </div>

            <div class="invoice-badge-wrap">
                <div class="tax-badge">
                    <i class="fa-solid fa-certificate"></i>
                    <span>فاتورة ضريبية مبسطة (Simplified Tax Invoice)</span>
                </div>
                <div class="invoice-number-main num-font">INV-{{ $order->order_number }}</div>
                <div class="invoice-date-main">
                    تاريخ الإصدار: <span class="num-font">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>
        </header>

        <!-- Dynamic Order Type Banner: Pre-Order vs Self-Printing -->
        @if($order->order_type === 'self_printing')
            <!-- KIOSK INSTANT PRINTING -->
            <div class="order-type-banner self-printing">
                <div class="banner-icon-title">
                    <div class="banner-icon" style="color: #0284c7;">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <div class="banner-text-title">خدمة الطباعة الذاتية الفورية (Self-Service Kiosk)</div>
                        <div class="banner-text-desc">
                            تمت معالجة وطباعة هذا الطلب فورياً عبر كشك الخدمة الذاتية.
                        </div>
                    </div>
                </div>
                <div class="banner-pill-code num-font" style="color: #005bb5;">
                    كود الجهاز: {{ $order->kiosk?->machine_code ?? 'KSK-Machine' }}
                </div>
            </div>
        @else
            <!-- PRE-ORDER / BRANCH PICKUP -->
            <div class="order-type-banner pre-order">
                <div class="banner-icon-title">
                    <div class="banner-icon" style="color: #16a34a;">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <div class="banner-text-title">خدمة الطباعة المسبقة (Pre-Order - استلام من الفرع)</div>
                        <div class="banner-text-desc">
                            طلب مجهز مسبقاً لاستلامه مباشرة من فرع: <strong>{{ $branchName }}</strong>
                        </div>
                    </div>
                </div>
                @if($order->is_teacher)
                    <div class="teacher-stamp">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>طلب معلّم (حساب تعليمي)</span>
                    </div>
                @else
                    <div class="banner-pill-code" style="color: #15803d;">
                        استلام فرع
                    </div>
                @endif
            </div>
        @endif

        <!-- Order & Customer Information Grid -->
        <div class="info-grid">
            <!-- Col 1 -->
            <div class="info-item">
                <span class="info-label">العميل (Customer)</span>
                <span class="info-val">
                    {{ $customerName }}
                    @if(!empty($order->user?->phone))
                        <span class="num-font" style="font-size: 0.88rem; color: var(--text-muted); font-weight: normal;" dir="ltr">
                            ({{ $order->user->phone }})
                        </span>
                    @endif
                </span>
            </div>

            <!-- Col 2 -->
            <div class="info-item">
                <span class="info-label">
                    {{ $order->order_type === 'self_printing' ? 'موقع الجهاز / الكشك' : 'فرع الاستلام' }}
                </span>
                <span class="info-val highlight">
                    @if($order->order_type === 'self_printing')
                        {{ $kioskBranchName }} 
                        @if(!empty($kioskLocation))
                            - <span style="font-weight: 600; font-size: 0.85rem;">{{ $kioskLocation }}</span>
                        @endif
                    @else
                        {{ $branchName }}
                    @endif
                </span>
            </div>

            <!-- Col 3 -->
            <div class="info-item">
                <span class="info-label">حالة السداد والدفع</span>
                <div>
                    @if($order->payment_status === 'paid')
                        <span class="status-pill status-paid">
                            <i class="fa-solid fa-check"></i>
                            <span>مدفوع (Paid)</span>
                        </span>
                    @else
                        <span class="status-pill status-unpaid">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>غير مدفوع (Unpaid)</span>
                        </span>
                    @endif
                    <span style="font-size: 0.82rem; color: var(--text-muted); margin-right: 6px;">
                        وسيلة الدفع: <strong>{{ $order->payment_method ? strtoupper(str_replace('_', ' ', $order->payment_method)) : 'إلكتروني' }}</strong>
                    </span>
                </div>
            </div>

            <!-- Col 4 -->
            <div class="info-item">
                <span class="info-label">
                    {{ $order->order_type === 'self_printing' ? 'وقت الطباعة الذاتية' : 'موعد الاستلام المجدول' }}
                </span>
                <span class="info-val num-font">
                    @if($order->order_type === 'self_printing')
                        {{ $order->created_at->format('Y-m-d H:i') }}
                    @else
                        {{ $order->scheduled_pickup_at ? $order->scheduled_pickup_at->format('Y-m-d H:i') : 'فوري عند الجاهزية' }}
                    @endif
                </span>
            </div>
        </div>

        <!-- Table of Order Items -->
        <div class="table-section">
            <div class="section-title">
                <i class="fa-solid fa-list-check" style="color: var(--primary);"></i>
                <span>تفاصيل المطبوعات والمواصفات الفنية</span>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 35px; text-align: center;">#</th>
                        <th>الملف والمواصفات (File & Specifications)</th>
                        <th style="width: 70px; text-align: center;">النسخ</th>
                        <th style="width: 75px; text-align: center;">الصفحات</th>
                        <th style="width: 90px; text-align: center;">سعر الصفحة</th>
                        <th style="width: 105px; text-align: left;">الإجمالي (ر.س)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $index => $item)
                        <tr>
                            <td style="text-align: center;" class="num-font">{{ $index + 1 }}</td>
                            <td>
                                <strong style="color: var(--text-dark);">{{ $item->original_file_name ?: 'مستند طباعة' }}</strong>
                                <div class="item-spec-tags">
                                    <span class="spec-tag">{{ strtoupper($item->paper_size ?? 'A4') }}</span>
                                    
                                    @if(($item->color_mode ?? '') === 'color')
                                        <span class="spec-tag color">🎨 ألوان (Color)</span>
                                    @else
                                        <span class="spec-tag">⚪ أسود وأبيض (B&W)</span>
                                    @endif

                                    @if(($item->side_mode ?? '') === 'double_sided')
                                        <span class="spec-tag">وجهين (2-Sided)</span>
                                    @else
                                        <span class="spec-tag">وجه واحد (1-Sided)</span>
                                    @endif

                                    @if($item->finishingOption)
                                        <span class="spec-tag finishing">
                                            ✨ تشطيب: {{ $localize($item->finishingOption->name) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: center;" class="num-font">{{ $item->copies_count ?? 1 }}</td>
                            <td style="text-align: center;" class="num-font">{{ $item->detected_page_count ?? 1 }}</td>
                            <td style="text-align: center;" class="num-font">
                                {{ number_format((float) ($item->unit_price_per_page ?? 0), 2) }}
                            </td>
                            <td style="text-align: left;" class="num-font">
                                <strong>{{ number_format((float) ($item->total_item_price ?? 0), 2) }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 20px;">
                                لا توجد تفاصيل للملفات
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bottom Section: ZATCA QR + Financial Totals -->
        <div class="bottom-split">
            
            <!-- Right: ZATCA Electronic QR Stamp -->
            <div class="zatca-box">
                <div class="zatca-qr-svg">
                    {!! $qrSvg !!}
                </div>
                <div class="zatca-text-wrap">
                    <div class="zatca-title">
                        <i class="fa-solid fa-qrcode" style="color: var(--primary); margin-left: 4px;"></i>
                        ختم الفاتورة الإلكترونية
                    </div>
                    <div class="zatca-desc">
                        رمز استجابة سريع مشفر ومعتمد مطابق لمتطلبات هيئة الزكاة والضريبة والجمارك (ZATCA) في المملكة العربية السعودية.
                    </div>
                    <div class="zatca-badge-ok">
                        <i class="fa-solid fa-shield-check"></i>
                        <span>فاتورة رقمية موثقة ومعتمدة</span>
                    </div>
                </div>
            </div>

            <!-- Left: Financial Breakdown Totals -->
            <div class="totals-card">
                <div class="totals-row">
                    <span class="totals-label">المجموع الفرعي (غير شامل الضريبة):</span>
                    <span class="totals-value num-font">
                        {{ number_format((float) ($order->subtotal ?? ($order->total_amount - $order->tax_amount)), 2) }} ر.س
                    </span>
                </div>

                @if((float) ($order->discount_amount ?? 0) > 0)
                    <div class="totals-row" style="color: #dc2626;">
                        <span class="totals-label">قيمة الخصم (Discount):</span>
                        <span class="totals-value num-font">-{{ number_format((float) $order->discount_amount, 2) }} ر.س</span>
                    </div>
                @endif

                <div class="totals-row">
                    <span class="totals-label">ضريبة القيمة المضافة (15% VAT):</span>
                    <span class="totals-value num-font">
                        {{ number_format((float) ($order->tax_amount ?? 0), 2) }} ر.س
                    </span>
                </div>

                <div class="totals-row final">
                    <span class="totals-label">الإجمالي النهائي المستحق:</span>
                    <span class="totals-value num-font" style="font-size: 1.35rem;">
                        {{ number_format((float) $order->total_amount, 2) }} <span style="font-size: 0.95rem; font-weight: normal;">ر.س</span>
                    </span>
                </div>
            </div>

        </div>

        <!-- Footer Notes -->
        <footer class="invoice-footer">
            <div>
                تم إصدار هذه الفاتورة إلكترونياً من خلال نظام <strong>{{ $companyName }}</strong>.
            </div>
            <div>
                شكراً لتعاملكم معنا • Thank you for your business
            </div>
        </footer>

    </div>

</body>
</html>
