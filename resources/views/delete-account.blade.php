<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="صفحة طلب حذف الحساب والبيانات - تطبيق ومنصة طباعة (Taba3a). Account and Data Deletion Request Page for Google Play & Apple App Store compliance.">
    <title>حذف الحساب والبيانات | تطبيق ومنصة طباعة - Taba3a Account Deletion</title>

    <!-- Google Fonts: Cairo & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-glow: rgba(37, 99, 235, 0.35);
            --accent: #06b6d4;
            --accent-glow: rgba(6, 182, 212, 0.3);
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --danger-glow: rgba(239, 68, 68, 0.35);
            --success: #10b981;
            --warning: #f59e0b;
            --bg-dark: #0a0e17;
            --bg-card: rgba(18, 24, 38, 0.75);
            --bg-card-hover: rgba(28, 36, 56, 0.9);
            --border-glass: rgba(255, 255, 255, 0.08);
            --border-glass-hover: rgba(59, 130, 246, 0.4);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-primary);
            line-height: 1.7;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(239, 68, 68, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 85% 30%, rgba(37, 99, 235, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 50% 85%, rgba(6, 182, 212, 0.08) 0%, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .num-font {
            font-family: 'Plus Jakarta Sans', 'Cairo', sans-serif;
        }

        /* Header / Navbar */
        .header {
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(10, 14, 23, 0.85);
            border-bottom: 1px solid var(--border-glass);
            padding: 1rem 0;
        }

        .container {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-primary);
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--danger), #b91c1c);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.3rem;
            box-shadow: 0 0 20px var(--danger-glow);
        }

        .brand-title {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Lang Switcher */
        .lang-switch {
            display: flex;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
            border-radius: 30px;
            padding: 4px;
        }

        .lang-btn {
            border: none;
            background: transparent;
            color: var(--text-secondary);
            padding: 6px 14px;
            border-radius: 20px;
            font-family: 'Cairo', 'Plus Jakarta Sans', sans-serif;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .lang-btn.active {
            background: var(--danger);
            color: #ffffff;
            box-shadow: 0 2px 10px var(--danger-glow);
        }

        .back-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #f87171;
        }

        /* Hero */
        .hero {
            padding: 3rem 0 2rem;
            text-align: center;
        }

        .badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 9999px;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }

        .hero-title {
            font-size: 2.35rem;
            font-weight: 800;
            margin-bottom: 0.85rem;
            background: linear-gradient(135deg, #ffffff 40%, #fca5a5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            color: var(--text-secondary);
            max-width: 620px;
            margin: 0 auto;
        }

        /* Tabs Navigation */
        .tabs-wrap {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .tab-button {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            color: var(--text-secondary);
            padding: 0.75rem 1.4rem;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
        }

        .tab-button:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-primary);
        }

        .tab-button.active {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.45);
            color: #fca5a5;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
        }

        /* Content Card */
        .card-main {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 2.5rem;
            backdrop-filter: blur(16px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
        }

        /* Disclosure Grid */
        .disclosure-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.25rem;
            margin: 1.5rem 0 2rem;
        }

        .disclosure-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-glass);
            border-radius: 14px;
            padding: 1.25rem;
        }

        .disclosure-card.danger {
            border-color: rgba(239, 68, 68, 0.3);
            background: rgba(239, 68, 68, 0.04);
        }

        .disclosure-card.warning {
            border-color: rgba(245, 158, 11, 0.3);
            background: rgba(245, 158, 11, 0.04);
        }

        .disclosure-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 0.75rem;
        }

        .disclosure-card.danger .disclosure-title {
            color: #f87171;
        }

        .disclosure-card.warning .disclosure-title {
            color: #fbbf24;
        }

        .disclosure-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-size: 0.92rem;
            color: #cbd5e1;
        }

        .disclosure-list li {
            position: relative;
            padding-right: 1.4rem;
        }

        [dir="ltr"] .disclosure-list li {
            padding-right: 0;
            padding-left: 1.4rem;
        }

        .disclosure-list li::before {
            content: "•";
            position: absolute;
            right: 0.2rem;
            font-size: 1.3rem;
            line-height: 1;
        }

        [dir="ltr"] .disclosure-list li::before {
            right: auto;
            left: 0.2rem;
        }

        .disclosure-card.danger .disclosure-list li::before {
            color: #ef4444;
        }

        .disclosure-card.warning .disclosure-list li::before {
            color: #f59e0b;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.5rem;
            text-align: right;
        }

        [dir="ltr"] .form-group {
            text-align: left;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .form-input-wrap {
            position: relative;
        }

        .form-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            padding: 0.85rem 1rem;
            color: #ffffff;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #ef4444;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.25);
            background: rgba(255, 255, 255, 0.08);
        }

        .form-help {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-top: 0.4rem;
        }

        .btn-danger-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--danger), var(--danger-hover));
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 0.95rem;
            font-size: 1.05rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            box-shadow: 0 4px 15px var(--danger-glow);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-danger-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5);
        }

        .btn-danger-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-secondary);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            padding: 0.75rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
        }

        /* Alert Boxes */
        .alert-box {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: none;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .alert-box.success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #6ee7b7;
        }

        .alert-box.error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        .alert-box.info {
            background: rgba(37, 99, 235, 0.12);
            border: 1px solid rgba(37, 99, 235, 0.35);
            color: #93c5fd;
        }

        /* Step Indicator */
        .steps-flow {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .step-node {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .step-node.active {
            color: #f87171;
        }

        .step-node.done {
            color: #34d399;
        }

        .step-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
        }

        .step-node.active .step-circle {
            background: rgba(239, 68, 68, 0.2);
            border-color: #ef4444;
            color: #fca5a5;
        }

        .step-node.done .step-circle {
            background: rgba(16, 185, 129, 0.2);
            border-color: #10b981;
            color: #6ee7b7;
        }

        .step-divider {
            width: 40px;
            height: 2px;
            background: var(--border-glass);
        }

        /* In-App Steps */
        .step-instruction-box {
            display: flex;
            align-items: flex-start;
            gap: 1.25rem;
            padding: 1.25rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-glass);
            border-radius: 14px;
            margin-bottom: 1rem;
        }

        .step-instruction-box:last-child {
            margin-bottom: 0;
        }

        .instruction-number {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #f87171;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .instruction-text h4 {
            font-size: 1.05rem;
            margin-bottom: 0.35rem;
            color: #ffffff;
        }

        .instruction-text p {
            font-size: 0.92rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            margin-top: auto;
            border-top: 1px solid var(--border-glass);
            background: rgba(10, 14, 23, 0.9);
            padding: 2.5rem 0 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .footer-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: #f87171;
        }

        @media (max-width: 640px) {
            .hero-title {
                font-size: 1.85rem;
            }
            .card-main {
                padding: 1.5rem;
            }
            .disclosure-grid {
                grid-template-columns: 1fr;
            }
            .steps-flow {
                gap: 0.5rem;
            }
            .step-divider {
                width: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <header class="header">
        <div class="container">
            <div class="nav-inner">
                <a href="{{ url('/landing') }}" class="brand-link">
                    <div class="brand-icon">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                    <div>
                        <div class="brand-title">منصة طباعة</div>
                        <div style="font-size: 0.72rem; color: var(--text-muted);" class="num-font">Taba3a Smart Printing</div>
                    </div>
                </a>

                <div class="nav-actions">
                    <!-- Language Selector (Ar / En) -->
                    <div class="lang-switch">
                        <button type="button" class="lang-btn active" id="btnAr" onclick="switchLanguage('ar')">العربية</button>
                        <button type="button" class="lang-btn" id="btnEn" onclick="switchLanguage('en')">English</button>
                    </div>

                    <a href="{{ url('/privacy') }}" class="back-link">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span id="txtPrivacyNav">سياسة الخصوصية</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container" style="flex: 1; padding-bottom: 3rem;">
        
        <!-- Hero Section -->
        <section class="hero">
            <div class="badge-tag">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span id="badgeText">سياسة ومسار حذف الحساب والبيانات</span>
            </div>
            <h1 class="hero-title" id="heroTitle">طلب حذف الحساب والبيانات الشخصية</h1>
            <p class="hero-subtitle" id="heroSubtitle">
                وفقاً لسياسات Google Play ومتطلبات App Store ونظام حماية البيانات الشخصية، يمكنك هنا حذف حسابك نهائياً والاطلاع على تفاصيل البيانات التي يتم حذفها فوراً والبيانات المحتفظ بها نظامياً.
            </p>
        </section>

        <!-- Google Play & Apple Mandatory Data Disclosure Section -->
        <div class="disclosure-grid">
            <div class="disclosure-card danger">
                <div class="disclosure-title">
                    <i class="fa-solid fa-trash-can"></i>
                    <span id="delDataTitle">ما الذي سيتم حذفه نهائياً؟</span>
                </div>
                <ul class="disclosure-list" id="delDataList">
                    <li>بيانات الملف الشخصي (الاسم، رقم الجوال، البريد الإلكتروني).</li>
                    <li>كافة جلسات الدخول الفعالة والرموز المميزة (Revoke Tokens).</li>
                    <li>رموز الإشعارات (FCM Device Tokens).</li>
                    <li>المستندات والملفات المرفوعة غير المطبوعة فوراً.</li>
                    <li>العناوين والإعدادات الشخصية المحفوظة.</li>
                </ul>
            </div>

            <div class="disclosure-card warning">
                <div class="disclosure-title">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span id="retainDataTitle">ما الذي نحتفظ به نظامياً ولماذا؟</span>
                </div>
                <ul class="disclosure-list" id="retainDataList">
                    <li>فواتير وسجلات العمليات المالية المنفذة سابقاً.</li>
                    <li>الرقم الضريبي وتفاصيل الفواتير الإلكترونية المعتمدة.</li>
                    <li><strong>سبب الاحتفاظ:</strong> التزام قانوني إلزامي وفقاً لأنظمة هيئة الزكاة والضريبة والجمارك (ZATCA) والأنظمة المحاسبية والمالية في المملكة العربية السعودية، ولا تستخدم هذه السجلات لأي أغراض أخرى.</li>
                </ul>
            </div>
        </div>

        <!-- Mode Switcher Tabs -->
        <div class="tabs-wrap">
            <button type="button" class="tab-button active" id="tabOtpBtn" onclick="switchTab('otp')">
                <i class="fa-solid fa-mobile-screen-button"></i>
                <span id="tabOtpLabel">الحذف الفوري الآلي برمز التحقق (OTP)</span>
            </button>
            <button type="button" class="tab-button" id="tabRequestBtn" onclick="switchTab('request')">
                <i class="fa-solid fa-envelope-open-text"></i>
                <span id="tabRequestLabel">تقديم طلب يدوي للدعم الفني</span>
            </button>
            <button type="button" class="tab-button" id="tabInAppBtn" onclick="switchTab('inApp')">
                <i class="fa-brands fa-apple"></i>
                <span id="tabInAppLabel">طريقة الحذف من داخل التطبيق</span>
            </button>
        </div>

        <!-- Global Alert Box -->
        <div id="alertBox" class="alert-box">
            <i id="alertIcon" class="fa-solid fa-circle-check" style="font-size: 1.25rem; flex-shrink: 0; margin-top: 2px;"></i>
            <div id="alertMessage"></div>
        </div>

        <!-- TAB 1: Direct Instant Deletion via OTP -->
        <div id="tabOtp" class="card-main">
            <!-- Step Indicators -->
            <div class="steps-flow">
                <div class="step-node active" id="step1Indicator">
                    <div class="step-circle">1</div>
                    <span id="step1Label">إدخال رقم الجوال</span>
                </div>
                <div class="step-divider"></div>
                <div class="step-node" id="step2Indicator">
                    <div class="step-circle">2</div>
                    <span id="step2Label">رمز التحقق والتأكيد</span>
                </div>
            </div>

            <!-- STEP 1 Form: Phone Number -->
            <div id="step1Container">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.5rem;" id="step1Title">أدخل رقم الجوال المسجل في التطبيق</h3>
                    <p style="color: var(--text-secondary); font-size: 0.95rem;" id="step1Sub">سنرسل لك رمز تحقق عبر الرسائل القصيرة للتأكد من هويتك كمالك للحساب قبل الحذف النهائي.</p>
                </div>

                <form id="formSendOtp" onsubmit="handleSendOtp(event)">
                    <div class="form-group">
                        <label class="form-label" for="phoneInput" id="lblPhone">رقم الجوال المسجل (مثال: 0551234567 أو +966551234567)</label>
                        <div class="form-input-wrap">
                            <input type="text" id="phoneInput" name="phone" class="form-input num-font" placeholder="05xxxxxxxx أو +9665xxxxxxxx" required autocomplete="tel" dir="ltr" style="text-align: left;">
                        </div>
                        <div class="form-help" id="helpPhone">يرجى كتابة رقم الجوال المستخدم في تسجيل الدخول بتطبيق طباعة.</div>
                    </div>

                    <button type="submit" id="btnSendOtp" class="btn-danger-submit">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span id="btnSendOtpText">إرسال رمز التحقق (Send Verification Code)</span>
                    </button>
                </form>
            </div>

            <!-- STEP 2 Form: Enter OTP & Final Confirmation -->
            <div id="step2Container" style="display: none;">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 52px; height: 52px; border-radius: 50%; background: rgba(239, 68, 68, 0.15); color: #f87171; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.4rem;">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.5rem;" id="step2Title">تأكيد حذف الحساب نهائياً</h3>
                    <p style="color: var(--text-secondary); font-size: 0.95rem;">
                        <span id="step2Sub">تم إرسال رمز التحقق إلى الرقم: </span>
                        <strong id="displayPhone" class="num-font" style="color: #ffffff;"></strong>
                    </p>
                </div>

                <form id="formConfirmDelete" onsubmit="handleConfirmDelete(event)">
                    <div class="form-group">
                        <label class="form-label" for="otpInput" id="lblOtp">رمز التحقق المكون من 4 أرقام (OTP Code)</label>
                        <div class="form-input-wrap">
                            <input type="text" id="otpInput" name="otp_code" class="form-input num-font" placeholder="1234" maxlength="4" required style="text-align: center; font-size: 1.6rem; letter-spacing: 8px;" dir="ltr">
                        </div>
                    </div>

                    <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; font-size: 0.9rem; color: #fca5a5;">
                        <i class="fa-solid fa-triangle-exclamation" style="margin-left: 6px;"></i>
                        <span id="txtWarningFinal">تنبيه: هذا الإجراء لا يمكن التراجع عنه. سيتم تعطيل حسابك وإلغاء تسجيل الدخول فوراً.</span>
                    </div>

                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <button type="submit" id="btnConfirmDelete" class="btn-danger-submit" style="flex: 2;">
                            <i class="fa-solid fa-trash-can"></i>
                            <span id="btnConfirmText">تأكيد حذف الحساب نهائياً (Permanently Delete)</span>
                        </button>
                        <button type="button" onclick="cancelStep2()" class="btn-secondary" style="flex: 1;">
                            <span id="btnCancelText">إلغاء أو تعديل الرقم</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- SUCCESS STATE -->
            <div id="stepSuccessContainer" style="display: none; text-align: center; padding: 2rem 1rem;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: rgba(16, 185, 129, 0.15); border: 2px solid #10b981; color: #10b981; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2.2rem;">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h3 style="font-size: 1.6rem; font-weight: 800; color: #ffffff; margin-bottom: 0.75rem;" id="successTitle">تم حذف الحساب بنجاح</h3>
                <p style="color: #cbd5e1; font-size: 1.05rem; max-width: 580px; margin: 0 auto 2rem; line-height: 1.8;" id="successMsg">
                    تم حذف حسابك وإلغاء تفعيل كافة الجلسات وحذف البيانات الشخصية والملفات المرتبطة به طبقاً لسياسة الخصوصية. شكراً لاستخدامك تطبيق طباعة.
                </p>
                <a href="{{ url('/landing') }}" class="btn-secondary" style="display: inline-block;">
                    <i class="fa-solid fa-house" style="margin-left: 6px;"></i>
                    <span id="btnReturnHome">العودة للرئيسية</span>
                </a>
            </div>
        </div>

        <!-- TAB 2: Manual Request Form -->
        <div id="tabRequest" class="card-main" style="display: none;">
            <div style="margin-bottom: 2rem; text-align: center;">
                <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.5rem;" id="manualTitle">تقديم طلب حذف الحساب يدوياً للإدارة</h3>
                <p style="color: var(--text-secondary); font-size: 0.95rem;" id="manualSub">إذا كنت تواجه صعوبة في استقبال رمز التحقق أو فقدت إمكانية الوصول لهاتفك، املأ هذا النموذج وسيقوم فريق الدعم بالتحقق ومعالجة طلبك خلال 48 ساعة.</p>
            </div>

            <form id="formManualRequest" onsubmit="handleManualRequest(event)">
                <div class="form-group">
                    <label class="form-label" for="manualName" id="lblManualName">الاسم الكامل</label>
                    <input type="text" id="manualName" name="name" class="form-input" placeholder="اكتب اسمك الثلاثي" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="manualPhone" id="lblManualPhone">رقم الجوال المسجل بالحساب</label>
                    <input type="text" id="manualPhone" name="phone" class="form-input num-font" placeholder="05xxxxxxxx أو +966xxxxxxxxx" required dir="ltr" style="text-align: left;">
                </div>

                <div class="form-group">
                    <label class="form-label" for="manualEmail" id="lblManualEmail">البريد الإلكتروني (اختياري لتأكيد الإنجاز)</label>
                    <input type="email" id="manualEmail" name="email" class="form-input num-font" placeholder="your-email@example.com" dir="ltr" style="text-align: left;">
                </div>

                <div class="form-group">
                    <label class="form-label" for="manualReason" id="lblManualReason">سبب حذف الحساب (اختياري)</label>
                    <textarea id="manualReason" name="reason" class="form-input" rows="3" placeholder="ملاحظات أو سبب رغبتك في حذف الحساب..."></textarea>
                </div>

                <button type="submit" id="btnSubmitManual" class="btn-danger-submit">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span id="btnSubmitManualText">إرسال طلب الحذف (Submit Request)</span>
                </button>
            </form>
        </div>

        <!-- TAB 3: In-App Deletion Instructions (App Store & Google Play Guide) -->
        <div id="tabInApp" class="card-main" style="display: none;">
            <div style="margin-bottom: 2rem; text-align: center;">
                <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.5rem;" id="inAppTitle">خطوات حذف الحساب مباشرة من داخل التطبيق</h3>
                <p style="color: var(--text-secondary); font-size: 0.95rem;" id="inAppSub">يمكنك في أي وقت حذف حسابك بنقرة زر واحدة عبر تطبيق iOS أو Android:</p>
            </div>

            <div class="step-instruction-box">
                <div class="instruction-number">1</div>
                <div class="instruction-text">
                    <h4 id="inAppStep1Title">افتح تطبيق طباعة (Taba3a)</h4>
                    <p id="inAppStep1Desc">قم بتشغيل التطبيق على هاتفك المحمول وتأكد من تسجيل الدخول بحسابك.</p>
                </div>
            </div>

            <div class="step-instruction-box">
                <div class="instruction-number">2</div>
                <div class="instruction-text">
                    <h4 id="inAppStep2Title">انتقل إلى "الملف الشخصي" (Profile)</h4>
                    <p id="inAppStep2Desc">اضغط على أيقونة الحساب / الملف الشخصي في الشريط السفلي للتطبيق.</p>
                </div>
            </div>

            <div class="step-instruction-box">
                <div class="instruction-number">3</div>
                <div class="instruction-text">
                    <h4 id="inAppStep3Title">اختر "إعدادات الحساب" ثم "حذف الحساب"</h4>
                    <p id="inAppStep3Desc">انزل إلى أسفل الصفحة واضغط على زر "حذف الحساب نهائياً" (Delete Account) الموضح باللون الأحمر.</p>
                </div>
            </div>

            <div class="step-instruction-box">
                <div class="instruction-number">4</div>
                <div class="instruction-text">
                    <h4 id="inAppStep4Title">تأكيد الحذف</h4>
                    <p id="inAppStep4Desc">قم بقراءة تنبيه الحذف واضغط "تأكيد الحذف" لإلغاء الحساب فوراً ومسح الجلسة والبيانات.</p>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-links">
                <a href="{{ url('/landing') }}" id="ftHome">الرئيسية</a>
                <span style="opacity: 0.3;">•</span>
                <a href="{{ url('/privacy') }}" id="ftPrivacy">سياسة الخصوصية</a>
                <span style="opacity: 0.3;">•</span>
                <a href="{{ url('/delete-account') }}" style="color: #f87171; font-weight: 600;" id="ftDelete">حذف الحساب</a>
                <span style="opacity: 0.3;">•</span>
                <a href="{{ url('/admin/login') }}" id="ftAdmin">بوابة الإدارة</a>
            </div>
            <div class="num-font" style="opacity: 0.7;">
                جميع الحقوق محفوظة &copy; {{ date('Y') }} منصة طباعة (Taba3a Smart Printing Platform). All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Interactive Logic Script -->
    <script>
        let currentLang = 'ar';
        let verifiedPhone = '';

        const translations = {
            ar: {
                badge: 'سياسة ومسار حذف الحساب والبيانات',
                heroTitle: 'طلب حذف الحساب والبيانات الشخصية',
                heroSubtitle: 'وفقاً لسياسات Google Play ومتطلبات App Store ونظام حماية البيانات الشخصية، يمكنك هنا حذف حسابك نهائياً والاطلاع على تفاصيل البيانات التي يتم حذفها فوراً والبيانات المحتفظ بها نظامياً.',
                delDataTitle: 'ما الذي سيتم حذفه نهائياً؟',
                delDataList: [
                    'بيانات الملف الشخصي (الاسم، رقم الجوال، البريد الإلكتروني).',
                    'كافة جلسات الدخول الفعالة والرموز المميزة (Revoke Tokens).',
                    'رموز الإشعارات (FCM Device Tokens).',
                    'المستندات والملفات المرفوعة غير المطبوعة فوراً.',
                    'العناوين والإعدادات الشخصية المحفوظة.'
                ],
                retainDataTitle: 'ما الذي نحتفظ به نظامياً ولماذا؟',
                retainDataList: [
                    'فواتير وسجلات العمليات المالية المنفذة سابقاً.',
                    'الرقم الضريبي وتفاصيل الفواتير الإلكترونية المعتمدة.',
                    '<strong>سبب الاحتفاظ:</strong> التزام قانوني إلزامي وفقاً لأنظمة هيئة الزكاة والضريبة والجمارك (ZATCA) والأنظمة المحاسبية والمالية في المملكة العربية السعودية، ولا تستخدم هذه السجلات لأي أغراض أخرى.'
                ],
                tabOtp: 'الحذف الفوري الآلي برمز التحقق (OTP)',
                tabRequest: 'تقديم طلب يدوي للدعم الفني',
                tabInApp: 'طريقة الحذف من داخل التطبيق',
                step1Label: 'إدخال رقم الجوال',
                step2Label: 'رمز التحقق والتأكيد',
                step1Title: 'أدخل رقم الجوال المسجل في التطبيق',
                step1Sub: 'سنرسل لك رمز تحقق عبر الرسائل القصيرة للتأكد من هويتك كمالك للحساب قبل الحذف النهائي.',
                lblPhone: 'رقم الجوال المسجل (مثال: 0551234567 أو +966551234567)',
                helpPhone: 'يرجى كتابة رقم الجوال المستخدم في تسجيل الدخول بتطبيق طباعة.',
                btnSendOtp: 'إرسال رمز التحقق (Send Verification Code)',
                step2Title: 'تأكيد حذف الحساب نهائياً',
                step2Sub: 'تم إرسال رمز التحقق إلى الرقم: ',
                lblOtp: 'رمز التحقق المكون من 4 أرقام (OTP Code)',
                txtWarningFinal: 'تنبيه: هذا الإجراء لا يمكن التراجع عنه. سيتم تعطيل حسابك وإلغاء تسجيل الدخول فوراً.',
                btnConfirm: 'تأكيد حذف الحساب نهائياً (Permanently Delete)',
                btnCancel: 'إلغاء أو تعديل الرقم',
                successTitle: 'تم حذف الحساب بنجاح',
                successMsg: 'تم حذف حسابك وإلغاء تفعيل كافة الجلسات وحذف البيانات الشخصية والملفات المرتبطة به طبقاً لسياسة الخصوصية. شكراً لاستخدامك تطبيق طباعة.',
                btnReturnHome: 'العودة للرئيسية',
                manualTitle: 'تقديم طلب حذف الحساب يدوياً للإدارة',
                manualSub: 'إذا كنت تواجه صعوبة في استقبال رمز التحقق أو فقدت إمكانية الوصول لهاتفك، املأ هذا النموذج وسيقوم فريق الدعم بالتحقق ومعالجة طلبك خلال 48 ساعة.',
                lblManualName: 'الاسم الكامل',
                lblManualPhone: 'رقم الجوال المسجل بالحساب',
                lblManualEmail: 'البريد الإلكتروني (اختياري لتأكيد الإنجاز)',
                lblManualReason: 'سبب حذف الحساب (اختياري)',
                btnSubmitManual: 'إرسال طلب الحذف (Submit Request)',
                inAppTitle: 'خطوات حذف الحساب مباشرة من داخل التطبيق',
                inAppSub: 'يمكنك في أي وقت حذف حسابك بنقرة زر واحدة عبر تطبيق iOS أو Android:',
                inAppStep1Title: 'افتح تطبيق طباعة (Taba3a)',
                inAppStep1Desc: 'قم بتشغيل التطبيق على هاتفك المحمول وتأكد من تسجيل الدخول بحسابك.',
                inAppStep2Title: 'انتقل إلى "الملف الشخصي" (Profile)',
                inAppStep2Desc: 'اضغط على أيقونة الحساب / الملف الشخصي في الشريط السفلي للتطبيق.',
                inAppStep3Title: 'اختر "إعدادات الحساب" ثم "حذف الحساب"',
                inAppStep3Desc: 'انزل إلى أسفل الصفحة واضغط على زر "حذف الحساب نهائياً" (Delete Account) الموضح باللون الأحمر.',
                inAppStep4Title: 'تأكيد الحذف',
                inAppStep4Desc: 'قم بقراءة تنبيه الحذف واضغط "تأكيد الحذف" لإلغاء الحساب فوراً ومسح الجلسة والبيانات.',
                ftHome: 'الرئيسية',
                ftPrivacy: 'سياسة الخصوصية',
                ftDelete: 'حذف الحساب',
                ftAdmin: 'بوابة الإدارة',
                txtPrivacyNav: 'سياسة الخصوصية'
            },
            en: {
                badge: 'Account & Data Deletion Policy & Flow',
                heroTitle: 'Request Account & Data Deletion',
                heroSubtitle: 'In compliance with Google Play Store policies, Apple App Store Guidelines, and Saudi Personal Data Protection Law (PDPL), you can permanently delete your account here and review which data is deleted and what must be legally retained.',
                delDataTitle: 'What data will be permanently deleted?',
                delDataList: [
                    'Personal profile details (Full Name, Phone Number, Email).',
                    'All active authentication tokens and sessions (Revoked).',
                    'Device push notification tokens (FCM Tokens).',
                    'Uploaded unprinted documents and temporary files immediately.',
                    'Saved addresses and personal preferences.'
                ],
                retainDataTitle: 'What data is retained and why?',
                retainDataList: [
                    'Completed financial transaction records and invoices.',
                    'Tax identification numbers and authorized e-invoices.',
                    '<strong>Retention Reason:</strong> Mandatory legal and regulatory obligation under Saudi ZATCA and statutory financial laws. Retained logs are never used for any secondary purpose.'
                ],
                tabOtp: 'Instant Automated Deletion (SMS OTP)',
                tabRequest: 'Manual Support Deletion Request',
                tabInApp: 'In-App Deletion Instructions',
                step1Label: 'Enter Phone Number',
                step2Label: 'Verify Code & Confirm',
                step1Title: 'Enter Registered Phone Number',
                step1Sub: 'We will send a one-time verification SMS code to ensure you own the account before permanent deletion.',
                lblPhone: 'Registered Phone Number (e.g. 0551234567 or +966551234567)',
                helpPhone: 'Enter the phone number linked to your Taba3a app account.',
                btnSendOtp: 'Send Verification Code (SMS OTP)',
                step2Title: 'Confirm Permanent Deletion',
                step2Sub: 'Verification code sent to phone number: ',
                lblOtp: '4-Digit Verification Code (OTP Code)',
                txtWarningFinal: 'Warning: This action cannot be undone. Your account will be deactivated and sessions terminated immediately.',
                btnConfirm: 'Permanently Delete Account',
                btnCancel: 'Cancel or Change Phone',
                successTitle: 'Account Deleted Successfully',
                successMsg: 'Your account has been deleted, all sessions revoked, and personal data purged in accordance with our Privacy Policy. Thank you for using Taba3a.',
                btnReturnHome: 'Back to Home',
                manualTitle: 'Submit Manual Deletion Request',
                manualSub: 'If you cannot receive SMS codes or lost phone access, submit this request and our support team will verify and process deletion within 48 hours.',
                lblManualName: 'Full Name',
                lblManualPhone: 'Registered Phone Number',
                lblManualEmail: 'Email Address (optional, for confirmation)',
                lblManualReason: 'Reason for Leaving (optional)',
                btnSubmitManual: 'Submit Deletion Request',
                inAppTitle: 'How to Delete Your Account In-App',
                inAppSub: 'You can delete your account directly at any time inside the iOS or Android mobile app:',
                inAppStep1Title: 'Open Taba3a App',
                inAppStep1Desc: 'Launch the application on your smartphone and log into your account.',
                inAppStep2Title: 'Go to Profile',
                inAppStep2Desc: 'Tap on your Profile icon on the bottom navigation bar.',
                inAppStep3Title: 'Select "Account Settings" then "Delete Account"',
                inAppStep3Desc: 'Scroll down to the bottom and click on the red "Delete Account" button.',
                inAppStep4Title: 'Confirm Deletion',
                inAppStep4Desc: 'Review the prompt and tap "Confirm Deletion" to immediately purge your session and account.',
                ftHome: 'Home',
                ftPrivacy: 'Privacy Policy',
                ftDelete: 'Delete Account',
                ftAdmin: 'Admin Portal',
                txtPrivacyNav: 'Privacy Policy'
            }
        };

        function switchLanguage(lang) {
            currentLang = lang;
            const t = translations[lang];
            const html = document.documentElement;

            document.getElementById('btnAr').classList.toggle('active', lang === 'ar');
            document.getElementById('btnEn').classList.toggle('active', lang === 'en');

            html.setAttribute('lang', lang);
            html.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');

            document.getElementById('badgeText').textContent = t.badge;
            document.getElementById('heroTitle').textContent = t.heroTitle;
            document.getElementById('heroSubtitle').textContent = t.heroSubtitle;
            document.getElementById('delDataTitle').textContent = t.delDataTitle;
            document.getElementById('retainDataTitle').textContent = t.retainDataTitle;

            // Update list items
            const delList = document.getElementById('delDataList');
            delList.innerHTML = t.delDataList.map(item => `<li>${item}</li>`).join('');

            const retainList = document.getElementById('retainDataList');
            retainList.innerHTML = t.retainDataList.map(item => `<li>${item}</li>`).join('');

            document.getElementById('tabOtpLabel').textContent = t.tabOtp;
            document.getElementById('tabRequestLabel').textContent = t.tabRequest;
            document.getElementById('tabInAppLabel').textContent = t.tabInApp;

            document.getElementById('step1Label').textContent = t.step1Label;
            document.getElementById('step2Label').textContent = t.step2Label;
            document.getElementById('step1Title').textContent = t.step1Title;
            document.getElementById('step1Sub').textContent = t.step1Sub;
            document.getElementById('lblPhone').textContent = t.lblPhone;
            document.getElementById('helpPhone').textContent = t.helpPhone;
            document.getElementById('btnSendOtpText').textContent = t.btnSendOtp;

            document.getElementById('step2Title').textContent = t.step2Title;
            document.getElementById('step2Sub').textContent = t.step2Sub;
            document.getElementById('lblOtp').textContent = t.lblOtp;
            document.getElementById('txtWarningFinal').textContent = t.txtWarningFinal;
            document.getElementById('btnConfirmText').textContent = t.btnConfirm;
            document.getElementById('btnCancelText').textContent = t.btnCancel;

            document.getElementById('successTitle').textContent = t.successTitle;
            document.getElementById('successMsg').textContent = t.successMsg;
            document.getElementById('btnReturnHome').textContent = t.btnReturnHome;

            document.getElementById('manualTitle').textContent = t.manualTitle;
            document.getElementById('manualSub').textContent = t.manualSub;
            document.getElementById('lblManualName').textContent = t.lblManualName;
            document.getElementById('lblManualPhone').textContent = t.lblManualPhone;
            document.getElementById('lblManualEmail').textContent = t.lblManualEmail;
            document.getElementById('lblManualReason').textContent = t.lblManualReason;
            document.getElementById('btnSubmitManualText').textContent = t.btnSubmitManual;

            document.getElementById('inAppTitle').textContent = t.inAppTitle;
            document.getElementById('inAppSub').textContent = t.inAppSub;
            document.getElementById('inAppStep1Title').textContent = t.inAppStep1Title;
            document.getElementById('inAppStep1Desc').textContent = t.inAppStep1Desc;
            document.getElementById('inAppStep2Title').textContent = t.inAppStep2Title;
            document.getElementById('inAppStep2Desc').textContent = t.inAppStep2Desc;
            document.getElementById('inAppStep3Title').textContent = t.inAppStep3Title;
            document.getElementById('inAppStep3Desc').textContent = t.inAppStep3Desc;
            document.getElementById('inAppStep4Title').textContent = t.inAppStep4Title;
            document.getElementById('inAppStep4Desc').textContent = t.inAppStep4Desc;

            document.getElementById('ftHome').textContent = t.ftHome;
            document.getElementById('ftPrivacy').textContent = t.ftPrivacy;
            document.getElementById('ftDelete').textContent = t.ftDelete;
            document.getElementById('ftAdmin').textContent = t.ftAdmin;
            document.getElementById('txtPrivacyNav').textContent = t.txtPrivacyNav;
        }

        function switchTab(tab) {
            hideAlert();
            document.getElementById('tabOtpBtn').classList.toggle('active', tab === 'otp');
            document.getElementById('tabRequestBtn').classList.toggle('active', tab === 'request');
            document.getElementById('tabInAppBtn').classList.toggle('active', tab === 'inApp');

            document.getElementById('tabOtp').style.display = tab === 'otp' ? 'block' : 'none';
            document.getElementById('tabRequest').style.display = tab === 'request' ? 'block' : 'none';
            document.getElementById('tabInApp').style.display = tab === 'inApp' ? 'block' : 'none';
        }

        function showAlert(type, message) {
            const alertBox = document.getElementById('alertBox');
            const alertIcon = document.getElementById('alertIcon');
            const alertMsg = document.getElementById('alertMessage');

            alertBox.className = 'alert-box ' + type;
            alertMsg.innerHTML = message;

            if (type === 'success') {
                alertIcon.className = 'fa-solid fa-circle-check';
            } else if (type === 'error') {
                alertIcon.className = 'fa-solid fa-circle-xmark';
            } else {
                alertIcon.className = 'fa-solid fa-circle-info';
            }

            alertBox.style.display = 'flex';
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function hideAlert() {
            document.getElementById('alertBox').style.display = 'none';
        }

        async function handleSendOtp(e) {
            e.preventDefault();
            hideAlert();

            const phone = document.getElementById('phoneInput').value.trim();
            const btn = document.getElementById('btnSendOtp');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>جاري التحقق وإرسال الرمز...</span>';

            try {
                const response = await fetch("{{ route('account.delete.send-otp') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ phone: phone })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    verifiedPhone = data.phone || phone;
                    document.getElementById('displayPhone').textContent = verifiedPhone;

                    // If dev OTP present in debug mode, prefill or alert for testing
                    if (data.dev_otp) {
                        showAlert('info', (currentLang === 'ar' ? 'وضع الاختبار التجريبي: رمز التحقق هو ' : 'Debug mode: Test OTP is ') + '<strong>' + data.dev_otp + '</strong>');
                        document.getElementById('otpInput').value = data.dev_otp;
                    } else {
                        showAlert('success', currentLang === 'ar' ? data.message : data.message_en);
                    }

                    // Move to Step 2
                    document.getElementById('step1Container').style.display = 'none';
                    document.getElementById('step2Container').style.display = 'block';
                    document.getElementById('step1Indicator').className = 'step-node done';
                    document.getElementById('step2Indicator').className = 'step-node active';
                } else {
                    showAlert('error', (currentLang === 'ar' ? (data.message || 'حدث خطأ أثناء المعالجة') : (data.message_en || 'Error processing request')));
                }
            } catch (err) {
                showAlert('error', currentLang === 'ar' ? 'تعذر الاتصال بالخادم، يرجى المحاولة لاحقاً.' : 'Network error, please try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        async function handleConfirmDelete(e) {
            e.preventDefault();
            hideAlert();

            const otp = document.getElementById('otpInput').value.trim();
            const btn = document.getElementById('btnConfirmDelete');
            const originalText = btn.innerHTML;

            if (otp.length !== 4) {
                showAlert('error', currentLang === 'ar' ? 'يرجى إدخال رمز التحقق المكون من 4 أرقام.' : 'Please enter the 4-digit verification code.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>جاري تأكيد الحذف...</span>';

            try {
                const response = await fetch("{{ route('account.delete.confirm') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        phone: verifiedPhone,
                        otp_code: otp
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    document.getElementById('step2Container').style.display = 'none';
                    document.getElementById('stepSuccessContainer').style.display = 'block';
                    document.getElementById('step2Indicator').className = 'step-node done';
                    showAlert('success', currentLang === 'ar' ? data.message : data.message_en);
                } else {
                    showAlert('error', (currentLang === 'ar' ? (data.message || 'رمز التحقق غير صحيح') : (data.message_en || 'Invalid verification code')));
                }
            } catch (err) {
                showAlert('error', currentLang === 'ar' ? 'تعذر الاتصال بالخادم، يرجى المحاولة لاحقاً.' : 'Network error, please try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        function cancelStep2() {
            document.getElementById('step2Container').style.display = 'none';
            document.getElementById('step1Container').style.display = 'block';
            document.getElementById('step1Indicator').className = 'step-node active';
            document.getElementById('step2Indicator').className = 'step-node';
            hideAlert();
        }

        async function handleManualRequest(e) {
            e.preventDefault();
            hideAlert();

            const name = document.getElementById('manualName').value.trim();
            const phone = document.getElementById('manualPhone').value.trim();
            const email = document.getElementById('manualEmail').value.trim();
            const reason = document.getElementById('manualReason').value.trim();
            const btn = document.getElementById('btnSubmitManual');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>جاري إرسال الطلب...</span>';

            try {
                const response = await fetch("{{ route('account.delete.request') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name,
                        phone: phone,
                        email: email,
                        reason: reason
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showAlert('success', currentLang === 'ar' ? data.message : data.message_en);
                    document.getElementById('formManualRequest').reset();
                } else {
                    showAlert('error', (currentLang === 'ar' ? (data.message || 'تعذر إرسال الطلب') : (data.message_en || 'Failed to submit request')));
                }
            } catch (err) {
                showAlert('error', currentLang === 'ar' ? 'تعذر الاتصال بالخادم، يرجى المحاولة لاحقاً.' : 'Network error, please try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    </script>
</body>
</html>
