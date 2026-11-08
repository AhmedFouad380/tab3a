<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم الدفع بنجاح - بنك الراجحي</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        :root {
            --primary: #005bb5;
            --success: #10b981;
            --success-dark: #047857;
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
            background: #ecfdf5;
            border: 2px solid #a7f3d0;
            color: #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.15);
        }
        .title {
            font-size: 1.6rem;
            font-weight: 900;
            color: #065f46;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.6;
        }
        .details-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 28px;
            text-align: right;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #edf2f7;
            font-size: 0.9rem;
        }
        .details-row:last-child {
            border-bottom: none;
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
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <h1 class="title">تم الدفع بنجاح!</h1>
        <p class="subtitle">
            شكراً لك، تم استلام دفعتك وتأكيد العملية بنجاح عبر بوابة بنك الراجحي.
        </p>

        @if($order)
            <div class="details-box">
                <div class="details-row">
                    <span style="color: #64748b;">رقم الطلب:</span>
                    <strong class="num-font">{{ $order->order_number }}</strong>
                </div>
                <div class="details-row">
                    <span style="color: #64748b;">المبلغ المسدد:</span>
                    <strong class="num-font" style="color: #047857; font-size: 1.1rem;">
                        {{ number_format((float) $order->total_amount, 2) }} ر.س
                    </strong>
                </div>
                @if(!empty($verification['transaction_id']))
                    <div class="details-row">
                        <span style="color: #64748b;">رقم المعاملة (الراجحي):</span>
                        <span class="num-font" style="font-size: 0.85rem;">{{ $verification['transaction_id'] }}</span>
                    </div>
                @endif
                <div class="details-row">
                    <span style="color: #64748b;">بوابة الدفع:</span>
                    <span><strong>بنك الراجحي (Al Rajhi Bank)</strong></span>
                </div>
            </div>

            <div class="btn-group">
                <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-primary" target="_blank">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>عرض الفاتورة الإلكترونية</span>
                </a>
                <a href="{{ url('/') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-house"></i>
                    <span>العودة للرئيسية</span>
                </a>
            </div>
        @else
            <a href="{{ url('/') }}" class="btn btn-primary">العودة للرئيسية</a>
        @endif
    </div>

</body>
</html>
