<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعذر إتمام الدفع - بنك الراجحي</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        :root {
            --primary: #005bb5;
            --danger: #dc2626;
            --bg-page: #f1f5f9;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', system-ui, sans-serif;
        }
        .num-font {
            font-family: 'Plus Jakarta Sans', 'Cairo', sans-serif;
        }
        body {
            background: var(--bg-page);
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 16px 45px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            max-width: 500px;
            width: 100%;
            padding: 40px 32px;
            text-align: center;
        }
        .icon-circle {
            width: 84px;
            height: 84px;
            background: #fef2f2;
            border: 2px solid #fecaca;
            color: #dc2626;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(220, 38, 38, 0.12);
        }
        .title {
            font-size: 1.6rem;
            font-weight: 900;
            color: #991b1b;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.6;
        }
        .error-alert {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
            border-radius: 12px;
            padding: 14px;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 24px;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-primary {
            background: var(--primary);
            color: #ffffff;
        }
        .btn-primary:hover {
            background: #004b99;
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="icon-circle">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>

        <h1 class="title">تعذر إتمام الدفع</h1>
        <p class="subtitle">
            نعتذر، لم تكتمل عملية الدفع عبر بنك الراجحي. يرجى التحقق من بيانات البطاقة أو رصيد الحساب والمحاولة مرة أخرى.
        </p>

        @if(!empty($errorMessage))
            <div class="error-alert">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        <div class="btn-group">
            @if(!empty($order))
                <a href="{{ route('payment.alrajhi.checkout', $order) }}" class="btn btn-primary">
                    <i class="fa-solid fa-rotate-right"></i>
                    <span>إعادة المحاولة عبر الراجحي</span>
                </a>
            @endif
            <a href="{{ url('/') }}" class="btn btn-secondary">
                <i class="fa-solid fa-house"></i>
                <span>العودة للرئيسية</span>
            </a>
        </div>
    </div>

</body>
</html>
