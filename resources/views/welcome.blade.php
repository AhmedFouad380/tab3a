<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="منصة طباعة - المنصة السحابية المتكاملة لخدمات الطباعة الفورية وإدارة المطابع وأجهزة الخدمة الذاتية">
    <title>{{ $appName ?? 'منصة طباعة الذكية' }} | حلول الطباعة الرقمية والسحابية</title>

    <!-- Google Fonts: Cairo & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-glow: rgba(37, 99, 235, 0.35);
            --accent: #06b6d4;
            --accent-glow: rgba(6, 182, 212, 0.3);
            --bg-dark: #0a0e17;
            --bg-card: rgba(18, 24, 38, 0.7);
            --bg-card-hover: rgba(28, 36, 56, 0.85);
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
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(37, 99, 235, 0.15) 0%, transparent 45%),
                radial-gradient(circle at 85% 30%, rgba(6, 182, 212, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 50% 85%, rgba(99, 102, 241, 0.1) 0%, transparent 50%);
            background-attachment: fixed;
        }

        /* Number Font Override */
        .num-font {
            font-family: 'Plus Jakarta Sans', 'Cairo', sans-serif;
        }

        /* Container */
        .container {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Navbar */
        .navbar-wrap {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(10, 14, 23, 0.8);
            border-bottom: 1px solid var(--border-glass);
            transition: all 0.3s ease;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
        }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-primary);
        }

        .brand-logo-img {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid var(--border-glass);
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        .brand-name {
            font-size: 1.35rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 30%, #93c5fd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s, transform 0.2s;
            position: relative;
        }

        .nav-links a:hover {
            color: #ffffff;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0.65rem 1.4rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            box-shadow: 0 4px 20px var(--primary-glow);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            box-shadow: 0 6px 26px rgba(37, 99, 235, 0.5);
            transform: translateY(-2px);
            color: #ffffff;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            border: 1px solid var(--border-glass);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            color: #ffffff;
        }

        .btn-lg {
            padding: 0.9rem 2rem;
            font-size: 1.05rem;
            border-radius: 14px;
        }

        /* Hero Section */
        .hero-section {
            padding-top: 170px;
            padding-bottom: 90px;
            position: relative;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 3.5rem;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(37, 99, 235, 0.12);
            border: 1px solid rgba(37, 99, 235, 0.3);
            color: #93c5fd;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            animation: pulse-glow 3s infinite alternate;
        }

        @keyframes pulse-glow {
            0% { box-shadow: 0 0 10px rgba(37, 99, 235, 0.2); }
            100% { box-shadow: 0 0 25px rgba(37, 99, 235, 0.5); }
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 900;
            line-height: 1.25;
            margin-bottom: 1.5rem;
            letter-spacing: -0.5px;
        }

        .hero-title span.highlight {
            background: linear-gradient(135deg, #60a5fa 20%, #38bdf8 60%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            line-height: 1.8;
            max-width: 580px;
        }

        .hero-buttons {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }

        .hero-trust-bar {
            display: flex;
            align-items: center;
            gap: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-glass);
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .trust-item i {
            color: #38bdf8;
            font-size: 1.1rem;
        }

        /* Hero Visual Preview */
        .hero-preview {
            position: relative;
        }

        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            padding: 2rem;
            backdrop-filter: blur(25px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 2;
        }

        .preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-glass);
        }

        .preview-title {
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .preview-status {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .preview-status::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 8px #34d399;
        }

        .preview-kiosk-box {
            background: rgba(10, 14, 23, 0.7);
            border: 1px solid var(--border-glass);
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .kiosk-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .kiosk-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(37, 99, 235, 0.2);
            color: #60a5fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .kiosk-details h4 {
            font-size: 0.95rem;
            font-weight: 700;
        }

        .kiosk-details p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .preview-code-badge {
            background: rgba(255, 255, 255, 0.05);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            color: #38bdf8;
            letter-spacing: 2px;
            border: 1px dashed rgba(56, 189, 248, 0.4);
        }

        .preview-features-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .preview-feat-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--text-secondary);
            padding: 8px 12px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.02);
        }

        .floating-badge {
            position: absolute;
            background: rgba(18, 24, 38, 0.9);
            border: 1px solid var(--border-glass);
            padding: 12px 18px;
            border-radius: 16px;
            backdrop-filter: blur(16px);
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            z-index: 3;
            animation: float-anim 4s ease-in-out infinite;
        }

        .floating-badge.badge-top {
            top: -20px;
            left: -20px;
        }

        .floating-badge.badge-bottom {
            bottom: -25px;
            right: -15px;
            animation-delay: 2s;
        }

        @keyframes float-anim {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* Stats Section */
        .stats-section {
            padding: 3rem 0;
            position: relative;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 1.75rem;
            text-align: center;
            backdrop-filter: blur(15px);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-glass-hover);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
        }

        .stat-number {
            font-size: 2.4rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.3rem;
        }

        .stat-label {
            font-size: 0.95rem;
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* Services & Features Section */
        .section {
            padding: 90px 0;
            position: relative;
        }

        .section-header {
            text-align: center;
            max-width: 680px;
            margin: 0 auto 4rem;
        }

        .section-tag {
            color: #38bdf8;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.8rem;
            display: inline-block;
        }

        .section-title {
            font-size: 2.3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .section-desc {
            color: var(--text-secondary);
            font-size: 1.05rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 2.2rem;
            backdrop-filter: blur(15px);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            background: var(--bg-card-hover);
            border-color: var(--border-glass-hover);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon-wrap {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(37, 99, 235, 0.25);
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon-wrap {
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            color: #ffffff;
            transform: scale(1.05);
            box-shadow: 0 6px 20px var(--primary-glow);
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
        }

        .feature-text {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* How it works */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            position: relative;
        }

        .step-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 2.2rem;
            position: relative;
            text-align: center;
        }

        .step-number {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            color: #ffffff;
            font-weight: 800;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 4px 15px var(--primary-glow);
        }

        .step-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .step-desc {
            color: var(--text-secondary);
            font-size: 0.92rem;
            line-height: 1.7;
        }

        /* FAQ Section */
        .faq-list {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .faq-item {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-question {
            padding: 1.4rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--text-primary);
            user-select: none;
        }

        .faq-question i {
            font-size: 0.9rem;
            color: #38bdf8;
            transition: transform 0.3s ease;
        }

        .faq-answer {
            padding: 0 1.75rem 1.4rem;
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
            display: none;
        }

        .faq-item.active .faq-answer {
            display: block;
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-item.active {
            border-color: var(--border-glass-hover);
            background: var(--bg-card-hover);
        }

        /* CTA Banner */
        .cta-banner {
            margin: 80px 0;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.25) 0%, rgba(6, 182, 212, 0.2) 100%);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 28px;
            padding: 4rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .cta-title {
            font-size: 2.4rem;
            font-weight: 900;
            margin-bottom: 1.2rem;
        }

        .cta-subtitle {
            font-size: 1.1rem;
            color: var(--text-secondary);
            max-width: 620px;
            margin: 0 auto 2.5rem;
        }

        .cta-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        /* Footer */
        .footer {
            border-top: 1px solid var(--border-glass);
            background: rgba(8, 11, 18, 0.95);
            padding: 4rem 0 2rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-brand p {
            color: var(--text-muted);
            font-size: 0.92rem;
            margin-top: 1rem;
            line-height: 1.8;
            max-width: 320px;
        }

        .footer-col h4 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            color: #ffffff;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .footer-col a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .footer-col a:hover {
            color: #ffffff;
        }

        .footer-bottom {
            padding-top: 2rem;
            border-top: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--text-muted);
            font-size: 0.88rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        /* Mobile Hamburger & Drawer */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.4rem;
            cursor: pointer;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .hero-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .hero-subtitle {
                margin: 0 auto 2.5rem;
            }
            .hero-buttons {
                justify-content: center;
            }
            .hero-trust-bar {
                justify-content: center;
            }
            .hero-preview {
                max-width: 500px;
                margin: 0 auto;
            }
            .features-grid, .steps-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            .mobile-menu-btn {
                display: block;
            }
            .hero-title {
                font-size: 2.2rem;
            }
            .features-grid, .steps-grid, .stats-grid {
                grid-template-columns: 1fr;
            }
            .footer-grid {
                grid-template-columns: 1fr;
            }
            .floating-badge {
                display: none;
            }
            .cta-banner {
                padding: 3rem 1.5rem;
            }
            .cta-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <header class="navbar-wrap">
        <div class="container">
            <nav class="navbar">
                <a href="{{ url('/') }}" class="brand-logo-wrap">
                    @if (file_exists(public_path('logo.jpeg')))
                        <img src="{{ asset('logo.jpeg') }}" alt="شعار طباعة" class="brand-logo-img">
                    @else
                        <div class="kiosk-icon" style="width: 42px; height: 42px; border-radius: 12px;">
                            <i class="fa-solid fa-print"></i>
                        </div>
                    @endif
                    <span class="brand-name">{{ $appName ?? 'منصة طباعة' }}</span>
                </a>

                <ul class="nav-links">
                    <li><a href="#hero">الرئيسية</a></li>
                    <li><a href="#features">خدماتنا</a></li>
                    <li><a href="#how-it-works">كيف نعمل؟</a></li>
                    <li><a href="#kiosks">الأجهزة والفروع</a></li>
                    <li><a href="#faqs">الأسئلة الشائعة</a></li>
                    <li><a href="#contact">الدعم الفني</a></li>
                </ul>

                <div class="nav-actions">
                    <a href="{{ url('/admin/login') }}" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>تسجيل الدخول</span>
                    </a>
                    <button class="mobile-menu-btn" id="mobileMenuToggle" aria-label="القائمة">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Mobile Navigation Drawer -->
    <div id="mobileDrawer" style="display:none; position: fixed; top: 80px; left: 0; right: 0; background: rgba(10, 14, 23, 0.98); border-bottom: 1px solid var(--border-glass); padding: 1.5rem; z-index: 99; backdrop-filter: blur(20px);">
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 1.2rem; text-align: center;">
            <li><a href="#hero" class="mobile-link" style="color: #fff; text-decoration: none; font-size: 1.1rem; font-weight: 600;">الرئيسية</a></li>
            <li><a href="#features" class="mobile-link" style="color: #cbd5e1; text-decoration: none; font-size: 1rem;">خدماتنا</a></li>
            <li><a href="#how-it-works" class="mobile-link" style="color: #cbd5e1; text-decoration: none; font-size: 1rem;">كيف نعمل؟</a></li>
            <li><a href="#kiosks" class="mobile-link" style="color: #cbd5e1; text-decoration: none; font-size: 1rem;">الأجهزة والفروع</a></li>
            <li><a href="#faqs" class="mobile-link" style="color: #cbd5e1; text-decoration: none; font-size: 1rem;">الأسئلة الشائعة</a></li>
            <li><a href="#contact" class="mobile-link" style="color: #cbd5e1; text-decoration: none; font-size: 1rem;">الدعم الفني</a></li>
            <li style="padding-top: 1rem;">
                <a href="{{ url('/admin/login') }}" class="btn btn-primary" style="width: 100%;">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>الدخول إلى لوحة التحكم</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Hero Section -->
    <section class="hero-section" id="hero">
        <div class="container">
            <div class="hero-grid">
                <!-- Text Content -->
                <div class="hero-text-content">
                    <div class="hero-badge">
                        <i class="fa-solid fa-bolt"></i>
                        <span>الطباعة السحابية الذكية الأسرع في المملكة</span>
                    </div>
                    <h1 class="hero-title">
                        مستنداتك وأعمالك<br>
                        تُطبع <span class="highlight">بضغطة زر وبأعلى دقة</span>
                    </h1>
                    <p class="hero-subtitle">
                        منظومة متكاملة لطباعة الملفات والملازم والمستندات عبر أجهزة الخدمة الذاتية الفورية (Kiosks) أو الطلب المسبق مع خيارات التجليد والتوصيل السريع لقطاع الأفراد والشركات والتعليم.
                    </p>
                    <div class="hero-buttons">
                        <a href="{{ url('/admin/login') }}" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>دخول لوحة التحكم</span>
                        </a>
                        <a href="#features" class="btn btn-secondary btn-lg">
                            <i class="fa-solid fa-compass"></i>
                            <span>استكشف الخدمات</span>
                        </a>
                    </div>
                    <div class="hero-trust-bar">
                        <div class="trust-item">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>طباعة فورية بالكود السري</span>
                        </div>
                        <div class="trust-item">
                            <i class="fa-solid fa-lock"></i>
                            <span>تشفير وأمان الملفات بنسبة 100%</span>
                        </div>
                        <div class="trust-item">
                            <i class="fa-solid fa-clock"></i>
                            <span>متاح 24/7 عبر الأكشاك</span>
                        </div>
                    </div>
                </div>

                <!-- Interactive Visual Card -->
                <div class="hero-preview">
                    <!-- Top Floating Badge -->
                    <div class="floating-badge badge-top">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.2); color: #34d399; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-file-circle-check"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">حالة الطابعة</div>
                            <div style="font-size: 0.9rem; font-weight: 700; color: #34d399;">جاهزة للطباعة الفورية</div>
                        </div>
                    </div>

                    <!-- Main Preview Card -->
                    <div class="preview-card">
                        <div class="preview-header">
                            <div class="preview-title">
                                <i class="fa-solid fa-sliders text-[#38bdf8]" style="color: #38bdf8;"></i>
                                <span>محاكاة طلب طباعة ذاتية</span>
                            </div>
                            <span class="preview-status">متصل سحابياً</span>
                        </div>

                        <div class="preview-kiosk-box">
                            <div class="kiosk-info">
                                <div class="kiosk-icon">
                                    <i class="fa-solid fa-microchip"></i>
                                </div>
                                <div class="kiosk-details">
                                    <h4>كشك الرياض - فرع العليا</h4>
                                    <p>طريق الملك فهد • مسافة 1.2 كم</p>
                                </div>
                            </div>
                            <div class="preview-code-badge num-font">#TB-8942</div>
                        </div>

                        <div class="preview-features-list">
                            <div class="preview-feat-item">
                                <span>نوع الورق والتجليد</span>
                                <strong style="color: #fff;">A4 فاخر 80g • سلك حلزوني</strong>
                            </div>
                            <div class="preview-feat-item">
                                <span>نمط الطباعة</span>
                                <strong style="color: #fff;">ملون كامل • وجهين (Duplex)</strong>
                            </div>
                            <div class="preview-feat-item">
                                <span>فحص الملفات الآلي</span>
                                <span style="color: #34d399; font-weight: 700;"><i class="fa-solid fa-check-double"></i> تم التحقق (PDF جاهز)</span>
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-glass); display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">الإجمالي التقديري</span>
                                <span class="num-font" style="font-size: 1.3rem; font-weight: 800; color: #38bdf8;">14.50 ر.س</span>
                            </div>
                            <a href="{{ url('/admin/login') }}" class="btn btn-primary" style="padding: 0.55rem 1.2rem; font-size: 0.88rem;">
                                <i class="fa-solid fa-qrcode"></i>
                                <span>إصدار الرمز</span>
                            </a>
                        </div>
                    </div>

                    <!-- Bottom Floating Badge -->
                    <div class="floating-badge badge-bottom">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59, 130, 246, 0.2); color: #60a5fa; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">رصيد المحفظة</div>
                            <div class="num-font" style="font-size: 0.95rem; font-weight: 800; color: #fff;">250.00 ر.س</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number num-font">+{{ number_format($ordersCount ?? 1450) }}</div>
                    <div class="stat-label">مستند وطباعة مكتملة</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number num-font">{{ $kiosksCount ?? 18 }}</div>
                    <div class="stat-label">كشك خدمة ذاتية نشط</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number num-font">{{ $branchesCount ?? 6 }}</div>
                    <div class="stat-label">فروع ومراكز مجهزة</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number num-font">99.8%</div>
                    <div class="stat-label">نسبة رضا المستخدمين</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services & Features Section -->
    <section class="section" id="features">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">مميزات لا تضاهى</span>
                <h2 class="section-title">حلول تقنية ذكية تعيد ابتكار الطباعة</h2>
                <p class="section-desc">صممنا منصة طباعة لتلائم الطلاب، المعلمين، الشركات والمكاتب بأحدث المعايير السحابية الفورية.</p>
            </div>

            <div class="features-grid">
                <!-- Feature 1 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-cube"></i>
                    </div>
                    <h3 class="feature-title">الطباعة الذاتية الفورية (Kiosks)</h3>
                    <p class="feature-text">
                        ارفع ملفاتك من جوالك أو حاسوبك، واحصل على رمز QR أو كود سري لطباعة مستنداتك في ثوانٍ من أقرب ماكينة خدمة ذاتية بدون أي طوابير أو موظفين.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <h3 class="feature-title">الطلبات المسبقة والتشطيب الفاخر</h3>
                    <p class="feature-text">
                        تخصيص كامل لخيارات الورق (عادي، مقوى، لامع)، والتجليد (سلك، حراري، دبوس)، والتغليف الاحترافي مع معاينة فورية قبل الدفع.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h3 class="feature-title">حلول المعلمين والجامعات</h3>
                    <p class="feature-text">
                        نظام خاص لطباعة الملازم والكتب الدراسية بالكميات المطلوبة مع أسعار خاصة بالجملة وتوزيع سهل ومريح على الطلاب.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="feature-title">أعلى درجات الخصوصية والأمان</h3>
                    <p class="feature-text">
                        ملفاتك مشفرة بالكامل ولا يتم تخزينها بعد إتمام الطباعة إلا بموافقتك، مما يضمن أقصى معايير السرية لمستنداتك الحساسة.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h3 class="feature-title">المحفظة الإلكترونية والدفع السهل</h3>
                    <p class="feature-text">
                        ادفع بمرونة تامة عبر مدى، Apple Pay، أو عبر رصيد محفظتك الرقمية داخل المنصة واستفد من عروض الشحن الترويجية.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">لوحة تحكم وإدارة احترافية</h3>
                    <p class="feature-text">
                        إدارة مركزية لجميع الطابعات ومستوى الأحبار والأوراق وتتبع حالة الطلبات والتحليلات المالية والتقارير بدقة متناهية.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="section" id="how-it-works" style="background: rgba(13, 18, 30, 0.4);">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">بساطة وسرعة</span>
                <h2 class="section-title">كيف تتم عملية الطباعة في 3 خطوات؟</h2>
                <p class="section-desc">خطوات يسيرة تختصر عليك الوقت والجهد وتمنحك أفضل نتيجة ممكنة.</p>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number num-font">1</div>
                    <h3 class="step-title">رفع الملف وضبط الخيارات</h3>
                    <p class="step-desc">ارفع ملفك بصيغة PDF أو Word، وحدد الألوان (أبيض وأسود أو ملون)، نوع الورق وخيارات التجليد.</p>
                </div>

                <div class="step-card">
                    <div class="step-number num-font">2</div>
                    <h3 class="step-title">الدفع وإصدار رمز الاستلام</h3>
                    <p class="step-desc">راجع حاسبة التكلفة الفورية، ادفع بأمان، واحصل فوراً على رمز الطباعة السري (OTP أو QR Code).</p>
                </div>

                <div class="step-card">
                    <div class="step-number num-font">3</div>
                    <h3 class="step-title">الطباعة الذاتية أو التوصيل</h3>
                    <p class="step-desc">توجه لأقرب جهاز كشك واكتب الرمز لتستلم أوراقك خلال لحظات، أو استلمها عبر خدمة التوصيل حتى باب بيتك.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Kiosks & Technology Showcase -->
    <section class="section" id="kiosks">
        <div class="container">
            <div class="preview-card" style="padding: 3rem; background: linear-gradient(135deg, rgba(18, 24, 38, 0.85) 0%, rgba(30, 41, 68, 0.6) 100%);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;">
                    <div>
                        <span class="section-tag">أجهزة الخدمة الذاتية</span>
                        <h2 class="section-title" style="margin-bottom: 1.25rem;">أكشاك طباعة ذكية تنتشر في أهم المواقع</h2>
                        <p style="color: var(--text-secondary); line-height: 1.8; margin-bottom: 2rem;">
                            تنتشر مكائن طباعة الذاتية في الجامعات، المراكز التجارية، والمستشفيات لتكون الخدمة دائماً بالقرب منك متى ما احتجت إليها، مزودة بأحدث طابعات الليزر فائقة السرعة والدقة.
                        </p>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 10px; color: var(--text-primary); font-weight: 600;">
                                <i class="fa-solid fa-check text-[#38bdf8]" style="color: #38bdf8;"></i>
                                <span>فحص صحة الماكينة (Pre-Print Health Check) قبل الخصم من الرصيد</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px; color: var(--text-primary); font-weight: 600;">
                                <i class="fa-solid fa-check text-[#38bdf8]" style="color: #38bdf8;"></i>
                                <span>شاشات لمس تفاعلية وسريعة الاستجابة باللغة العربية</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px; color: var(--text-primary); font-weight: 600;">
                                <i class="fa-solid fa-check text-[#38bdf8]" style="color: #38bdf8;"></i>
                                <span>إشعارات حية عبر تطبيق الجوال لحظة بدء وانتهاء الطباعة</span>
                            </div>
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <div style="background: rgba(10, 14, 23, 0.8); border: 1px solid var(--border-glass); border-radius: 20px; padding: 2.5rem; display: inline-block; max-width: 380px; width: 100%;">
                            <i class="fa-solid fa-print" style="font-size: 5rem; color: #38bdf8; margin-bottom: 1.5rem; filter: drop-shadow(0 0 20px var(--primary-glow));"></i>
                            <h4 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 0.5rem;">طباعة فورية بالكود</h4>
                            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">أدخل رمز التحقق في شاشة الكشك وستبدأ الطباعة فوراً دون لمس الملفات</p>
                            <a href="{{ url('/admin/login') }}" class="btn btn-primary" style="width: 100%;">
                                <i class="fa-solid fa-desktop"></i>
                                <span>الدخول للإدارة</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section" id="faqs">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">استفسارات شائعة</span>
                <h2 class="section-title">كل ما تود معرفته عن منصة طباعة</h2>
                <p class="section-desc">إجابات واضحة ومباشرة عن أبرز الأسئلة المتكررة من عملائنا.</p>
            </div>

            <div class="faq-list">
                <div class="faq-item active">
                    <div class="faq-question">
                        <span>كيف يمكنني استلام أوراقي من كشك الخدمة الذاتية؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        بمجرد إنشاء الطلب والدفع عبر المنصة، يظهر لك رمز سري مكون من أرقام أو رمز QR. توجه إلى أي كشك تابع للمنصة، واضغط على "استلام طلب" ثم أدخل الرمز، وستقوم الطابعة بإخراج أوراقك فوراً.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>ما هي صيغ الملفات المدعومة للطباعة؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        ندعم حالياً ملفات PDF بجميع إصداراتها بشكل أساسي ومفضل لضمان الحفاظ على تنسيق الخطوط والأبعاد، بالإضافة إلى مستندات Word، وصور JPG/PNG عالية الدقة.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>ماذا يحدث إذا انقطع الورق أو تعطلت الطابعة أثناء الطباعة؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        يقوم نظامنا السحابي بإجراء فحص صحة الماكينة (Machine Health Check) مسبقاً قبل السماح بطباعة المستند، وفي حال حدوث أي طارئ يتم استرداد المبلغ فوراً إلى محفظتك الإلكترونية ويمكنك استخدام الرمز في كشك آخر.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>هل تتوفر خدمات خاصة للمدارس والجامعات والمعلمين؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        نعم بكل تأكيد! نوفر بوابات مخصصة للمعلمين لرفع الملازم والكتب مع عروض أسعار تفضيلية للكميات، مع إمكانية إتاحة الملازم لطلابهم لطلبها وطباعتها مباشرة.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <div class="container" id="contact">
        <div class="cta-banner">
            <h2 class="cta-title">جاهز لتجربة أسرع وأذكى نظام طباعة؟</h2>
            <p class="cta-subtitle">
                سواء كنت مديراً لفرع، مسؤول نظام، أو شريكاً تجارياً، يمكنك الدخول إلى لوحة التحكم فوراً وإدارة كامل العمليات بسهولة.
            </p>
            <div class="cta-actions">
                <a href="{{ url('/admin/login') }}" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>تسجيل الدخول إلى لوحة التحكم</span>
                </a>
                @if(!empty($supportPhone))
                    <a href="tel:{{ $supportPhone }}" class="btn btn-secondary btn-lg">
                        <i class="fa-solid fa-phone"></i>
                        <span>اتصل بالدعم: {{ $supportPhone }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="{{ url('/') }}" class="brand-logo-wrap">
                        @if (file_exists(public_path('logo.jpeg')))
                            <img src="{{ asset('logo.jpeg') }}" alt="شعار طباعة" class="brand-logo-img">
                        @else
                            <div class="kiosk-icon" style="width: 40px; height: 40px; border-radius: 10px;">
                                <i class="fa-solid fa-print"></i>
                            </div>
                        @endif
                        <span class="brand-name">{{ $appName ?? 'منصة طباعة' }}</span>
                    </a>
                    <p>
                        الحل المتكامل للطباعة الذاتية السحابية، وربط المطابع ومكائن الخدمة الذاتية بأحدث التقنيات وأعلى معايير الجودة والسرعة.
                    </p>
                </div>

                <div class="footer-col">
                    <h4>روابط سريعة</h4>
                    <ul>
                        <li><a href="#hero">الرئيسية</a></li>
                        <li><a href="#features">الخدمات والحلول</a></li>
                        <li><a href="#how-it-works">كيف تعمل المنصة؟</a></li>
                        <li><a href="#kiosks">الأجهزة والفروع</a></li>
                        <li><a href="#faqs">الأسئلة الشائعة</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>الأنظمة والإدارة</h4>
                    <ul>
                        <li><a href="{{ url('/admin/login') }}">بوابة المشرفين والإدارة</a></li>
                        <li><a href="{{ url('/admin') }}">لوحة التحكم الرئيسية</a></li>
                        <li><a href="{{ url('/api/v1/pricing-rules') }}" target="_blank">قواعد التسعير والخيارات</a></li>
                        <li><a href="{{ url('/api/v1/branches') }}" target="_blank">فروع وأجهزة الخدمة</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>المساعدة والتواصل</h4>
                    <ul>
                        <li><a href="tel:{{ $supportPhone ?? '+966500000000' }}"><i class="fa-solid fa-headset" style="margin-left: 8px;"></i>الدعم الفني المباشر</a></li>
                        <li><span style="color: var(--text-muted); font-size: 0.88rem;">متاح يومياً 24/7 لخدمتكم</span></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    جميع الحقوق محفوظة &copy; {{ date('Y') }} {{ $appName ?? 'منصة طباعة' }}
                </div>
                <div style="display: flex; gap: 1.5rem;">
                    <a href="{{ url('/admin/login') }}" style="color: #38bdf8; text-decoration: none; font-weight: 600;">
                        <i class="fa-solid fa-lock" style="margin-left: 5px;"></i>
                        دخول النظام
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Interactive Scripts -->
    <script>
        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentElement;
                const wasActive = item.classList.contains('active');
                
                // Close other items
                document.querySelectorAll('.faq-item').forEach(other => {
                    other.classList.remove('active');
                });

                if (!wasActive) {
                    item.classList.add('active');
                }
            });
        });

        // Mobile Menu Toggle
        const mobileBtn = document.getElementById('mobileMenuToggle');
        const mobileDrawer = document.getElementById('mobileDrawer');
        if (mobileBtn && mobileDrawer) {
            mobileBtn.addEventListener('click', () => {
                const isOpen = mobileDrawer.style.display === 'block';
                mobileDrawer.style.display = isOpen ? 'none' : 'block';
            });

            document.querySelectorAll('.mobile-link').forEach(link => {
                link.addEventListener('click', () => {
                    mobileDrawer.style.display = 'none';
                });
            });
        }
    </script>
</body>
</html>
