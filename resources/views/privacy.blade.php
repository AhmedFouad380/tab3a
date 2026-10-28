<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="سياسة الخصوصية وحماية البيانات - تطبيق ومنصة طباعة (Taba3a) لخدمات الطباعة الفورية والذكية. Privacy Policy for Taba3a App.">
    <title>سياسة الخصوصية | تطبيق طباعة - Taba3a Privacy Policy</title>

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
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
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
                radial-gradient(circle at 15% 15%, rgba(37, 99, 235, 0.15) 0%, transparent 45%),
                radial-gradient(circle at 85% 30%, rgba(6, 182, 212, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 50% 85%, rgba(99, 102, 241, 0.1) 0%, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Number Font */
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
            max-width: 1100px;
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
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.3rem;
            box-shadow: 0 0 20px var(--primary-glow);
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
            cursor: pointer;
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
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 2px 10px var(--primary-glow);
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
            color: #38bdf8;
        }

        /* Hero Banner */
        .hero-banner {
            padding: 3.5rem 0 2rem;
            text-align: center;
            position: relative;
        }

        .badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 9999px;
            background: rgba(37, 99, 235, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #60a5fa;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.25;
            background: linear-gradient(135deg, #ffffff 30%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: var(--text-secondary);
            max-width: 680px;
            margin: 0 auto;
        }

        .meta-strip {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            margin-top: 1.75rem;
            font-size: 0.88rem;
            color: var(--text-muted);
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Highlight Card */
        .security-guarantee-card {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.15), rgba(6, 182, 212, 0.1));
            border: 1px solid rgba(59, 130, 246, 0.35);
            border-radius: 20px;
            padding: 1.75rem 2rem;
            margin: 2rem 0;
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(12px);
        }

        .shield-icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .guarantee-text h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #34d399;
            margin-bottom: 0.4rem;
        }

        .guarantee-text p {
            font-size: 0.95rem;
            color: #cbd5e1;
            line-height: 1.6;
        }

        /* Content Sections */
        .content-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(16px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            transition: border-color 0.2s ease;
        }

        .content-card:hover {
            border-color: var(--border-glass-hover);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-glass);
        }

        .section-number {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(37, 99, 235, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #60a5fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            flex-shrink: 0;
        }

        .section-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #ffffff;
        }

        .section-body {
            color: #cbd5e1;
            font-size: 1rem;
            line-height: 1.8;
        }

        .section-body p {
            margin-bottom: 1rem;
        }

        .section-body p:last-child {
            margin-bottom: 0;
        }

        .bullet-list {
            list-style: none;
            margin: 1rem 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .bullet-list li {
            position: relative;
            padding-right: 1.75rem;
            font-size: 0.98rem;
            color: #cbd5e1;
        }

        [dir="ltr"] .bullet-list li {
            padding-right: 0;
            padding-left: 1.75rem;
        }

        .bullet-list li::before {
            content: "✓";
            position: absolute;
            right: 0;
            top: 0;
            color: #38bdf8;
            font-weight: 800;
        }

        [dir="ltr"] .bullet-list li::before {
            right: auto;
            left: 0;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
            margin: 1.25rem 0;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-glass);
            border-radius: 14px;
            padding: 1.25rem;
            transition: background 0.2s;
        }

        .info-box:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .info-box-header {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #60a5fa;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .info-box-desc {
            font-size: 0.92rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .badge-warning-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.25);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-top: 1rem;
            font-size: 0.92rem;
            color: #fde68a;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: #ffffff;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            box-shadow: 0 4px 15px var(--primary-glow);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
        }

        .btn-outline-danger {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .btn-outline-danger:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: #ef4444;
            color: #fca5a5;
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
            color: #38bdf8;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.85rem;
            }
            .content-card {
                padding: 1.5rem;
            }
            .security-guarantee-card {
                flex-direction: column;
                padding: 1.25rem;
            }
            .meta-strip {
                gap: 1rem;
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
                        <i class="fa-solid fa-print"></i>
                    </div>
                    <div>
                        <div class="brand-title">منصة طباعة</div>
                        <div style="font-size: 0.72rem; color: var(--text-muted);" class="num-font">Taba3a Smart Printing</div>
                    </div>
                </a>

                <div class="nav-actions">
                    <!-- Language Selector (Ar / En) -->
                    <div class="lang-switch" role="tablist">
                        <button type="button" class="lang-btn active" id="btnAr" onclick="switchLanguage('ar')">العربية</button>
                        <button type="button" class="lang-btn" id="btnEn" onclick="switchLanguage('en')">English</button>
                    </div>

                    <a href="{{ url('/landing') }}" class="back-link">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span id="txtBackNav">الرئيسية</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container" style="flex: 1; padding-bottom: 3rem;">
        
        <!-- Hero Section -->
        <section class="hero-banner">
            <div class="badge-tag">
                <i class="fa-solid fa-shield-halved"></i>
                <span id="badgeText">وثيقة الخصوصية وحماية بيانات المستخدمين</span>
            </div>
            <h1 class="hero-title" id="heroTitle">سياسة الخصوصية وسرية البيانات</h1>
            <p class="hero-subtitle" id="heroSubtitle">
                نحن في تطبيق ومنصة طباعة (Taba3a) نلتزم بأعلى معايير الأمان وحماية خصوصية بياناتك ومستنداتك المرفوعة للطباعة وفقاً للأنظمة المعمول بها في المملكة العربية السعودية ومتطلبات متجري Apple و Google.
            </p>

            <div class="meta-strip num-font">
                <div class="meta-item">
                    <i class="fa-regular fa-calendar-check" style="color: #60a5fa;"></i>
                    <span id="metaUpdated">آخر تحديث: أكتوبر 2026</span>
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-globe" style="color: #34d399;"></i>
                    <span id="metaScope">ساري على كافة مستخدمي التطبيق وأجهزة الخدمة الذاتية</span>
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-file-shield" style="color: #06b6d4;"></i>
                    <span id="metaCompliant">متوافق مع معايير App Store & Google Play</span>
                </div>
            </div>
        </section>

        <!-- Critical Guarantee Highlight Box (Special for Print Document Privacy) -->
        <div class="security-guarantee-card">
            <div class="shield-icon-wrap">
                <i class="fa-solid fa-lock"></i>
            </div>
            <div class="guarantee-text">
                <h3 id="guaranteeTitle">ضمان أمان وسرية المستندات والمسح التلقائي الفوري</h3>
                <p id="guaranteeDesc">
                    تعتبر ملفاتك ومستنداتك المرفوعة للطباعة مشفرة بالكامل أثناء النقل والمعالجة. لا يقوم أي فرد من فريق العمل بالاطلاع على محتوى ملفاتك، <strong>ويتم حذف ملفات الطباعة تلقائياً ونهائياً من خوادمنا فور إتمام عملية الطباعة بنجاح</strong> أو بعد انقضاء جلسة الطباعة دون استرجاع.
                </p>
            </div>
        </div>

        <!-- ================= Arabic Content ================= -->
        <div id="contentAr">
            
            <!-- 1. Introduction -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">1</div>
                    <h2 class="section-title">المقدمة والجهة المسؤولة عن معالجة البيانات</h2>
                </div>
                <div class="section-body">
                    <p>
                        أهلاً بكم في <strong>منصة وتطبيق طباعة (Taba3a)</strong> ("نحن" أو "المنصة"). نحن نوفر حلول الطباعة الرقمية والذاتية عبر شبكة ماكينات الخدمة الذاتية (Kiosks) والفروع المعتمدة في المملكة العربية السعودية.
                    </p>
                    <p>
                        توضح هذه السياسة كيفية جمعنا واستخدامنا وحمايتنا لبياناتكم الشخصية والمستندات التي تقومون برفعها عبر التطبيق أو أجهزة الخدمة الذاتية، والتزامنا الصارم بـ <strong>نظام حماية البيانات الشخصية في المملكة العربية السعودية</strong>، ولوائح الخصوصية لمتجري <strong>Apple App Store</strong> و <strong>Google Play</strong>.
                    </p>
                </div>
            </div>

            <!-- 2. Data We Collect -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">2</div>
                    <h2 class="section-title">البيانات والمعلومات التي نقوم بجمعها</h2>
                </div>
                <div class="section-body">
                    <p>نقوم بجمع البيانات الضرورية فقط لتقديم وتسهيل تجربة الطباعة السريعة والآمنة:</p>
                    
                    <div class="grid-2">
                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-user-check"></i>
                                <span>معلومات الحساب والتعريف</span>
                            </div>
                            <div class="info-box-desc">
                                الاسم، رقم الهاتف المحمول (يستخدم للتحقق عبر رمز التحقق OTP وتسجيل الدخول)، وعنوان البريد الإلكتروني (اختياري لاستلام الفواتير والإشعارات).
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>المستندات والملفات المرفوعة</span>
                            </div>
                            <div class="info-box-desc">
                                ملفات PDF أو الصور أو المستندات المرفوعة بغرض تجهيزها وطباعتها على ماكينات الخدمة الذاتية أو استلامها من الفرع.
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>بيانات الموقع الجغرافي (GPS)</span>
                            </div>
                            <div class="info-box-desc">
                                نطلب إذن الموقع الجغرافي بموافقتك الصريحة فقط لعرض أقرب ماكينات الخدمة الذاتية (Kiosks) والفروع إليك وحساب المسافة. لا نتتبع موقعك في الخلفية.
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-credit-card"></i>
                                <span>بيانات المدفوعات والمعاملات</span>
                            </div>
                            <div class="info-box-desc">
                                تفاصيل طلبات الطباعة وتاريخ العمليات ورصيد المحفظة. تتم عمليات الدفع الإلكتروني عبر بوابات دفع بنكية معتمدة (مدى، Apple Pay، فيزا)، ولا نقوم بحفظ أرقام البطاقات البنكية في خوادمنا.
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-mobile-screen"></i>
                                <span>معلومات الجهاز والتشخيص</span>
                            </div>
                            <div class="info-box-desc">
                                نوع الجهاز، نظام التشغيل (iOS / Android)، معرفات الإشعارات (FCM Token) لإرسال حالة اكتمال الطباعة، وسجلات الأخطاء البرمجية لتحسين الأداء.
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-building"></i>
                                <span>بيانات الشركات والمؤسسات</span>
                            </div>
                            <div class="info-box-desc">
                                في حال تسجيل حساب تجاري: اسم المنشأة، والرقم الضريبي لإصدار الفواتير الضريبية المتوافقة مع متطلبات هيئة الزكاة والضريبة والجمارك (ZATCA).
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. How We Use Your Data -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">3</div>
                    <h2 class="section-title">كيف نستخدم بياناتك الشخصية؟</h2>
                </div>
                <div class="section-body">
                    <p>نستخدم البيانات التي نجمعها للأغراض التشغيلية المشروعة التالية فقط:</p>
                    <ul class="bullet-list">
                        <li><strong>تنفيذ وطباعة الطلبات:</strong> إرسال المستندات إلى ماكينة الطباعة الذاتية المحددة أو فرع الطباعة بناءً على طلبك ومواصفاتك.</li>
                        <li><strong>التحقق والدخول الآمن:</strong> إرسال رموز التحقق عبر الرسائل النصية القصيرة (SMS OTP) للتحقق من هوية صاحب الرقم وحماية الحساب.</li>
                        <li><strong>التنبيهات وتتبع الطلبات:</strong> إشعارك فور جاهزية الملف للطباعة أو عند انتهاء تنفيذ أمر الطباعة.</li>
                        <li><strong>تحديد وتوجيه المواقع:</strong> تمكينك من اختيار أقرب كشك طباعة ذاتية أو نقطة استلام.</li>
                        <li><strong>خدمة العملاء والدعم الفني:</strong> الرد على استفساراتك وحل المشكلات الفنية أثناء الاستخدام.</li>
                        <li><strong>الامتثال القانوني والضريبي:</strong> إصدار الفواتير الإلكترونية المعتمدة والاحتفاظ بسجلات المعاملات المالية طبقاً للأنظمة المالية في المملكة.</li>
                    </ul>
                </div>
            </div>

            <!-- 4. Security & Document Retention (Critical for Apple/Google) -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">4</div>
                    <h2 class="section-title">أمن وسرية المستندات وسياسة الحذف التلقائي</h2>
                </div>
                <div class="section-body">
                    <p>
                        نحن نعي تماماً الحساسية البالغة للمستندات والملفات الورقية والشخصية التي تقوم برفعها. لذلك نتبع الإجراءات الأمنية التالية:
                    </p>
                    <ul class="bullet-list">
                        <li><strong>التشفير الكامل:</strong> يتم نقل جميع المستندات باستخدام بروتوكولات التشفير المتقدمة (TLS/SSL 256-bit) لضمان عدم اعتراضها أو الاطلاع عليها أثناء النقل.</li>
                        <li><strong>المعالجة الآلية بدون تدخل بشري:</strong> تتم معالجة المستندات وحساب عدد الصفحات وتنسيقها برمجياً دون تدخل أي عنصر بشري.</li>
                        <li><strong>الحذف التلقائي الفوري:</strong> يتم مسح وحذف الملفات المرفوعة نهائياً من خوادم المنصة وماكينات الخدمة الذاتية فور انتهاء عملية الطباعة بنجاح، أو خلال مدة أقصاها 24 ساعة للطلبات غير المكتملة، ولا نحتفظ بنسخ احتياطية منها.</li>
                        <li><strong>عزل الجلسات:</strong> تقتصر إمكانية إرسال أمر الطباعة إلى الماكينة على حامل رمز الـ QR أو كود الجلسة المرتبط بحسابك فقط.</li>
                    </ul>
                </div>
            </div>

            <!-- 5. Third-Party Sharing -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">5</div>
                    <h2 class="section-title">مشاركة البيانات مع أطراف ثالثة</h2>
                </div>
                <div class="section-body">
                    <p>
                        <strong>نحن لا نبيع أو نؤجر أو نتاجر ببياناتك الشخصية أو ملفاتك المرفوعة لأي طرف ثالث أو لأغراض تسويقية إطلاقاً.</strong>
                    </p>
                    <p>نشارك البيانات بالقدر الأدنى الضروري فقط مع شركاء الخدمة المعتمدين والموثوقين لتشغيل الخدمة:</p>
                    <ul class="bullet-list">
                        <li><strong>بوابات الدفع الإلكتروني المعتمدة:</strong> لمعالجة مدفوعاتك بأمان وتشفير تام (مثل مدى، Apple Pay، فيزا).</li>
                        <li><strong>مزودي خدمات الرسائل النصية القصيرة (SMS Gateways):</strong> لإرسال رموز التحقق لمرة واحدة (OTP) لتسجيل الدخول.</li>
                        <li><strong>مزودي البنية السحابية والخوادم:</strong> لاستضافة المنصة في بيئات سحابية معتمدة وآمنة ذات كفاءة وموثوقية عالية.</li>
                        <li><strong>الجهات النظامية والرسمية:</strong> فقط عند الإلزام بموجب أمر قضائي أو متطلب نظامي واجب النفاذ في المملكة العربية السعودية.</li>
                    </ul>
                </div>
            </div>

            <!-- 6. Account Deletion & User Rights (Google Play Requirement) -->
            <div class="content-card" style="border: 1px solid rgba(239, 68, 68, 0.3);">
                <div class="section-header">
                    <div class="section-number" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.4); color: #f87171;">6</div>
                    <h2 class="section-title">حقوق المستخدم وسياسة حذف الحساب والبيانات</h2>
                </div>
                <div class="section-body">
                    <p>
                        وفقاً لأنظمة حماية البيانات ومتطلبات متجري Apple و Google، يحق لك في أي وقت ممارسة حقوقك التالية:
                    </p>
                    <ul class="bullet-list">
                        <li><strong>حق الوصول والاطلاع:</strong> معرفة البيانات المسجلة باسمك ومراجعة سجل طلباتك ومعاملاتك.</li>
                        <li><strong>حق التصحيح والتعديل:</strong> تحديث بيانات ملفك الشخصي ورقم هاتفك واسمك من خلال التطبيق.</li>
                        <li><strong>حق حذف الحساب نهائياً:</strong> يحق لك طلب حذف حسابك وكافة البيانات المرتبطة به في أي وقت.</li>
                    </ul>

                    <div class="badge-warning-box">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.25rem; flex-shrink: 0; margin-top: 2px;"></i>
                        <div>
                            <strong>ماذا يحدث عند حذف حسابك؟</strong><br>
                            يتم إبطال جميع جلسات الدخول، وإلغاء صلاحية الرموز المميزة، وحذف بيانات الملف الشخصي، وإزالة أي ملفات مؤقتة غير مطبوعة. نحتفظ فقط بسجلات الفواتير والمدفوعات السابقة المنفذة للامتثال للمتطلبات الضريبية والمحاسبية الإلزامية في المملكة العربية السعودية (ZATCA).
                        </div>
                    </div>

                    <div style="margin-top: 1.75rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                        <a href="{{ url('/delete-account') }}" class="btn-outline-danger">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>الانتقال لصفحة حذف الحساب (Delete Account)</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 7. Children Privacy -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">7</div>
                    <h2 class="section-title">خصوصية الأطفال والقُصّر</h2>
                </div>
                <div class="section-body">
                    <p>
                        خدماتنا مخصصة للاستخدام العام وللطلاب والأفراد والشركات. نحن لا نقوم عن قصد أو دراية بجمع بيانات شخصية من أطفال دون سن 13 عاماً بدون موافقة ولي الأمر أو الوصي الشرعي. إذا علمنا بجمع بيانات قاصر دون موافقة نظامية، سنقوم بحذفها على الفور.
                    </p>
                </div>
            </div>

            <!-- 8. Updates to this Policy -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">8</div>
                    <h2 class="section-title">التعديلات على سياسة الخصوصية</h2>
                </div>
                <div class="section-body">
                    <p>
                        قد نقوم بتحديث سياسة الخصوصية هذه من وقت لآخر لتعكس التغييرات في ممارساتنا التشغيلية أو التوافق مع الأنظمة القانونية والمتطلبات التقنية الجديدة. سنقوم بنشر النسخة المحدثة وتحديث تاريخ "آخر تحديث" في أعلى هذه الصفحة، وننصح بمراجعتها دورياً.
                    </p>
                </div>
            </div>

            <!-- 9. Contact Us -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">9</div>
                    <h2 class="section-title">التواصل معنا ومسؤول حماية البيانات</h2>
                </div>
                <div class="section-body">
                    <p>إذا كانت لديك أي استفسارات أو ملاحظات أو شكاوى تتعلق بسياسة الخصوصية أو معالجة بياناتك، يمكنك التواصل معنا مباشرة عبر القنوات التالية:</p>
                    
                    <div class="grid-2" style="margin-top: 1rem;">
                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-envelope"></i>
                                <span>البريد الإلكتروني للدعم والخصوصية</span>
                            </div>
                            <div class="info-box-desc num-font">
                                <a href="mailto:support@taba3a.com" style="color: #38bdf8; text-decoration: none;">support@taba3a.com</a>
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-phone"></i>
                                <span>خدمة العملاء الهاتفية</span>
                            </div>
                            <div class="info-box-desc num-font">
                                <a href="tel:+966500000000" style="color: #38bdf8; text-decoration: none;">+966 50 000 0000</a>
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-map-pin"></i>
                                <span>المقر الرئيسي</span>
                            </div>
                            <div class="info-box-desc">
                                المملكة العربية السعودية - الرياض
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-globe"></i>
                                <span>الموقع الرسمي</span>
                            </div>
                            <div class="info-box-desc num-font">
                                <a href="{{ url('/') }}" style="color: #38bdf8; text-decoration: none;">taba3a.com</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= English Content (For Apple & Google Store Review) ================= -->
        <div id="contentEn" style="display: none;" dir="ltr">
            
            <!-- 1. Introduction -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">1</div>
                    <h2 class="section-title">Introduction & Data Controller</h2>
                </div>
                <div class="section-body">
                    <p>
                        Welcome to <strong>Taba3a Smart Printing Platform & Mobile Application</strong> ("we", "us", or "our"). We provide smart, on-demand digital printing solutions across a network of self-service kiosks and authorized branches in the Kingdom of Saudi Arabia.
                    </p>
                    <p>
                        This Privacy Policy outlines how we collect, use, process, and safeguard your personal data and documents uploaded via our mobile application and self-service kiosks, strictly adhering to the <strong>Personal Data Protection Law (PDPL) in Saudi Arabia</strong> and the developer policies of <strong>Apple App Store</strong> and <strong>Google Play</strong>.
                    </p>
                </div>
            </div>

            <!-- 2. Information We Collect -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">2</div>
                    <h2 class="section-title">Information & Data We Collect</h2>
                </div>
                <div class="section-body">
                    <p>We only collect data strictly necessary to deliver a fast, reliable, and secure printing experience:</p>
                    
                    <div class="grid-2">
                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-user-check"></i>
                                <span>Account & Identity Data</span>
                            </div>
                            <div class="info-box-desc">
                                Full Name, Mobile Phone Number (used for SMS OTP verification and authentication), and Email address (optional, for electronic receipts and notifications).
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>Uploaded Files & Documents</span>
                            </div>
                            <div class="info-box-desc">
                                PDF documents, images, and files uploaded exclusively for the purpose of printing at self-service kiosks or branch pickup.
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>Geolocation Data (GPS)</span>
                            </div>
                            <div class="info-box-desc">
                                With your explicit permission, we access your device location to display the nearest self-service kiosks and print branches. We do not track your location in the background.
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-credit-card"></i>
                                <span>Payments & Transactions</span>
                            </div>
                            <div class="info-box-desc">
                                Print order history, wallet balance, and invoices. Online payments are handled by certified, PCI-DSS compliant payment gateways (Mada, Apple Pay, Visa). We never store raw credit card numbers.
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-mobile-screen"></i>
                                <span>Device & Diagnostics</span>
                            </div>
                            <div class="info-box-desc">
                                Device model, OS version (iOS / Android), push notification tokens (FCM Token) to alert you of print completion, and anonymized crash logs for app stability.
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-building"></i>
                                <span>Corporate & Business Data</span>
                            </div>
                            <div class="info-box-desc">
                                For business accounts: Entity name and Tax Identification Number (VAT/TRN) for issuing tax invoices compliant with Saudi ZATCA regulations.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. How We Use Data -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">3</div>
                    <h2 class="section-title">How We Use Your Personal Data</h2>
                </div>
                <div class="section-body">
                    <p>We process your data strictly for legitimate operational purposes:</p>
                    <ul class="bullet-list">
                        <li><strong>Order Fulfillment & Printing:</strong> Sending your documents to the chosen kiosk or print facility according to your printing options.</li>
                        <li><strong>Authentication & Security:</strong> Sending one-time verification passwords (SMS OTP) to verify phone ownership and secure account access.</li>
                        <li><strong>Real-time Status Notifications:</strong> Keeping you updated on file processing, print readiness, and completion.</li>
                        <li><strong>Kiosk & Branch Locating:</strong> Enabling you to find nearby printing points and calculate walking/driving distances.</li>
                        <li><strong>Customer Support:</strong> Assisting you with operational inquiries and resolving technical issues.</li>
                        <li><strong>Tax & Legal Compliance:</strong> Issuing legally compliant electronic tax invoices and maintaining required audit records in KSA.</li>
                    </ul>
                </div>
            </div>

            <!-- 4. Document Security & Deletion (Apple / Google Crucial Policy) -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">4</div>
                    <h2 class="section-title">Document Security & Automatic Purge Policy</h2>
                </div>
                <div class="section-body">
                    <p>
                        We understand the utmost sensitivity and confidentiality of your documents and personal records. We enforce rigorous protection mechanisms:
                    </p>
                    <ul class="bullet-list">
                        <li><strong>End-to-End Encryption:</strong> All uploaded files are transmitted via enterprise TLS/SSL (256-bit) encryption protocols to prevent unauthorized access.</li>
                        <li><strong>Automated Processing:</strong> Files are processed, page-counted, and rendered purely by software algorithms with zero human access or intervention.</li>
                        <li><strong>Immediate Automatic Deletion:</strong> Uploaded print files are permanently and automatically purged from our servers and kiosk machines immediately upon successful printing, or within 24 hours for unprinted sessions. No backup copies are maintained.</li>
                        <li><strong>Session Token Security:</strong> File print execution at a kiosk is protected by encrypted QR codes and temporary session tokens accessible only by the account owner.</li>
                    </ul>
                </div>
            </div>

            <!-- 5. Third-Party Sharing -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">5</div>
                    <h2 class="section-title">Third-Party Data Disclosures</h2>
                </div>
                <div class="section-body">
                    <p>
                        <strong>We do not sell, rent, or trade your personal data or uploaded files to any third party for marketing or advertising purposes under any circumstances.</strong>
                    </p>
                    <p>We only share minimal necessary data with trusted operational partners:</p>
                    <ul class="bullet-list">
                        <li><strong>Certified Payment Gateways:</strong> To securely process transactions (Mada, Apple Pay, Visa, Mastercard).</li>
                        <li><strong>SMS Gateways:</strong> To deliver one-time verification passwords (OTP) for authentication.</li>
                        <li><strong>Secure Cloud Infrastructure:</strong> To host the backend system in compliant, high-security server environments.</li>
                        <li><strong>Statutory Authorities:</strong> Solely if mandated by formal judicial order or regulatory obligation under the laws of Saudi Arabia.</li>
                    </ul>
                </div>
            </div>

            <!-- 6. Account Deletion & User Rights -->
            <div class="content-card" style="border: 1px solid rgba(239, 68, 68, 0.3);">
                <div class="section-header">
                    <div class="section-number" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.4); color: #f87171;">6</div>
                    <h2 class="section-title">Account Deletion & User Rights (Google Play / Apple Compliant)</h2>
                </div>
                <div class="section-body">
                    <p>
                        In accordance with privacy standards and store guidelines, you are entitled to exercise your data rights at any time:
                    </p>
                    <ul class="bullet-list">
                        <li><strong>Right to Access:</strong> View and review all information, transaction history, and orders associated with your account.</li>
                        <li><strong>Right to Rectification:</strong> Update or modify your profile data and preferences through the app.</li>
                        <li><strong>Right to Permanent Account Deletion:</strong> You have the right to request full deletion of your account and related personal records.</li>
                    </ul>

                    <div class="badge-warning-box">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.25rem; flex-shrink: 0; margin-top: 2px;"></i>
                        <div>
                            <strong>What happens when your account is deleted?</strong><br>
                            All active authentication tokens and sessions are revoked, personal profile data is soft/hard deleted, and any remaining temporary files are purged. Only completed financial invoices and tax audit logs are retained as required by Saudi Arabian tax authorities (ZATCA) and financial regulations.
                        </div>
                    </div>

                    <div style="margin-top: 1.75rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                        <a href="{{ url('/delete-account') }}" class="btn-outline-danger">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Go to Account Deletion Page</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 7. Children's Privacy -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">7</div>
                    <h2 class="section-title">Children's Privacy</h2>
                </div>
                <div class="section-body">
                    <p>
                        Our platform is designed for general audiences, students, and businesses. We do not knowingly collect personal identifiable information from children under the age of 13 without verifiable parental or guardian consent. If we become aware of any such data collected inadvertently, it will be deleted immediately.
                    </p>
                </div>
            </div>

            <!-- 8. Updates to this Policy -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">8</div>
                    <h2 class="section-title">Policy Changes & Revisions</h2>
                </div>
                <div class="section-body">
                    <p>
                        We may periodically revise this Privacy Policy to reflect operational, legal, or technical advancements. Changes become effective upon posting the revised policy with an updated revision date. We encourage regular review of this page.
                    </p>
                </div>
            </div>

            <!-- 9. Contact Us -->
            <div class="content-card">
                <div class="section-header">
                    <div class="section-number">9</div>
                    <h2 class="section-title">Contact & Data Protection Inquiries</h2>
                </div>
                <div class="section-body">
                    <p>If you have questions, feedback, or inquiries regarding your personal data or privacy practices, please contact us directly:</p>
                    
                    <div class="grid-2" style="margin-top: 1rem;">
                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-envelope"></i>
                                <span>Support & Privacy Email</span>
                            </div>
                            <div class="info-box-desc num-font">
                                <a href="mailto:support@taba3a.com" style="color: #38bdf8; text-decoration: none;">support@taba3a.com</a>
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-phone"></i>
                                <span>Customer Support Phone</span>
                            </div>
                            <div class="info-box-desc num-font">
                                <a href="tel:+966500000000" style="color: #38bdf8; text-decoration: none;">+966 50 000 0000</a>
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-map-pin"></i>
                                <span>Headquarters</span>
                            </div>
                            <div class="info-box-desc">
                                Riyadh, Kingdom of Saudi Arabia
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-header">
                                <i class="fa-solid fa-globe"></i>
                                <span>Website</span>
                            </div>
                            <div class="info-box-desc num-font">
                                <a href="{{ url('/') }}" style="color: #38bdf8; text-decoration: none;">taba3a.com</a>
                            </div>
                        </div>
                    </div>
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
                <a href="{{ url('/privacy') }}" style="color: #38bdf8; font-weight: 600;" id="ftPrivacy">سياسة الخصوصية</a>
                <span style="opacity: 0.3;">•</span>
                <a href="{{ url('/delete-account') }}" id="ftDelete">حذف الحساب</a>
                <span style="opacity: 0.3;">•</span>
                <a href="{{ url('/admin/login') }}" id="ftAdmin">بوابة الإدارة</a>
            </div>
            <div class="num-font" style="opacity: 0.7;">
                جميع الحقوق محفوظة &copy; {{ date('Y') }} منصة طباعة (Taba3a Smart Printing Platform). All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Interactive Language Switcher Script -->
    <script>
        function switchLanguage(lang) {
            const btnAr = document.getElementById('btnAr');
            const btnEn = document.getElementById('btnEn');
            const contentAr = document.getElementById('contentAr');
            const contentEn = document.getElementById('contentEn');
            const html = document.documentElement;

            if (lang === 'en') {
                btnEn.classList.add('active');
                btnAr.classList.remove('active');
                contentAr.style.display = 'none';
                contentEn.style.display = 'block';
                html.setAttribute('lang', 'en');
                html.setAttribute('dir', 'ltr');

                document.getElementById('badgeText').textContent = 'User Privacy & Data Protection Policy';
                document.getElementById('heroTitle').textContent = 'Privacy Policy & Data Confidentiality';
                document.getElementById('heroSubtitle').textContent = 'At Taba3a Smart Printing Platform, we adhere to the highest security and confidentiality standards for your personal data and uploaded printing documents, compliant with Saudi PDPL and Apple App Store & Google Play requirements.';
                document.getElementById('metaUpdated').textContent = 'Last Updated: October 2026';
                document.getElementById('metaScope').textContent = 'Applies to all App users & Self-service Kiosks';
                document.getElementById('metaCompliant').textContent = 'App Store & Google Play Compliant';
                document.getElementById('guaranteeTitle').textContent = 'Document Confidentiality & Immediate Automated Deletion Guarantee';
                document.getElementById('guaranteeDesc').textContent = 'Your uploaded print files are encrypted end-to-end. No staff member has access to your file contents, and all files are automatically and permanently purged from our servers immediately upon successful print completion.';
                document.getElementById('txtBackNav').textContent = 'Home';

                document.getElementById('ftHome').textContent = 'Home';
                document.getElementById('ftPrivacy').textContent = 'Privacy Policy';
                document.getElementById('ftDelete').textContent = 'Delete Account';
                document.getElementById('ftAdmin').textContent = 'Admin Portal';
            } else {
                btnAr.classList.add('active');
                btnEn.classList.remove('active');
                contentAr.style.display = 'block';
                contentEn.style.display = 'none';
                html.setAttribute('lang', 'ar');
                html.setAttribute('dir', 'rtl');

                document.getElementById('badgeText').textContent = 'وثيقة الخصوصية وحماية بيانات المستخدمين';
                document.getElementById('heroTitle').textContent = 'سياسة الخصوصية وسرية البيانات';
                document.getElementById('heroSubtitle').textContent = 'نحن في تطبيق ومنصة طباعة (Taba3a) نلتزم بأعلى معايير الأمان وحماية خصوصية بياناتك ومستنداتك المرفوعة للطباعة وفقاً للأنظمة المعمول بها في المملكة العربية السعودية ومتطلبات متجري Apple و Google.';
                document.getElementById('metaUpdated').textContent = 'آخر تحديث: أكتوبر 2026';
                document.getElementById('metaScope').textContent = 'ساري على كافة مستخدمي التطبيق وأجهزة الخدمة الذاتية';
                document.getElementById('metaCompliant').textContent = 'متوافق مع معايير App Store & Google Play';
                document.getElementById('guaranteeTitle').textContent = 'ضمان أمان وسرية المستندات والمسح التلقائي الفوري';
                document.getElementById('guaranteeDesc').textContent = 'تعتبر ملفاتك ومستنداتك المرفوعة للطباعة مشفرة بالكامل أثناء النقل والمعالجة. لا يقوم أي فرد من فريق العمل بالاطلاع على محتوى ملفاتك، ويتم حذف ملفات الطباعة تلقائياً ونهائياً من خوادمنا فور إتمام عملية الطباعة بنجاح أو بعد انقضاء جلسة الطباعة دون استرجاع.';
                document.getElementById('txtBackNav').textContent = 'الرئيسية';

                document.getElementById('ftHome').textContent = 'الرئيسية';
                document.getElementById('ftPrivacy').textContent = 'سياسة الخصوصية';
                document.getElementById('ftDelete').textContent = 'حذف الحساب';
                document.getElementById('ftAdmin').textContent = 'بوابة الإدارة';
            }
        }
    </script>
</body>
</html>
