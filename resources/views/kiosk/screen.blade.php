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
            --accent-blue: #0284c7;
            --bg-canvas: #edf3f9;
            --card-white: #ffffff;
            --text-dark: #0f172a;
            --text-blue: #004085;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
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

        /* Ambient Geometric Blue Ribbons in Background (Matching Screenshot) */
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

        .bg-ribbon-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 45vw;
            height: 100%;
            background: linear-gradient(135deg, rgba(186, 215, 248, 0.45) 0%, rgba(219, 234, 254, 0.15) 60%, transparent 100%);
            clip-path: polygon(0 0, 75% 0, 25% 100%, 0 100%);
        }

        .bg-ribbon-left-accent {
            position: absolute;
            top: 0;
            left: 6%;
            width: 32vw;
            height: 100%;
            background: linear-gradient(135deg, rgba(147, 197, 253, 0.3) 0%, rgba(219, 234, 254, 0.05) 50%, transparent 100%);
            clip-path: polygon(0 0, 85% 0, 20% 100%, 0 100%);
        }

        .bg-ribbon-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 45vw;
            height: 100%;
            background: linear-gradient(225deg, rgba(186, 215, 248, 0.45) 0%, rgba(219, 234, 254, 0.15) 60%, transparent 100%);
            clip-path: polygon(25% 0, 100% 0, 100% 100%, 75% 100%);
        }

        .bg-ribbon-right-accent {
            position: absolute;
            top: 0;
            right: 6%;
            width: 32vw;
            height: 100%;
            background: linear-gradient(225deg, rgba(147, 197, 253, 0.3) 0%, rgba(219, 234, 254, 0.05) 50%, transparent 100%);
            clip-path: polygon(15% 0, 100% 0, 100% 100%, 80% 100%);
        }

        /* Subtle Stationery Book Watermark (Bottom Left) */
        .watermark-book {
            position: absolute;
            bottom: 40px;
            left: 30px;
            width: 140px;
            height: 140px;
            opacity: 0.18;
            pointer-events: none;
            color: #2563eb;
        }

        /* ================= Header Bar ================= */
        .kiosk-header {
            position: relative;
            z-index: 10;
            height: 64px;
            background: #ffffff;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            box-shadow: 0 2px 10px rgba(0, 50, 120, 0.04);
        }

        /* Header Right: Brand + Machine Badge */
        .header-brand-wrap {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-app-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #005bb5, #0284c7);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(0, 91, 181, 0.25);
        }

        .header-titles {
            display: flex;
            flex-direction: column;
        }

        .header-app-name {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--primary-blue);
            line-height: 1.2;
        }

        .header-branch-name {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .kiosk-code-pill {
            background: #005bb5;
            color: #ffffff;
            border-radius: 999px;
            padding: 5px 16px;
            font-size: 0.82rem;
            font-weight: 800;
            box-shadow: 0 2px 8px rgba(0, 91, 181, 0.3);
        }

        /* Header Left: Status, Paper, Ink & Clock */
        .header-stats-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .status-badge.online {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
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

        .stat-badge-blue {
            background: #f0f7ff;
            color: #0284c7;
            border: 1px solid #dbeafe;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .stat-badge-slate {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .header-clock {
            font-size: 1.05rem;
            font-weight: 800;
            color: #004085;
            padding-right: 6px;
        }

        /* ================= Main Stage & Card ================= */
        .main-stage {
            position: relative;
            z-index: 5;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px 24px;
        }

        /* The Hero Floating White Card (Exact replica of the screenshot) */
        .card-stage {
            width: 92%;
            max-width: 1040px;
            background: var(--card-white);
            border-radius: 28px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 16px 45px rgba(2, 60, 130, 0.12);
            position: relative;
            overflow: hidden;
            padding: 38px 46px;
        }

        /* Geometric corner accents on the main card */
        .card-corner-topleft {
            position: absolute;
            top: 0;
            left: 0;
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #005bb5 50%, transparent 50%);
            border-top-left-radius: 26px;
        }

        .card-corner-bottomright {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 44px;
            height: 44px;
            background: linear-gradient(315deg, #005bb5 50%, transparent 50%);
            border-bottom-right-radius: 26px;
        }

        .card-corner-bottomleft {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 32px;
            height: 32px;
            background: linear-gradient(45deg, #0284c7 50%, transparent 50%);
            border-bottom-left-radius: 26px;
        }

        .card-corner-topright {
            position: absolute;
            top: 0;
            right: 0;
            width: 32px;
            height: 32px;
            background: linear-gradient(225deg, #005bb5 50%, transparent 50%);
            border-top-right-radius: 26px;
        }

        /* ================= Stage 1: Waiting Scan View (Screenshot Layout) ================= */
        .stage-waiting-grid {
            display: grid;
            grid-template-columns: 1.35fr auto 1fr;
            align-items: center;
            gap: 28px;
            width: 100%;
        }

        /* Column 1: Info & Features (Right side in RTL) */
        .waiting-text-col {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .waiting-title {
            font-size: 1.85rem;
            font-weight: 900;
            color: var(--primary-blue);
            line-height: 1.25;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .waiting-subtitle {
            font-size: 0.95rem;
            color: var(--text-body);
            line-height: 1.6;
            margin-bottom: 22px;
            font-weight: 600;
        }

        .features-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.94rem;
            font-weight: 700;
            color: #1e293b;
        }

        .feature-icon-box {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: #e0f2fe;
            color: #0284c7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            border: 1px solid #bae6fd;
        }

        /* Column 2: Logo & Machine Code Badge (Center) */
        .waiting-middle-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
            padding: 0 10px;
        }

        /* Partner Brand Logo (قرطاسية منصور - Mansour Stationery / Platform Logo) */
        .partner-logo-box {
            display: flex;
            align-items: center;
            gap: 10px;
            direction: ltr;
        }

        .partner-icon-diamond {
            width: 44px;
            height: 44px;
            background: #ffffff;
            border: 2.5px solid #005bb5;
            border-radius: 10px;
            transform: rotate(45deg);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 91, 181, 0.15);
            flex-shrink: 0;
        }

        .partner-icon-inner {
            transform: rotate(-45deg);
            color: #005bb5;
            font-size: 19px;
        }

        .partner-text-en {
            text-align: right;
            font-family: 'Cairo', sans-serif;
            color: #005bb5;
        }

        .partner-text-en .brand-ar {
            font-size: 1.15rem;
            font-weight: 900;
            line-height: 1.1;
        }

        .partner-text-en .brand-en {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }

        .partner-text-ar {
            text-align: left;
            font-family: 'Cairo', sans-serif;
            color: #005bb5;
        }

        .partner-text-ar .brand-ar {
            font-size: 1.15rem;
            font-weight: 900;
            line-height: 1.1;
        }

        .partner-text-ar .brand-en {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }

        /* Machine Code Sub-Card */
        .machine-code-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .machine-code-label {
            font-size: 0.88rem;
            font-weight: 800;
            color: var(--primary-blue);
            line-height: 1.2;
            text-align: center;
        }

        .machine-code-box {
            background: #e0f2fe;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 8px 16px;
            text-align: center;
            color: #005bb5;
            font-weight: 800;
            font-size: 1.15rem;
            line-height: 1.15;
            box-shadow: inset 0 2px 4px rgba(0, 91, 181, 0.05);
        }

        /* Column 3: Big QR Code (Left side in RTL) */
        .waiting-qr-col {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-frame-outer {
            width: 250px;
            height: 250px;
            background: #ffffff;
            border-radius: 22px;
            border: 2px solid #005bb5;
            box-shadow: 0 10px 30px rgba(0, 91, 181, 0.12);
            padding: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .qr-frame-outer svg {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }

        /* Subtle animated scan beam across QR */
        .qr-scan-line {
            position: absolute;
            left: 8px;
            right: 8px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #0284c7, #38bdf8, transparent);
            box-shadow: 0 0 10px #0284c7;
            border-radius: 2px;
            animation: scan-move 3.2s ease-in-out infinite;
            pointer-events: none;
            opacity: 0.85;
        }

        @keyframes scan-move {
            0% { top: 12px; }
            50% { top: 230px; }
            100% { top: 12px; }
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
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            width: 100%;
            max-width: 680px;
            margin: 16px 0 24px;
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

        /* ================= Footer Bar ================= */
        .kiosk-footer {
            position: relative;
            z-index: 10;
            height: 48px;
            background: var(--primary-blue);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            font-size: 0.88rem;
            font-weight: 600;
            box-shadow: 0 -2px 10px rgba(0, 50, 120, 0.1);
        }

        .kiosk-footer a {
            color: #ffffff;
            text-decoration: none;
        }

        .footer-support {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-note {
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0.95;
            font-size: 0.84rem;
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
            .features-list {
                align-items: center;
            }
            .waiting-middle-col {
                order: 3;
            }
            .waiting-qr-col {
                order: 2;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient Geometric Ribbon Overlays & Watermark (Matching Screenshot) -->
    <div class="bg-decorative-wrap">
        <div class="bg-ribbon-left"></div>
        <div class="bg-ribbon-left-accent"></div>
        <div class="bg-ribbon-right"></div>
        <div class="bg-ribbon-right-accent"></div>
        
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
        <!-- Right side in RTL: App Brand, Branch and Machine Badge -->
        <div class="header-brand-wrap">
            <div class="header-app-icon">
                <i class="fa-solid fa-print"></i>
            </div>
            <div class="header-titles">
                <div class="header-app-name">{{ $appName ?? 'تطبيق طباعة' }}</div>
                <div class="header-branch-name">
                    <span>📍</span>
                    <span>{{ $kiosk->branch?->name['ar'] ?? 'فرع الرياض - حي العليا' }}</span>
                </div>
            </div>
            <div class="kiosk-code-pill num-font">{{ $kiosk->machine_code }}</div>
        </div>

        <!-- Left side in RTL: Status Badge & Digital Clock -->
        <div class="header-stats-wrap">
            <div class="status-badge online">
                <div class="status-dot"></div>
                <span id="machine-status-text">متصلة وجاهزة</span>
            </div>

            <div class="header-clock num-font" id="digital-clock">--:--</div>
        </div>
    </header>

    <!-- Main Stage -->
    <main class="main-stage">
        <div class="card-stage" id="screen-content">
            <!-- Decorative corner polygons on the card (as in screenshot) -->
            <div class="card-corner-topleft"></div>
            <div class="card-corner-bottomright"></div>
            <div class="card-corner-bottomleft"></div>
            <div class="card-corner-topright"></div>

            <!-- ================= Stage 1: Waiting Scan View (Screenshot Matching) ================= -->
            <div class="stage-waiting-grid" id="view-waiting">
                
                <!-- Column 1: Main Title, Subtitle, and Feature Badges (Right in RTL) -->
                <div class="waiting-text-col">
                    <h1 class="waiting-title">الطباعة الذاتية الفورية</h1>
                    <p class="waiting-subtitle">
                        امسح رمز الاستجابة السريعة (QR) بكاميرا هاتفك لبدء ربط جهازك فوراً وتجهيز مستندك للطباعة.
                    </p>

                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon-box">
                                <i class="fa-solid fa-file-arrow-up"></i>
                            </div>
                            <span>ارفع ملفك مباشرة من جوالك (PDF) / صور.</span>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon-box">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <span>تحكم بخيارات الألوان والوجهين بلمسة واحدة.</span>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon-box">
                                <i class="fa-solid fa-credit-card"></i>
                            </div>
                            <span>ادفع إلكترونياً واستلم أوراقك في ثوانٍ.</span>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Partner Brand Logo & Machine Code (Center) -->
                <div class="waiting-middle-col">
                    <div class="partner-logo-box">
                        <div class="partner-text-en">
                            <div class="brand-ar">منصور</div>
                            <div class="brand-en">Mansour</div>
                        </div>
                        <div class="partner-icon-diamond">
                            <div class="partner-icon-inner">
                                <i class="fa-solid fa-book-open"></i>
                            </div>
                        </div>
                        <div class="partner-text-ar">
                            <div class="brand-ar">قرطاسية</div>
                            <div class="brand-en">stationery</div>
                        </div>
                    </div>

                    <div class="machine-code-wrap">
                        <div class="machine-code-box num-font">
                            <div>{{ substr($kiosk->machine_code, 0, strrpos($kiosk->machine_code, '-') !== false ? strrpos($kiosk->machine_code, '-') + 1 : 4) }}</div>
                            <div>{{ substr($kiosk->machine_code, strrpos($kiosk->machine_code, '-') !== false ? strrpos($kiosk->machine_code, '-') + 1 : 4) }}</div>
                        </div>
                        <div class="machine-code-label">
                            كود<br>الطابعة:
                        </div>
                    </div>
                </div>

                <!-- Column 3: The Big QR Code Frame (Left in RTL) -->
                <div class="waiting-qr-col">
                    <div class="qr-frame-outer">
                        <div class="qr-scan-line"></div>
                        <div id="qr-container" style="width: 100%; height: 100%;">
                            {!! $qrSvg !!}
                        </div>
                    </div>
                </div>

            </div>

            <!-- ================= Stage 2: Connected State ================= -->
            <div class="center-state" id="view-connected" style="display: none;">
                <div class="state-big-icon" style="color: #10b981;">
                    <i class="fa-solid fa-link"></i>
                </div>
                <h2 class="state-title" style="color: #059669;">تم ربط هاتفك بنجاح!</h2>
                <p class="state-subtitle">
                    يرجى الآن اختيار المستند وتحديد إعدادات الطباعة من شاشة جوالك.
                </p>
                <div style="font-size: 0.95rem; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
                    <div class="status-dot"></div> بانتظار رفع الملف من هاتفك...
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
                <div class="state-big-icon" style="color: #f59e0b;">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <h2 class="state-title" style="color: #d97706;">برجاء إتمام الدفع عبر الجوال</h2>
                <p class="state-subtitle">
                    إجمالي المبلغ المطلوب: <strong id="pay-total-amount" class="num-font" style="color: #005bb5; font-size: 1.4rem;">0.00 ر.س</strong>
                </p>
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

            <!-- Hidden Iframe for Direct Silent Hardware Printing -->
            <iframe id="kiosk-print-frame" style="position: fixed; right: 0; bottom: 0; width: 0; height: 0; border: 0; opacity: 0; pointer-events: none;"></iframe>

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

    <!-- Footer Bar (Exact match to screenshot bottom bar) -->
    <footer class="kiosk-footer">
        <!-- Right side in RTL: Support Phone -->
        <div class="footer-support">
            <i class="fa-solid fa-phone"></i>
            <span>في حال وجود أي استفسار أو مشكلة، تواصل معنا:</span>
            <span class="num-font" style="letter-spacing: 0.5px;">{{ $supportPhone ?? '+966500000000' }}</span>
        </div>

        <!-- Left side in RTL: Operational note -->
        <div class="footer-note">
            <i class="fa-solid fa-bolt"></i>
            <span>محتوى يتطلب اتصال بالإنترنت للحصول على نتائج دقيقة</span>
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
                    startPrintSimulation(data && data.session ? data.session.token : currentSessionToken);
                    triggerBrowserPrint(fileUrl);
                } else if (status === 'completed') {
                    document.getElementById('view-completed').style.display = 'flex';
                }
            }

            if (status === 'waiting_scan') {
                hasTriggeredPrintForToken = null;
            }

            if (status === 'waiting_scan' && data && data.qr_svg) {
                document.getElementById('qr-container').innerHTML = data.qr_svg;
            } else if (status === 'file_uploaded' && data && data.session) {
                document.getElementById('display-filename').innerText = data.session.file_name || 'document.pdf';
                document.getElementById('info-pages').innerText = data.session.detected_page_count || 1;
                document.getElementById('info-sheets').innerText = data.session.total_sheets || 1;
                document.getElementById('info-mode').innerText = (data.session.color_mode === 'color' ? 'ملون' : 'أبيض وأسود') + 
                    (data.session.side_mode === 'double_sided' ? ' (وجهين)' : ' (وجه واحد)');
                document.getElementById('display-price').innerText = Number(data.session.total_price).toFixed(2) + ' ر.س';
            } else if (status === 'awaiting_payment' && data && data.session) {
                document.getElementById('pay-total-amount').innerText = Number(data.session.total_price).toFixed(2) + ' ر.س';
            }
        }

        let hasTriggeredPrintForToken = null;

        function triggerBrowserPrint(fileUrl) {
            if (!fileUrl) {
                console.warn('Physical print: no file_url provided');
                return;
            }

            if (hasTriggeredPrintForToken === currentSessionToken) return;
            hasTriggeredPrintForToken = currentSessionToken;

            console.log('Sending direct print command to kiosk printer for:', fileUrl);

            let frame = document.getElementById('kiosk-print-frame');
            if (!frame) {
                frame = document.createElement('iframe');
                frame.id = 'kiosk-print-frame';
                frame.style.position = 'fixed';
                frame.style.right = '0';
                frame.style.bottom = '0';
                frame.style.width = '0';
                frame.style.height = '0';
                frame.style.border = '0';
                frame.style.opacity = '0';
                frame.style.pointerEvents = 'none';
                document.body.appendChild(frame);
            }

            frame.src = fileUrl;

            frame.onload = function() {
                try {
                    setTimeout(() => {
                        console.log('Triggering iframe window.print()');
                        frame.contentWindow.focus();
                        frame.contentWindow.print();
                    }, 500);
                } catch (e) {
                    console.error('Error invoking silent frame print:', e);
                }
            };
        }

        function startPrintSimulation(token) {
            clearInterval(printAnimationInterval);
            let percent = 0;
            const bar = document.getElementById('print-progress');
            const txt = document.getElementById('print-percent');
            const note = document.getElementById('print-status-note');

            bar.style.width = '0%';
            txt.innerText = '0%';
            note.innerText = '⚡ جاري استقبال أمر الطباعة وسحب الأوراق من الطابعة...';

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
