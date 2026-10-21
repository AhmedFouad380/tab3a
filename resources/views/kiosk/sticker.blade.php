<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملصق طابعة كشك - {{ $kiosk->machine_code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --secondary: #0f172a;
            --accent: #3b82f6;
            --bg: #f8fafc;
            --card-border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
        }

        .controls {
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
        }

        .btn {
            background: #0f172a;
            color: white;
            padding: 12px 28px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            background: #1e293b;
        }

        .btn-success {
            background: #059669;
        }

        .btn-success:hover {
            background: #047857;
        }

        /* Printable Sticker Sheet */
        .sticker-card {
            background: white;
            width: 480px;
            padding: 36px 32px;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border: 2px dashed #94a3b8;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .brand-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .brand-title {
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-kiosk {
            background: #ecfdf5;
            color: #065f46;
            font-weight: 800;
            font-size: 15px;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid #a7f3d0;
            letter-spacing: 0.5px;
        }

        .location-info {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 14px;
        }

        .headline {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.3;
            margin-bottom: 6px;
        }

        .sub-headline {
            font-size: 14px;
            color: #475569;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .qr-wrapper {
            background: #ffffff;
            display: inline-block;
            padding: 18px;
            border-radius: 24px;
            border: 3px solid #10b981;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15);
            margin-bottom: 18px;
        }

        .qr-wrapper svg {
            display: block;
            width: 250px;
            height: 250px;
        }

        .steps-container {
            background: #f8fafc;
            border-radius: 18px;
            padding: 16px 18px;
            margin-top: 10px;
            border: 1px solid #e2e8f0;
            text-align: right;
        }

        .steps-title {
            font-size: 13px;
            font-weight: 800;
            color: #334155;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }

        .step-item:last-child {
            margin-bottom: 0;
        }

        .step-number {
            width: 24px;
            height: 24px;
            background: #10b981;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .footer-note {
            margin-top: 18px;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
                justify-content: flex-start;
            }
            .controls {
                display: none;
            }
            .sticker-card {
                box-shadow: none;
                border: 1.5px solid #000;
                width: 100%;
                max-width: 420px;
                margin: 0 auto;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <div class="controls">
        <button class="btn btn-success" onclick="window.print()">
            🖨️ طباعة الملصق الآن (Print)
        </button>
        <a href="{{ route('kiosk.qr.download', $kiosk->id) }}" class="btn">
            ⬇️ تنزيل ملف الـ QR (SVG)
        </a>
        <a href="{{ route('kiosk.screen', $kiosk->machine_code) }}" target="_blank" class="btn" style="background: #3b82f6;">
            🖥️ معاينة شاشة العرض للكشك
        </a>
    </div>

    <div class="sticker-card">
        <div class="brand-header">
            <div class="brand-title">
                🖨️ {{ $appName }}
            </div>
            <div class="badge-kiosk">
                {{ $kiosk->machine_code }}
            </div>
        </div>

        <div class="location-info">
            📍 {{ $kiosk->branch?->name['ar'] ?? 'كشك الطباعة الذاتي' }} - {{ $kiosk->name['ar'] ?? '' }}
        </div>

        <div class="headline">
            الطباعة الفورية السريعة
        </div>
        <div class="sub-headline">
            امسح الرمز بكاميرا هاتفك لبدء الطباعة فوراً
        </div>

        <div class="qr-wrapper">
            {!! $qrSvg !!}
        </div>

        <div class="steps-container">
            <div class="steps-title">⚡ خطوات الاستخدام بثوانٍ:</div>
            <div class="step-item">
                <span class="step-number">1</span>
                <span>افتح كاميرا هاتفك وامسح الرمز أعلاه للربط.</span>
            </div>
            <div class="step-item">
                <span class="step-number">2</span>
                <span>ارفع ملفك وحدد الخيارات (ألوان / وجهين).</span>
            </div>
            <div class="step-item">
                <span class="step-number">3</span>
                <span>ادفع بسهولة واستلم أوراقك من الطابعة فوراً.</span>
            </div>
        </div>

        <div class="footer-note">
            <span>دعم فني: {{ $supportPhone }}</span>
            <span>مدعوم بتقنية الطباعة الذاتية</span>
        </div>
    </div>

</body>
</html>
