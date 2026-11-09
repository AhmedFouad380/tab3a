<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شاشة كشك الطباعة الفورية - {{ $kiosk->machine_code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        :root {
            --primary-blue: #005bb5;
            --primary-hover: #004b99;
            --primary-light: #e0f2fe;
            --accent-blue: #005bb5;
            --bg-canvas: #edf1f6; /* لون سكني مريح وأنيق */
            --card-white: #ffffff;
            --text-dark: #0f172a;
            --text-blue: #004085;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
            --border-soft: #e1ebf5;
            --success-green: #10b981;
            --success-bg: #ecfdf5;
            --success-text: #047857;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            user-select: none;
            font-family: 'Cairo', system-ui, -apple-system, sans-serif;
        }

        .num-font {
            font-family: 'Plus Jakarta Sans', 'Cairo', sans-serif;
        }

        body {
            background-color: var(--bg-canvas);
            color: var(--text-dark);
            min-height: 100vh;
            width: 100vw;
            overflow-x: hidden;
            overflow-y: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        /* Ambient Fluid Shapes in Background (Matching Screenshot - لون سكني) */
        .bg-decorative-wrap {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .bg-shape-left {
            position: absolute;
            top: 5%;
            left: -10vw;
            width: 36vw;
            height: 90%;
            background: radial-gradient(circle, rgba(195, 215, 238, 0.5) 0%, rgba(225, 236, 248, 0.2) 60%, transparent 80%);
            border-radius: 40% 60% 60% 40% / 40% 40% 60% 60%;
            filter: blur(20px);
        }

        .bg-shape-right {
            position: absolute;
            top: 5%;
            right: -10vw;
            width: 36vw;
            height: 90%;
            background: radial-gradient(circle, rgba(195, 215, 238, 0.5) 0%, rgba(225, 236, 248, 0.2) 60%, transparent 80%);
            border-radius: 60% 40% 40% 60% / 60% 60% 40% 40%;
            filter: blur(20px);
        }

        /* Subtle Stationery Book Watermark (Bottom Left) */
        .watermark-book {
            position: absolute;
            bottom: 40px;
            left: 30px;
            width: 140px;
            height: 140px;
            opacity: 0.12;
            pointer-events: none;
            color: #2563eb;
        }

        /* ================= Header Bar (Exact match to screenshot) ================= */
        .kiosk-header {
            position: relative;
            z-index: 10;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 44px 0;
        }

        /* Header Right: Titles & Machine Badge */
        .header-brand-wrap {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-logo-img {
            max-height: 38px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
            display: block;
        }

        .header-titles {
            display: flex;
            flex-direction: column;
            text-align: right;
            align-items: flex-start;
            gap: 2px;
        }

        .header-app-name {
            font-size: 1.25rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.2;
        }

        .header-branch-name {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .kiosk-code-pill {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            padding: 5px 18px;
            font-size: 0.86rem;
            font-weight: 800;
            box-shadow: 0 2px 6px rgba(30, 64, 175, 0.08);
        }

        /* Header Left: Status Badge & Digital Clock */
        .header-stats-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 0.84rem;
            font-weight: 700;
        }

        .status-badge.online {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.08);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.85); }
        }

        .header-clock-pill {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: 999px;
            padding: 6px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        .header-clock {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
        }

        /* ================= Main Stage & Card ================= */
        .main-stage {
            position: relative;
            z-index: 5;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px 44px;
        }

        /* The Clean Hero Floating White Card (Exact replica of screenshot) */
        .card-stage {
            width: 100%;
            max-width: 1040px;
            background: #ffffff;
            border-radius: 30px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 16px 45px rgba(15, 23, 42, 0.06);
            position: relative;
            padding: 34px 46px;
        }

        /* ================= Stage 1: Waiting Scan View (2-Column Screenshot Layout) ================= */
        .stage-waiting-grid {
            display: grid;
            grid-template-columns: 1.35fr 1fr;
            align-items: center;
            gap: 48px;
            width: 100%;
        }

        /* Column 1: Main Title & 5 Steps (Right side in RTL) */
        .waiting-text-col {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .waiting-title {
            font-size: 2.1rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.25;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .steps-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .step-card {
            display: flex;
            align-items: center;
            gap: 14px;
            background: #f1f7fd;
            border: 1px solid #e1ebf5;
            border-radius: 15px;
            padding: 10px 16px;
            transition: all 0.2s ease;
        }

        .step-card:hover {
            background: #eaf3fb;
            border-color: #cbdff2;
        }

        .step-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #dbeafe;
            color: #005bb5;
            font-weight: 800;
            font-size: 0.96rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .step-icon {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #005bb5;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .step-text {
            font-size: 0.98rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.45;
        }

        /* Column 2: Big QR Code Frame with Scanner Brackets (Left side in RTL) */
        .waiting-qr-col {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-scanner-frame {
            position: relative;
            width: 270px;
            height: 270px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 10px 32px rgba(0, 91, 181, 0.08);
            padding: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-scanner-frame svg {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }

        /* 4 Blue Corner Scanner Brackets */
        .corner {
            position: absolute;
            width: 36px;
            height: 36px;
            border-color: #005bb5;
            border-style: solid;
            pointer-events: none;
        }

        .corner-tl {
            top: -3px;
            left: -3px;
            border-width: 4px 0 0 4px;
            border-top-left-radius: 16px;
        }

        .corner-tr {
            top: -3px;
            right: -3px;
            border-width: 4px 4px 0 0;
            border-top-right-radius: 16px;
        }

        .corner-bl {
            bottom: -3px;
            left: -3px;
            border-width: 0 0 4px 4px;
            border-bottom-left-radius: 16px;
        }

        .corner-br {
            bottom: -3px;
            right: -3px;
            border-width: 0 4px 4px 0;
            border-bottom-right-radius: 16px;
        }

        /* Subtle animated scan beam across QR */
        .qr-scan-line {
            position: absolute;
            left: 12px;
            right: 12px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #0284c7, #38bdf8, transparent);
            box-shadow: 0 0 10px #0284c7;
            border-radius: 2px;
            animation: scan-move 3.2s ease-in-out infinite;
            pointer-events: none;
            opacity: 0.85;
        }

        @keyframes scan-move {
            0% { top: 16px; }
            50% { top: 250px; }
            100% { top: 16px; }
        }

        /* ================= Active Customer Stages (Connected, Uploaded, Printing, etc.) ================= */
        .center-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px 10px;
            width: 100%;
        }

        .state-big-icon {
            font-size: 58px;
            margin-bottom: 12px;
        }

        .state-title {
            font-size: 1.9rem;
            font-weight: 800;
            color: var(--primary-blue);
            margin-bottom: 8px;
        }

        .state-subtitle {
            font-size: 1.1rem;
            color: var(--text-body);
            max-width: 600px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* Info Grid for Uploaded Document */
        .doc-info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            width: 100%;
            max-width: 780px;
            margin: 16px 0 24px;
        }

        .payment-order-summary-box {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin: 12px 0 18px;
            max-width: 680px;
        }

        .summary-pill {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 9999px;
            padding: 6px 16px;
            font-size: 0.92rem;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .summary-pill i {
            color: var(--primary-blue);
        }

        .summary-pill.pill-color {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }
        .summary-pill.pill-color i {
            color: #ef4444;
        }

        .doc-info-card {
            background: #f0f7ff;
            border: 1px solid #bae6fd;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
        }

        .doc-info-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 4px;
        }

        .doc-info-value {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary-blue);
        }

        .doc-price-pill {
            background: linear-gradient(135deg, #005bb5, #0284c7);
            color: #ffffff;
            border-radius: 14px;
            padding: 12px 36px;
            font-size: 1.5rem;
            font-weight: 800;
            box-shadow: 0 6px 20px rgba(0, 91, 181, 0.25);
            display: inline-block;
        }

        /* Progress Bar for Printing */
        .progress-bar-container {
            width: 100%;
            max-width: 620px;
            height: 16px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
            margin: 20px 0 10px;
            position: relative;
        }

        .progress-bar-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #0284c7, #005bb5);
            border-radius: 999px;
            transition: width 0.3s ease;
        }

        .btn-continue {
            background: linear-gradient(135deg, #005bb5, #0284c7);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            padding: 14px 32px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 91, 181, 0.25);
            transition: all 0.2s;
        }

        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 91, 181, 0.4);
        }

        /* ================= Footer Dock Bar (Exact Screenshot Match) ================= */
        .kiosk-footer {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1040px;
            margin: 0 auto 18px;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 22px;
            padding: 14px 34px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-help {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .footer-help-icon {
            font-size: 28px;
            color: #005bb5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer-help-text {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .footer-help-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
        }

        .footer-help-phone {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .footer-download {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .footer-download-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
        }

        .footer-download-qr {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid var(--border-light);
            padding: 3px;
        }

        .footer-download-qr svg {
            width: 100% !important;
            height: 100% !important;
        }

        /* ================= Inactivity Modal ================= */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 36px 40px;
            max-width: 520px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-light);
        }

        .modal-countdown {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 1.15rem;
            font-weight: 800;
            margin: 20px 0;
            display: inline-block;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 10px;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 12px 24px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
        }

        @media (max-width: 992px) {
            .stage-waiting-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .waiting-qr-col {
                order: 2;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient Fluid Shapes in Background (Matching Screenshot - لون سكني) -->
    <div class="bg-decorative-wrap">
        <div class="bg-shape-left"></div>
        <div class="bg-shape-right"></div>
        
        <!-- Open Book Watermark Outline in Bottom Left -->
        <svg class="watermark-book" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            <path d="M6 6h10"/>
            <path d="M6 10h10"/>
        </svg>
    </div>

    <!-- Header Bar (Exact match to screenshot top-bar) -->
    <header class="kiosk-header">
        <!-- Right side in RTL: Machine Badge & Titles -->
        <div class="header-brand-wrap">
            <div class="header-titles">
                <div class="header-app-name">
                    @if(!empty($appLogoUrl) || file_exists(public_path('logo.jpeg')))
                        <img src="{{ $appLogoUrl ?? asset('logo.jpeg') }}" alt="الشعار" class="header-logo-img">
                    @else
                        <span>الطباعة الذاتية</span>
                    @endif
                </div>
                <div class="header-branch-name">
                    <span>{{ $kiosk->branch?->name['ar'] ?? 'الفرع الرئيسي-حي الضاحي' }}</span>
                    <span>📍</span>
                </div>
            </div>
            <div class="kiosk-code-pill num-font">{{ $kiosk->machine_code }}</div>
        </div>

        <!-- Left side in RTL: Status Badge & Digital Clock (Clock on far left, status next to it) -->
        <div class="header-stats-wrap">
            <div class="status-badge online">
                <div class="status-dot"></div>
                <span id="machine-status-text">متصلة وجاهزة</span>
            </div>

            <div class="header-clock-pill">
                <i class="fa-regular fa-clock"></i>
                <span class="header-clock num-font" id="digital-clock">--:--</span>
            </div>
        </div>
    </header>

    <!-- Main Stage -->
    <main class="main-stage">
        <div class="card-stage" id="screen-content">

            <!-- ================= Stage 1: Waiting Scan View (Screenshot Matching) ================= -->
            <div class="stage-waiting-grid" id="view-waiting">
                
                <!-- Column 1: Main Title and 4 Steps (Right in RTL) -->
                <div class="waiting-text-col">
                    <h1 class="waiting-title">خدمة الطباعة الذاتية</h1>

                    <div class="steps-list">
                        <!-- Step 1 -->
                        <div class="step-card">
                            <div class="step-badge num-font">1</div>
                            <div class="step-icon">
                                <i class="fa-solid fa-mobile-screen-button"></i>
                            </div>
                            <div class="step-text">
                                افتح تطبيق قرطاسية منصور واختر «الطباعة الذاتية»
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="step-card">
                            <div class="step-badge num-font">2</div>
                            <div class="step-icon">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <div class="step-text">
                                امسح رمز QR لربط طلبك بالجهاز
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="step-card">
                            <div class="step-badge num-font">3</div>
                            <div class="step-icon">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="step-text">
                                ارفع الملف المراد طباعته
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="step-card">
                            <div class="step-badge num-font">4</div>
                            <div class="step-icon">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div class="step-text">
                                حدد خيارات الطباعة وعدد النسخ
                            </div>
                        </div>

                        <!-- Step 5 -->
                        <div class="step-card">
                            <div class="step-badge num-font">5</div>
                            <div class="step-icon">
                                <i class="fa-regular fa-credit-card"></i>
                            </div>
                            <div class="step-text">
                                أكمل الدفع واستلم مطبوعاتك
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: The Big QR Code Frame with Scanner Brackets (Left in RTL) -->
                <div class="waiting-qr-col">
                    <div class="qr-scanner-frame">
                        <div class="corner corner-tl"></div>
                        <div class="corner corner-tr"></div>
                        <div class="corner corner-bl"></div>
                        <div class="corner corner-br"></div>
                        <div class="qr-scan-line"></div>
                        <div id="qr-container" style="width: 100%; height: 100%;">
                            {!! $qrSvg !!}
                        </div>
                    </div>
                </div>

            </div>

            <!-- ================= Stage 2: Connected State ================= -->
            <div class="center-state" id="view-connected" style="display: none;">
                <div class="state-big-icon" style="color: #005bb5;">
                    <i class="fa-solid fa-link"></i>
                </div>
                <h2 class="state-title" style="color: #005bb5;">تم ربط هاتفك بنجاح!</h2>
                <p class="state-subtitle">
                    يرجى الآن اختيار المستند وتحديد إعدادات الطباعة من شاشة جوالك.
                </p>
                <div style="font-size: 0.95rem; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
                    <div class="status-dot" style="background-color: #005bb5; box-shadow: 0 0 0 3px rgba(0, 91, 181, 0.2);"></div> بانتظار رفع الملف من هاتفك...
                </div>
            </div>

            <!-- ================= Stage 3: File Uploaded State ================= -->
            <div class="center-state" id="view-uploaded" style="display: none;">
                <div class="state-big-icon" style="color: #0284c7;">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>
                <h2 class="state-title">تم استلام المستند بنجاح</h2>
                <p class="state-subtitle num-font" id="display-filename" style="color: #005bb5; font-weight: 700;">
                    document.pdf
                </p>

                <div class="doc-info-grid">
                    <div class="doc-info-card">
                        <div class="doc-info-label">عدد الصفحات</div>
                        <div class="doc-info-value num-font" id="info-pages">0</div>
                    </div>
                    <div class="doc-info-card">
                        <div class="doc-info-label">عدد النسخ</div>
                        <div class="doc-info-value num-font" id="info-copies">1</div>
                    </div>
                    <div class="doc-info-card">
                        <div class="doc-info-label">إجمالي الأوراق (A4)</div>
                        <div class="doc-info-value num-font" id="info-sheets">0</div>
                    </div>
                    <div class="doc-info-card">
                        <div class="doc-info-label">نوع الطباعة</div>
                        <div class="doc-info-value" id="info-mode">أبيض وأسود</div>
                    </div>
                </div>

                <div class="doc-price-pill num-font" id="display-price">
                    0.00 ر.س
                </div>
            </div>

            <!-- ================= Stage 4: Awaiting Payment ================= -->
            <div class="center-state" id="view-payment" style="display: none;">
                <div class="state-big-icon" style="color: #005bb5;">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <h2 class="state-title" style="color: #005bb5;">برجاء إتمام الدفع عبر الجوال</h2>
                <p class="state-subtitle" style="margin-bottom: 8px;">
                    إجمالي المبلغ المطلوب: <strong id="pay-total-amount" class="num-font" style="color: #005bb5; font-size: 1.4rem;">0.00 ر.س</strong>
                </p>

                <div class="payment-order-summary-box">
                    <div class="summary-pill">
                        <i class="fa-solid fa-copy"></i>
                        <span>النسخ: <strong id="pay-copies" class="num-font">1</strong></span>
                    </div>
                    <div class="summary-pill">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>الصفحات: <strong id="pay-pages" class="num-font">1</strong></span>
                    </div>
                    <div class="summary-pill">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>الأوراق: <strong id="pay-sheets" class="num-font">1</strong></span>
                    </div>
                    <div class="summary-pill" id="pay-mode-pill">
                        <i class="fa-solid fa-palette"></i>
                        <span id="pay-mode">أبيض وأسود</span>
                    </div>
                </div>
                <div style="display: flex; gap: 16px; font-size: 26px; color: #475569; margin: 10px 0;">
                    <span><i class="fa-brands fa-apple-pay"></i></span>
                    <span><i class="fa-solid fa-credit-card"></i> مدى</span>
                    <span><i class="fa-brands fa-cc-visa"></i></span>
                </div>
                <div style="font-size: 0.9rem; color: var(--text-muted); margin-top: 10px;">
                    بمجرد اكتمال الدفع ستبدأ الطابعة بسحب الأوراق فوراً...
                </div>
            </div>

            <!-- ================= Stage 5: Printing ================= -->
            <div class="center-state" id="view-printing" style="display: none;">
                <div class="state-big-icon" style="color: #005bb5; animation: bounce-icon 2s infinite;">
                    <i class="fa-solid fa-print"></i>
                </div>
                <h2 class="state-title">جاري طباعة مستندك الآن...</h2>
                <p class="state-subtitle">
                    فضلاً انتظر، لا تغادر الكشك حتى خروج جميع الأوراق من مخرج الطابعة.
                </p>
                
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" id="print-progress"></div>
                </div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #005bb5; margin-top: 8px;" class="num-font" id="print-percent">
                    0%
                </div>
                <div id="print-status-note" style="font-size: 0.9rem; color: #0284c7; margin-top: 6px;">
                    ⚡ تم إرسال أمر الطباعة التلقائي إلى الطابعة المتصلة...
                </div>
            </div>

            <!-- Hidden Off-screen Iframe for Direct Hardware Printing -->
            <iframe id="kiosk-print-frame" style="position: fixed; left: -9999px; top: -9999px; width: 1200px; height: 1200px; border: 0; visibility: hidden;"></iframe>

            <!-- ================= Stage 6: Completed ================= -->
            <div class="center-state" id="view-completed" style="display: none;">
                <div class="state-big-icon" style="color: #10b981;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h2 class="state-title" style="color: #059669;">تمت الطباعة بنجاح!</h2>
                <p class="state-subtitle" style="font-weight: 700;">
                    فضلاً استلم أوراقك من مخرج الطابعة بالأسفل.
                </p>
                <div style="font-size: 0.88rem; color: var(--text-muted); margin: 10px 0 20px;">
                    🛡️ حفاظاً على خصوصيتك، تم حذف الملف تلقائياً من خوادمنا.
                </div>
                <button type="button" id="btn-return-home" class="btn-continue" onclick="handleReturnHome()">
                    <i class="fa-solid fa-house" style="margin-left: 8px;"></i>
                    العودة للصفحة الرئيسية
                </button>
            </div>

        </div>
    </main>

    <!-- Footer Dock Bar (Matching Screenshot) -->
    <footer class="kiosk-footer">
        @php
            $displayPhone = !empty(trim($supportPhone ?? '')) 
                ? $supportPhone 
                : (!empty(trim($kiosk->branch?->phone ?? '')) 
                    ? $kiosk->branch->phone 
                    : '+96650000000');
        @endphp

        <!-- Left Side (RTL): App Download -->
        <div class="footer-download">
            <div class="footer-download-qr">
                @if(!empty($appQrSvg))
                    {!! $appQrSvg !!}
                @else
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#005bb5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                @endif
            </div>
            <div class="footer-download-title">لتحميل التطبيق</div>
        </div>

        <!-- Right Side (RTL): Customer Support -->
        <div class="footer-help">
            <div class="footer-help-text">
                <div class="footer-help-title">تحتاج مساعدة؟</div>
                <div class="footer-help-phone">
                    <span>التواصل مع خدمة العملاء:</span>
                    <span class="num-font" dir="ltr">{{ $displayPhone }}</span>
                </div>
            </div>
            <div class="footer-help-icon">
                <i class="fa-solid fa-headset"></i>
            </div>
        </div>
    </footer>

    <!-- Inactivity Timeout Modal -->
    <div id="inactivity-modal" class="modal-overlay">
        <div class="modal-card">
            <div style="font-size: 42px; color: #f59e0b; margin-bottom: 12px;">⏳</div>
            <h3 style="font-size: 1.4rem; font-weight: 800; color: #004085; margin-bottom: 8px;">هل تريد المزيد من الوقت؟</h3>
            <p style="font-size: 0.95rem; color: var(--text-body); line-height: 1.6;">
                لوحظ عدم وجود نشاط على الشاشة أو هاتفك. للحفاظ على خصوصية بياناتك وإتاحة الكشك للعميل التالي، ستنتهي الجلسة تلقائياً.
            </p>
            <div class="modal-countdown num-font">
                ⏳ متبقي <span id="inactivity-countdown">10</span> ثوانٍ للإلغاء
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-continue" onclick="handleKeepAlive()">
                    نعم، أحتاج وقتاً إضافياً
                </button>
                <button type="button" class="btn-cancel" onclick="handleTimeoutCancel()">
                    إنهاء والعودة للرئيسية
                </button>
            </div>
        </div>
    </div>

    <!-- PDF Manipulation Library for dynamic multi-copy print jobs -->
    <script src="{{ asset('js/pdf-lib.min.js') }}"></script>

    <!-- Interactive Script Logic -->
    <script>
        const machineCode = "{{ $kiosk->machine_code }}";
        let currentStatus = null;
        let currentSessionToken = "{{ $session->session_token }}";
        let pollInterval = null;
        let printAnimationInterval = null;

        // Inactivity Watcher (60s Idle Limit)
        let idleSeconds = 0;
        let isTimeoutModalOpen = false;
        let modalCountdownInterval = null;

        window.addEventListener('click', () => {
            if (!isTimeoutModalOpen) idleSeconds = 0;
        });
        window.addEventListener('touchstart', () => {
            if (!isTimeoutModalOpen) idleSeconds = 0;
        });

        setInterval(() => {
            if (['connected', 'file_uploaded', 'awaiting_payment'].includes(currentStatus)) {
                idleSeconds++;
                if (idleSeconds >= 60 && !isTimeoutModalOpen) {
                    showInactivityModal();
                }
            } else {
                idleSeconds = 0;
                if (isTimeoutModalOpen) hideInactivityModal();
            }
        }, 1000);

        function showInactivityModal() {
            isTimeoutModalOpen = true;
            const modal = document.getElementById('inactivity-modal');
            const countdownEl = document.getElementById('inactivity-countdown');
            modal.style.display = 'flex';

            let remainingSeconds = 10;
            countdownEl.innerText = remainingSeconds;

            clearInterval(modalCountdownInterval);
            modalCountdownInterval = setInterval(() => {
                remainingSeconds--;
                countdownEl.innerText = remainingSeconds;
                if (remainingSeconds <= 0) {
                    clearInterval(modalCountdownInterval);
                    handleTimeoutCancel();
                }
            }, 1000);
        }

        function hideInactivityModal() {
            isTimeoutModalOpen = false;
            clearInterval(modalCountdownInterval);
            document.getElementById('inactivity-modal').style.display = 'none';
        }

        function handleKeepAlive() {
            hideInactivityModal();
            idleSeconds = 0;

            fetch('/api/v1/kiosks/extend-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ session_token: currentSessionToken })
            }).catch(err => console.log('Extend session ping error', err));
        }

        function handleTimeoutCancel() {
            hideInactivityModal();
            idleSeconds = 0;

            fetch(`/kiosk/complete-reset/${currentSessionToken}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: 'cancelled' })
            })
            .then(res => res.json())
            .then(() => {
                fetchStatus();
            })
            .catch(() => {
                fetchStatus();
            });
        }

        // Digital Clock (HH:mm)
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('digital-clock').innerText = `${hours}:${minutes}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Switch Screen State smoothly
        function renderState(status, data) {
            const statusChanged = (status !== currentStatus);
            currentStatus = status;

            if (statusChanged) {
                idleSeconds = 0;
                if (isTimeoutModalOpen) hideInactivityModal();

                document.getElementById('view-waiting').style.display = 'none';
                document.getElementById('view-connected').style.display = 'none';
                document.getElementById('view-uploaded').style.display = 'none';
                document.getElementById('view-payment').style.display = 'none';
                document.getElementById('view-printing').style.display = 'none';
                document.getElementById('view-completed').style.display = 'none';

                if (status === 'waiting_scan') {
                    document.getElementById('view-waiting').style.display = 'grid';
                } else if (status === 'connected') {
                    document.getElementById('view-connected').style.display = 'flex';
                } else if (status === 'file_uploaded') {
                    document.getElementById('view-uploaded').style.display = 'flex';
                } else if (status === 'awaiting_payment') {
                    document.getElementById('view-payment').style.display = 'flex';
                } else if (status === 'printing') {
                    document.getElementById('view-printing').style.display = 'flex';
                    const fileUrl = (data && data.session) ? data.session.file_url : null;
                    const colorMode = (data && data.session) ? data.session.color_mode : 'black_and_white';
                    const copies = (data && data.session && data.session.copies_count) ? parseInt(data.session.copies_count) : 1;
                    startPrintSimulation(data && data.session ? data.session.token : currentSessionToken, copies);
                    triggerBrowserPrint(fileUrl, colorMode, copies);
                } else if (status === 'completed') {
                    document.getElementById('view-completed').style.display = 'flex';
                } else if (status === 'cancelled' || status === 'expired') {
                    document.getElementById('view-waiting').style.display = 'grid';
                }
            }

            if (status === 'waiting_scan' || status === 'cancelled' || status === 'expired') {
                hasTriggeredPrintForToken = null;
            }

            if (status === 'waiting_scan' && data && data.qr_svg) {
                const qrContainer = document.getElementById('qr-container');
                if (qrContainer) qrContainer.innerHTML = data.qr_svg;
            } else if (status === 'file_uploaded' && data && data.session) {
                const sess = data.session;
                const filenameEl = document.getElementById('display-filename');
                const pagesEl = document.getElementById('info-pages');
                const copiesEl = document.getElementById('info-copies');
                const sheetsEl = document.getElementById('info-sheets');
                const modeEl = document.getElementById('info-mode');
                const priceEl = document.getElementById('display-price');

                if (filenameEl) filenameEl.innerText = sess.file_name || 'document.pdf';
                if (pagesEl) pagesEl.innerText = sess.detected_page_count || 1;
                if (copiesEl) copiesEl.innerText = sess.copies_count || 1;
                if (sheetsEl) sheetsEl.innerText = sess.total_sheets || 1;
                if (modeEl) {
                    modeEl.innerText = (sess.color_mode === 'color' ? 'ملون' : 'أبيض وأسود') + 
                        (sess.side_mode === 'double_sided' ? ' (وجهين)' : ' (وجه واحد)');
                }
                if (priceEl) priceEl.innerText = Number(sess.total_price || 0).toFixed(2) + ' ر.س';
            } else if (status === 'awaiting_payment' && data && data.session) {
                const sess = data.session;
                const totalEl = document.getElementById('pay-total-amount');
                const copiesEl = document.getElementById('pay-copies');
                const pagesEl = document.getElementById('pay-pages');
                const sheetsEl = document.getElementById('pay-sheets');
                const modeEl = document.getElementById('pay-mode');

                if (totalEl) totalEl.innerText = Number(sess.total_price || 0).toFixed(2) + ' ر.س';
                if (copiesEl) copiesEl.innerText = sess.copies_count || 1;
                if (pagesEl) pagesEl.innerText = sess.detected_page_count || 1;
                if (sheetsEl) sheetsEl.innerText = sess.total_sheets || 1;
                if (modeEl) {
                    modeEl.innerText = (sess.color_mode === 'color' ? 'ملون' : 'أبيض وأسود') + 
                        (sess.side_mode === 'double_sided' ? ' (وجهين)' : ' (وجه واحد)');
                }
            }
        }

        let hasTriggeredPrintForToken = null;

        async function triggerBrowserPrint(fileUrl, colorMode, copies = 1) {
            if (!fileUrl) {
                console.warn('Physical print: no file_url provided');
                return;
            }

            if (hasTriggeredPrintForToken === currentSessionToken) return;
            hasTriggeredPrintForToken = currentSessionToken;

            const totalCopies = Math.max(1, parseInt(copies) || 1);
            console.log('Sending direct print command to kiosk printer for:', fileUrl, 'colorMode:', colorMode, 'copies:', totalCopies);

            let printUrl = fileUrl;
            let isBlobUrl = false;
            const isPdf = fileUrl.toLowerCase().split('?')[0].endsWith('.pdf');
            const isImage = /\.(jpe?g|png|webp|gif)($|\?)/i.test(fileUrl);

            // 1. For PDFs: If copies > 1, duplicate pages in the PDF document itself using PDFLib!
            // This ensures 1 single reliable print job with ALL copies collated, bypassing Chrome's single-copy lock.
            if (isPdf && totalCopies > 1 && window.PDFLib) {
                try {
                    const existingPdfBytes = await fetch(fileUrl).then(r => r.arrayBuffer());
                    const originalDoc = await PDFLib.PDFDocument.load(existingPdfBytes);
                    const mergedDoc = await PDFLib.PDFDocument.create();

                    for (let c = 0; c < totalCopies; c++) {
                        const copiedPages = await mergedDoc.copyPages(originalDoc, originalDoc.getPageIndices());
                        copiedPages.forEach(page => mergedDoc.addPage(page));
                    }

                    const mergedBytes = await mergedDoc.save();
                    const blob = new Blob([mergedBytes], { type: 'application/pdf' });
                    printUrl = URL.createObjectURL(blob);
                    isBlobUrl = true;
                    console.log('Successfully generated multi-copy PDF binary with', totalCopies, 'copies');
                } catch (err) {
                    console.error('Failed to duplicate PDF pages via PDFLib, falling back to original URL:', err);
                }
            }

            let frame = document.getElementById('kiosk-print-frame');
            if (frame) {
                frame.remove();
            }

            frame = document.createElement('iframe');
            frame.id = 'kiosk-print-frame';
            // CRITICAL: Dimensions must be non-zero off-screen so Chrome renders PDF plugin and focuses correctly
            frame.style.position = 'fixed';
            frame.style.left = '-9999px';
            frame.style.top = '-9999px';
            frame.style.width = '1200px';
            frame.style.height = '1200px';
            frame.style.border = '0';
            frame.style.visibility = 'hidden';
            document.body.appendChild(frame);

            if (isImage) {
                // If it's an image, create multi-page document inside iframe using clean DOM manipulation
                const doc = frame.contentDocument || frame.contentWindow.document;
                doc.open();
                doc.close();

                const style = doc.createElement('style');
                style.textContent = '@page { size: auto; margin: 0; } body { margin: 0; padding: 0; background: #fff; }';
                doc.head.appendChild(style);

                for (let i = 0; i < totalCopies; i++) {
                    const wrap = doc.createElement('div');
                    wrap.style.cssText = 'page-break-after: always; width: 100vw; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 0;';
                    const img = doc.createElement('img');
                    img.src = fileUrl;
                    img.style.cssText = 'max-width: 100%; max-height: 100%; object-fit: contain;' + (colorMode === 'black_and_white' ? 'filter: grayscale(100%); -webkit-filter: grayscale(100%);' : '');
                    wrap.appendChild(img);
                    doc.body.appendChild(wrap);
                }

                setTimeout(() => {
                    try {
                        frame.contentWindow.focus();
                        frame.contentWindow.print();
                    } catch (e) {
                        console.error('Image frame print error:', e);
                    }
                }, 600);
            } else {
                // PDF Document (Single or Multi-Copy via PDFLib)
                frame.src = printUrl;

                frame.onload = function() {
                    // Give Chrome PDF plugin 800ms to paint before triggering print
                    setTimeout(() => {
                        try {
                            frame.contentWindow.focus();
                            frame.contentWindow.print();
                        } catch (e) {
                            console.error('PDF frame print error:', e);
                        }

                        if (isBlobUrl) {
                            setTimeout(() => URL.revokeObjectURL(printUrl), 10000);
                        }
                    }, 800);
                };
            }
        }

        function startPrintSimulation(token, copies = 1) {
            clearInterval(printAnimationInterval);
            let percent = 0;
            const bar = document.getElementById('print-progress');
            const txt = document.getElementById('print-percent');
            const note = document.getElementById('print-status-note');

            const totalCopies = Math.max(1, parseInt(copies) || 1);
            bar.style.width = '0%';
            txt.innerText = '0%';
            note.innerText = '⚡ جاري استقبال أمر الطباعة (' + (totalCopies > 1 ? totalCopies + ' نسخ' : 'نسخة واحدة') + ') وسحب الأوراق من الطابعة...';

            printAnimationInterval = setInterval(() => {
                percent += 5;
                if (percent > 100) percent = 100;

                bar.style.width = `${percent}%`;
                txt.innerText = `${percent}%`;

                if (percent === 30) {
                    note.innerText = '🖨️ الطابعة تقوم بمعالجة الصفحات وسحب الورق...';
                } else if (percent === 70) {
                    note.innerText = '📄 جاري إخراج الأوراق المطبوعة...';
                }

                if (percent >= 100) {
                    clearInterval(printAnimationInterval);
                    setTimeout(() => {
                        finishPrintingAndReset(token);
                    }, 1200);
                }
            }, 300);
        }

        function finishPrintingAndReset(token) {
            fetch(`/kiosk/complete-reset/${token}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: 'completed' })
            })
            .then(res => res.json())
            .then(data => {
                renderState('completed', data);
                setTimeout(() => {
                    handleReturnHome();
                }, 8000);
            })
            .catch(err => {
                console.error('Complete reset error', err);
                renderState('completed');
                setTimeout(() => {
                    handleReturnHome();
                }, 8000);
            });
        }

        function handleReturnHome() {
            renderState('waiting_scan', {
                session: null,
                qr_svg: null
            });
            fetchStatus();
        }

        async function fetchStatus() {
            try {
                const res = await fetch(`/api/kiosks/${machineCode}/screen-status`);
                if (!res.ok) return;
                const data = await res.json();

                if (data.kiosk) {
                    const statusDot = document.querySelector('.status-dot');
                    const statusText = document.getElementById('machine-status-text');
                    if (data.kiosk.status === 'online') {
                        statusText.innerText = 'متصلة وجاهزة';
                    } else if (data.kiosk.status === 'busy') {
                        statusText.innerText = 'الماكينة قيد الاستخدام';
                    } else {
                        statusText.innerText = 'خارج الخدمة';
                    }

                    const paperEl = document.getElementById('paper-count');
                    if (paperEl && data.kiosk.paper_tray_a4_sheets !== undefined) {
                        paperEl.innerText = data.kiosk.paper_tray_a4_sheets;
                    }
                    const tonerEl = document.getElementById('toner-count');
                    if (tonerEl && data.kiosk.black_toner_level !== undefined) {
                        tonerEl.innerText = `${data.kiosk.black_toner_level}%`;
                    }
                }

                if (data.session) {
                    currentSessionToken = data.session.token;
                    renderState(data.session.status, data);
                } else {
                    renderState('waiting_scan', data);
                }
            } catch (err) {
                console.warn('Polling error:', err);
            }
        }

        pollInterval = setInterval(fetchStatus, 2000);
        fetchStatus();
    </script>
</body>
</html>
