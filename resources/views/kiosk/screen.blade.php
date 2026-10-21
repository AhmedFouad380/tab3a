<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شاشة كشك الطباعة الفورية - {{ $kiosk->machine_code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090d16;
            --surface: #111827;
            --surface-card: #1e293b;
            --primary: #10b981;
            --primary-glow: rgba(16, 185, 129, 0.35);
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.35);
            --gold: #f59e0b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            user-select: none;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            width: 100vw;
            overflow-x: hidden;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        /* Ambient glowing background */
        body::before {
            content: '';
            position: absolute;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 500px;
            background: radial-gradient(ellipse, rgba(16, 185, 129, 0.12) 0%, rgba(15, 23, 42, 0) 70%);
            z-index: 0;
            pointer-events: none;
        }

        /* Header Bar */
        .header {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 32px;
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 8px 20px var(--primary-glow);
        }

        .brand-title {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        .kiosk-badge {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 4px 14px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 13px;
            font-family: 'Outfit', sans-serif;
        }

        .hardware-stats {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.03);
            padding: 6px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 10px #10b981;
            animation: pulse 2s infinite;
        }

        .clock-time {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
        }

        /* Main Display Canvas */
        .main-stage {
            position: relative;
            z-index: 10;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px 20px;
        }

        .card-stage {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 24px 32px;
            width: 100%;
            max-width: 880px;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Stage 1: Waiting for Scan */
        .stage-waiting {
            display: flex;
            align-items: center;
            justify-content: space-around;
            gap: 32px;
            text-align: right;
        }

        .qr-box-outer {
            background: #ffffff;
            padding: 16px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 40px var(--primary-glow);
            position: relative;
            animation: float 4s ease-in-out infinite;
        }

        .qr-box-outer svg {
            display: block;
            width: 220px;
            height: 220px;
        }

        .scanner-laser {
            position: absolute;
            left: 10%;
            right: 10%;
            height: 4px;
            background: linear-gradient(90deg, transparent, #10b981, transparent);
            box-shadow: 0 0 15px #10b981;
            animation: laserMove 2.5s ease-in-out infinite;
        }

        .stage-title {
            font-size: 28px;
            font-weight: 900;
            line-height: 1.25;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ffffff 40%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stage-sub {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .features-pill {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pill-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            font-weight: 700;
            color: #e2e8f0;
        }

        .pill-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        /* Generic State Center Card */
        .center-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .big-icon {
            font-size: 52px;
            filter: drop-shadow(0 15px 30px rgba(0,0,0,0.4));
            animation: bounceIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Stage Details Grid (Uploaded) */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            width: 100%;
            margin: 12px 0 16px 0;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 10px 14px;
            text-align: center;
        }

        .info-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            font-family: 'Outfit', 'Cairo', sans-serif;
        }

        /* Price highlight */
        .price-badge {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.25));
            border: 2px solid #f59e0b;
            color: #fbbf24;
            padding: 8px 28px;
            border-radius: 16px;
            font-size: 24px;
            font-weight: 900;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.2);
            font-family: 'Outfit', 'Cairo', sans-serif;
            display: inline-block;
        }

        /* Progress Bar */
        .progress-bar-container {
            width: 80%;
            height: 16px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            overflow: hidden;
            margin-top: 14px;
            position: relative;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #3b82f6);
            border-radius: 999px;
            width: 0%;
            transition: width 0.3s ease;
            box-shadow: 0 0 20px #10b981;
        }

        /* Footer */
        .footer {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 32px;
            background: rgba(17, 24, 39, 0.5);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes laserMove {
            0% { top: 10%; opacity: 0.8; }
            50% { top: 90%; opacity: 1; }
            100% { top: 10%; opacity: 0.8; }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.9); }
        }

        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            60% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        .pulse-ring {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            animation: pulse 1.5s infinite;
        }

        /* Inactivity Warning Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(4, 7, 15, 0.88);
            backdrop-filter: blur(14px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.25s ease;
        }

        .modal-card {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border: 2px solid #f59e0b;
            box-shadow: 0 25px 60px rgba(245, 158, 11, 0.25), 0 0 40px rgba(0, 0, 0, 0.8);
            border-radius: 28px;
            padding: 36px 40px;
            max-width: 580px;
            width: 90%;
            text-align: center;
            position: relative;
            animation: bounceIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .modal-icon-badge {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(245, 158, 11, 0.15);
            border: 2px solid #f59e0b;
            color: #f59e0b;
            font-size: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 0 25px rgba(245, 158, 11, 0.3);
            animation: pulse 1.2s infinite;
        }

        .modal-title {
            font-size: 26px;
            font-weight: 900;
            color: #fff;
            margin-bottom: 12px;
        }

        .modal-description {
            font-size: 16px;
            color: #cbd5e1;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .countdown-ring {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 18px;
            font-weight: 800;
            color: #fef08a;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.4);
            padding: 10px 24px;
            border-radius: 999px;
            margin-bottom: 28px;
        }

        .countdown-number {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 900;
            color: #f59e0b;
        }

        .modal-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
        }

        .btn-continue {
            flex: 1;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 14px;
            font-size: 17px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
            transition: all 0.2s ease;
        }

        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(16, 185, 129, 0.5);
        }

        .btn-cancel {
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 14px 20px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-cancel:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: #ef4444;
            color: #fca5a5;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Header Bar -->
    <header class="header">
        <div class="brand-section">
            <div class="brand-logo">🖨️</div>
            <div>
                <div class="brand-title">{{ $appName }}</div>
                <div style="font-size: 13px; color: var(--text-muted);">
                    📍 {{ $kiosk->branch?->name['ar'] ?? 'الفرع الرئيسي' }}
                </div>
            </div>
            <div class="kiosk-badge">{{ $kiosk->machine_code }}</div>
        </div>

        <div class="hardware-stats">
            <div class="stat-item">
                <div class="stat-dot"></div>
                <span id="machine-status-text">متصلة وجاهزة</span>
            </div>
            <div class="stat-item">
                📄 ورق A4: <span id="paper-count" style="color: #fff; margin-right: 4px;">{{ $kiosk->paper_tray_a4_sheets }}</span> ورقة
            </div>
            <div class="stat-item">
                💧 حبر أسود: <span id="toner-count" style="color: #fff; margin-right: 4px;">{{ $kiosk->black_toner_level }}%</span>
            </div>
            <div class="clock-time" id="digital-clock">--:--</div>
        </div>
    </header>

    <!-- Main Stage Container -->
    <main class="main-stage">
        <div class="card-stage" id="screen-content">
            <!-- Stage 1: Waiting Scan (Default) -->
            <div class="stage-waiting" id="view-waiting">
                <div class="qr-box-outer">
                    <div class="scanner-laser"></div>
                    <div id="qr-container">
                        {!! $qrSvg !!}
                    </div>
                </div>

                <div>
                    <h1 class="stage-title">الطباعة الذاتية الفورية</h1>
                    <p class="stage-sub">
                        امسح رمز الاستجابة السريعة (QR) بكاميرا هاتفك لبدء ربط جهازك فوراً ورفع مستندك للطباعة.
                    </p>

                    <div class="features-pill">
                        <div class="pill-item">
                            <div class="pill-icon">📱</div>
                            <span>ارفع ملفك مباشرة من جوالك (PDF / صور).</span>
                        </div>
                        <div class="pill-item">
                            <div class="pill-icon">⚡</div>
                            <span>تحكم بخيارات الألوان والوجهين بلمسة واحدة.</span>
                        </div>
                        <div class="pill-item">
                            <div class="pill-icon">💳</div>
                            <span>ادفع إلكترونياً واستلم أوراقك في ثوانٍ.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stage 2: Connected -->
            <div class="center-state" id="view-connected" style="display: none;">
                <div class="pulse-ring">🔗</div>
                <h1 class="stage-title" style="color: #34d399;">تم ربط هاتفك بنجاح!</h1>
                <p class="stage-sub" style="font-size: 22px;">
                    يرجى الآن اختيار المستند وتحديد إعدادات الطباعة من شاشة جوالك.
                </p>
                <div style="font-size: 15px; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
                    <div class="stat-dot"></div> بانتظار رفع الملف من الجوال...
                </div>
            </div>

            <!-- Stage 3: File Uploaded & Processing -->
            <div class="center-state" id="view-uploaded" style="display: none;">
                <div class="big-icon">📄</div>
                <h1 class="stage-title">تم استلام المستند بنجاح</h1>
                <p class="stage-sub" id="display-filename" style="color: #93c5fd; font-weight: 700; font-size: 20px;">
                    document.pdf
                </p>

                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">عدد الصفحات</div>
                        <div class="info-value" id="info-pages">0</div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">إجمالي الأوراق (A4)</div>
                        <div class="info-value" id="info-sheets">0</div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">نوع الطباعة</div>
                        <div class="info-value" id="info-mode">أبيض وأسود</div>
                    </div>
                </div>

                <div class="price-badge" id="display-price">
                    0.00 ر.س
                </div>
            </div>

            <!-- Stage 4: Awaiting Payment -->
            <div class="center-state" id="view-payment" style="display: none;">
                <div class="pulse-ring" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;">💳</div>
                <h1 class="stage-title" style="color: #fbbf24;">برجاء إتمام الدفع عبر الجوال</h1>
                <p class="stage-sub" style="font-size: 20px;">
                    إجمالي المبلغ المطلوب: <strong id="pay-total-amount" style="color: #fff;">0.00 ر.س</strong>
                </p>
                <div style="display: flex; gap: 15px; font-size: 30px; margin-top: 10px;">
                    <span>Pay</span> <span>💳 Mada</span> <span>💳 Visa</span>
                </div>
                <div style="font-size: 15px; color: var(--text-muted); margin-top: 15px;">
                    بمجرد اكتمال الدفع ستبدأ الطابعة بسحب الأوراق فوراً...
                </div>
            </div>

            <!-- Stage 5: Printing -->
            <div class="center-state" id="view-printing" style="display: none;">
                <div class="big-icon" style="animation: float 2s infinite;">🖨️</div>
                <h1 class="stage-title" style="color: #60a5fa;">جاري طباعة مستندك الآن...</h1>
                <p class="stage-sub" style="font-size: 20px;">
                    فضلاً انتظر، لا تغادر الكشك حتى خروج جميع الأوراق.
                </p>
                
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" id="print-progress"></div>
                </div>
                <div style="font-size: 15px; color: var(--text-muted); margin-top: 10px;" id="print-percent">
                    0%
                </div>
            </div>

            <!-- Stage 6: Completed -->
            <div class="center-state" id="view-completed" style="display: none;">
                <div class="big-icon">🎉</div>
                <h1 class="stage-title" style="color: #34d399;">تمت الطباعة بنجاح!</h1>
                <p class="stage-sub" style="font-size: 22px; font-weight: 700; color: #fff;">
                    فضلاً استلم أوراقك من مخرج الطابعة بالأسفل.
                </p>
                <div style="font-size: 14px; color: var(--text-muted); margin: 15px 0;">
                    🛡️ حفاظاً على خصوصيتك، تم حذف الملف تلقائياً من خوادمنا.
                </div>
                <div class="stat-item" style="font-size: 16px; color: #a7f3d0;">
                    ⏳ ستعود الشاشة لوضع الاستعداد خلال <strong id="reset-counter" style="margin: 0 4px; font-size: 20px;">7</strong> ثوانٍ
                </div>
            </div>

        </div>
    </main>

    <!-- Footer Bar -->
    <footer class="footer">
        <div>
            📞 في حال وجود أي استفسار أو مشكلة، تواصل معنا: {{ \App\Models\SystemSetting::where('key', 'support_phone')->first()?->value['ar'] ?? '+966500000000' }}
        </div>
        <div>
            ⚡ مدعوم بنظام الطباعة الفورية الذاتية الذكي
        </div>
    </footer>

    <!-- Inactivity Timeout Modal -->
    <div id="inactivity-modal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-icon-badge">⏳</div>
            <h2 class="modal-title">هل تريد المزيد من الوقت؟</h2>
            <p class="modal-description">
                لوحظ عدم وجود نشاط على الشاشة أو هاتفك. للحفاظ على خصوصية بياناتك وإتاحة الكشك للعميل التالي، ستنتهي الجلسة تلقائياً.
            </p>
            <div class="countdown-ring">
                ⏳ متبقي <span id="inactivity-countdown" class="countdown-number">10</span> ثوانٍ للإلغاء
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-continue" onclick="handleKeepAlive()">
                    ✅ نعم، أحتاج وقتاً إضافياً
                </button>
                <button type="button" class="btn-cancel" onclick="handleTimeoutCancel()">
                    ❌ إنهاء والعودة للرئيسية
                </button>
            </div>
        </div>
    </div>

    <script>
        const machineCode = "{{ $kiosk->machine_code }}";
        let currentStatus = null;
        let currentSessionToken = "{{ $session->session_token }}";
        let pollInterval = null;
        let printAnimationInterval = null;

        // Inactivity Watcher (30s Idle Limit)
        let idleSeconds = 0;
        let isTimeoutModalOpen = false;
        let modalCountdownInterval = null;

        // Reset idle on user touch/click on screen
        window.addEventListener('click', () => {
            if (!isTimeoutModalOpen) {
                idleSeconds = 0;
            }
        });
        window.addEventListener('touchstart', () => {
            if (!isTimeoutModalOpen) {
                idleSeconds = 0;
            }
        });

        // 1-second ticker for inactivity
        setInterval(() => {
            // Only monitor inactivity in active customer stages
            if (['connected', 'file_uploaded', 'awaiting_payment'].includes(currentStatus)) {
                idleSeconds++;
                if (idleSeconds >= 60 && !isTimeoutModalOpen) {
                    showInactivityModal();
                }
            } else {
                idleSeconds = 0;
                if (isTimeoutModalOpen) {
                    hideInactivityModal();
                }
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

        // Customer clicked: Yes, I need more time
        function handleKeepAlive() {
            hideInactivityModal();
            idleSeconds = 0;

            // Notify backend API
            fetch('/api/v1/kiosks/extend-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ session_token: currentSessionToken })
            }).catch(err => console.log('Extend session ping error', err));
        }

        // Timeout expired or user clicked: Cancel & Return to QR
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

        // Digital Clock
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
                if (isTimeoutModalOpen) {
                    hideInactivityModal();
                }

                // Hide all views
                document.getElementById('view-waiting').style.display = 'none';
                document.getElementById('view-connected').style.display = 'none';
                document.getElementById('view-uploaded').style.display = 'none';
                document.getElementById('view-payment').style.display = 'none';
                document.getElementById('view-printing').style.display = 'none';
                document.getElementById('view-completed').style.display = 'none';

                if (status === 'waiting_scan') {
                    document.getElementById('view-waiting').style.display = 'flex';
                } else if (status === 'connected') {
                    document.getElementById('view-connected').style.display = 'flex';
                } else if (status === 'file_uploaded') {
                    document.getElementById('view-uploaded').style.display = 'flex';
                } else if (status === 'awaiting_payment') {
                    document.getElementById('view-payment').style.display = 'flex';
                } else if (status === 'printing') {
                    document.getElementById('view-printing').style.display = 'flex';
                    startPrintSimulation(data.session.token);
                } else if (status === 'completed') {
                    document.getElementById('view-completed').style.display = 'flex';
                    startResetCountdown(data.session.token);
                }
            }

            // Always update dynamic content in real-time even if status hasn't changed
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

        // Simulate Smooth Printing Progress
        function startPrintSimulation(token) {
            let progress = 0;
            const bar = document.getElementById('print-progress');
            const percentText = document.getElementById('print-percent');
            clearInterval(printAnimationInterval);

            printAnimationInterval = setInterval(() => {
                progress += 5;
                if (progress > 100) {
                    progress = 100;
                    clearInterval(printAnimationInterval);
                    // Move to completed
                    setTimeout(() => {
                        renderState('completed', { session: { token: token } });
                    }, 500);
                }
                bar.style.width = progress + '%';
                percentText.innerText = progress + '%';
            }, 250);
        }

        // Auto-reset Countdown back to Standby
        function startResetCountdown(token) {
            let seconds = 7;
            const counter = document.getElementById('reset-counter');
            if (counter) counter.innerText = seconds;

            const timer = setInterval(() => {
                seconds--;
                if (counter) counter.innerText = seconds;
                if (seconds <= 0) {
                    clearInterval(timer);
                    // Call backend to complete & get new fresh token
                    fetch(`/kiosk/complete-reset/${token}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        currentStatus = null;
                        fetchStatus();
                    })
                    .catch(() => {
                        currentStatus = null;
                        fetchStatus();
                    });
                }
            }, 1000);
        }

        // Polling function
        function fetchStatus() {
            // While customer is viewing the completion screen, do not allow polling to overwrite it
            if (currentStatus === 'completed') {
                return;
            }

            fetch(`/api/kiosks/${machineCode}/screen-status`)
                .then(res => res.json())
                .then(data => {
                    // Check again if completion animation started while request was in-flight
                    if (currentStatus === 'completed') {
                        return;
                    }

                    // Update header supplies
                    document.getElementById('paper-count').innerText = data.paper_a4;
                    document.getElementById('toner-count').innerText = data.black_toner + '%';
                    
                    if (data.session) {
                        if (currentSessionToken !== data.session.token || data.session.status !== currentStatus) {
                            idleSeconds = 0;
                            if (isTimeoutModalOpen) {
                                hideInactivityModal();
                            }
                        }
                        currentSessionToken = data.session.token;
                        renderState(data.session.status, data);
                    }
                })
                .catch(err => console.error('Status poll error', err));
        }

        // Start polling every 1200ms
        pollInterval = setInterval(fetchStatus, 1200);
    </script>
</body>
</html>
