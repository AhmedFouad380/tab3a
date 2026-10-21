<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>الطباعة الفورية - {{ $kiosk->name['ar'] ?? $kiosk->machine_code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --bg: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .container {
            max-width: 480px;
            margin: 0 auto;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 10px;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .title {
            font-size: 24px;
            font-weight: 900;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-muted);
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .section-label {
            font-size: 14px;
            font-weight: 800;
            color: #cbd5e1;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed #475569;
            border-radius: 16px;
            padding: 24px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: rgba(15, 23, 42, 0.4);
        }

        .upload-area.active {
            border-color: var(--primary);
            background: rgba(16, 185, 129, 0.05);
        }

        .upload-icon {
            font-size: 36px;
            margin-bottom: 8px;
        }

        .upload-text {
            font-size: 14px;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 4px;
        }

        .upload-hint {
            font-size: 12px;
            color: var(--text-muted);
        }

        input[type="file"] {
            display: none;
        }

        /* Options */
        .option-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 14px;
        }

        .option-btn {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border);
            color: #cbd5e1;
            padding: 12px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }

        .option-btn.selected {
            background: rgba(16, 185, 129, 0.15);
            border-color: var(--primary);
            color: #34d399;
        }

        .copies-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 8px 16px;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #334155;
            color: white;
            font-size: 18px;
            font-weight: 800;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-value {
            font-size: 18px;
            font-weight: 800;
        }

        /* Rules summary pill */
        .limits-notice {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.25);
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 12px;
            color: #93c5fd;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Action Buttons */
        .btn-submit {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            font-size: 17px;
            font-weight: 800;
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .btn-pay {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
        }

        /* Price Breakdown */
        .price-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid var(--border);
        }

        .price-label {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .price-value {
            font-size: 22px;
            font-weight: 900;
            color: #34d399;
        }

        /* Notifications / Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 14px;
            display: none;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }
    </style>
</head>
<body>

    <div class="container">
        <header class="header">
            <div class="logo-badge">
                <span>🖨️</span> {{ $kiosk->machine_code }} - متصل الآن
            </div>
            <h1 class="title">الطباعة الفورية</h1>
            <p class="subtitle">{{ $kiosk->name['ar'] ?? 'كشك الطباعة الذاتية' }}</p>
        </header>

        <div id="error-alert" class="alert alert-danger"></div>
        <div id="success-alert" class="alert alert-success"></div>

        <div class="limits-notice">
            ℹ️ الحد الأقصى للملف: <strong>{{ $maxFileSizeMb }} MB</strong> | أقصى أوراق: <strong>{{ $maxSheets }} ورقة</strong> | أقل سعر: <strong>{{ number_format($minOrderPrice, 2) }} ر.س</strong>
        </div>

        <div class="card" id="upload-card">
            <div class="section-label">📂 اختيار المستند</div>
            
            <div class="upload-area" id="dropzone" onclick="document.getElementById('file-input').click()">
                <div class="upload-icon">📄</div>
                <div class="upload-text" id="file-name-display">انقر هنا لاختيار الملف من جوالك</div>
                <div class="upload-hint">يدعم صيغ PDF والصور حتى {{ $maxFileSizeMb }} ميجابايت</div>
            </div>
            <input type="file" id="file-input" accept=".pdf,image/png,image/jpeg">

            <div style="margin-top: 18px;">
                <div class="section-label">🎨 ألوان الطباعة</div>
                <div class="option-group">
                    <button type="button" class="option-btn selected" id="opt-bw" onclick="setColor('black_and_white')">
                        ⚪⚫ أبيض وأسود
                    </button>
                    <button type="button" class="option-btn" id="opt-color" onclick="setColor('color')">
                        🌈 ألوان كاملة
                    </button>
                </div>

                <div class="section-label">📑 نمط الطباعة</div>
                <div class="option-group">
                    <button type="button" class="option-btn selected" id="opt-single" onclick="setSide('single_sided')">
                        📄 وجه واحد
                    </button>
                    <button type="button" class="option-btn" id="opt-double" onclick="setSide('double_sided')">
                        📑 وجهين (Duplex)
                    </button>
                </div>

                <div class="section-label">🔢 عدد النسخ</div>
                <div class="copies-box">
                    <span>عدد النسخ المطلوبة:</span>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <button type="button" class="qty-btn" onclick="changeCopies(-1)">-</button>
                        <span class="qty-value" id="copies-count">1</span>
                        <button type="button" class="qty-btn" onclick="changeCopies(1)">+</button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn-submit" id="btn-upload" style="margin-top: 20px;" onclick="uploadDocument()">
                <span>📤 رفع الملف وعرضه بالشاشة</span>
            </button>
        </div>

        <!-- Payment Card (shown after upload) -->
        <div class="card" id="payment-card" style="display: none;">
            <div class="section-label" style="color: #34d399;">✅ تم تجهيز الطلب للشاشة</div>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px;">
                تم عرض تفاصيل مستندك على شاشة الطابعة أمامك. يرجى إتمام الدفع لبدء خروج الأوراق.
            </p>

            <div class="price-summary">
                <div class="price-label">المبلغ الإجمالي المطلوب:</div>
                <div class="price-value" id="final-price-text">0.00 ر.س</div>
            </div>

            <button type="button" class="btn-submit btn-pay" style="margin-top: 20px;" onclick="confirmAndPay()">
                <span>💳 ادفع الآن واستلم الطباعة</span>
            </button>
        </div>

    </div>

    <script>
        const token = "{{ $session->session_token }}";
        const maxMb = {{ $maxFileSizeMb }};
        let selectedFile = null;
        let colorMode = 'black_and_white';
        let sideMode = 'single_sided';
        let copies = 1;

        const fileInput = document.getElementById('file-input');
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                selectedFile = e.target.files[0];
                
                // Client-side file size check
                const fileSizeMb = selectedFile.size / (1024 * 1024);
                if (fileSizeMb > maxMb) {
                    showError(`حجم الملف (${fileSizeMb.toFixed(1)} MB) يتجاوز الحد المسموح به (${maxMb} MB).`);
                    selectedFile = null;
                    document.getElementById('file-name-display').innerText = 'انقر هنا لاختيار الملف من جوالك';
                    return;
                }

                hideAlerts();
                document.getElementById('file-name-display').innerText = selectedFile.name + ` (${(selectedFile.size / 1024).toFixed(0)} KB)`;
                document.getElementById('dropzone').classList.add('active');
            }
        });

        function setColor(mode) {
            colorMode = mode;
            document.getElementById('opt-bw').classList.toggle('selected', mode === 'black_and_white');
            document.getElementById('opt-color').classList.toggle('selected', mode === 'color');
        }

        function setSide(side) {
            sideMode = side;
            document.getElementById('opt-single').classList.toggle('selected', side === 'single_sided');
            document.getElementById('opt-double').classList.toggle('selected', side === 'double_sided');
        }

        function changeCopies(delta) {
            copies = Math.max(1, Math.min(50, copies + delta));
            document.getElementById('copies-count').innerText = copies;
        }

        function showError(msg) {
            const el = document.getElementById('error-alert');
            el.innerText = msg;
            el.style.display = 'block';
            document.getElementById('success-alert').style.display = 'none';
        }

        function showSuccess(msg) {
            const el = document.getElementById('success-alert');
            el.innerText = msg;
            el.style.display = 'block';
            document.getElementById('error-alert').style.display = 'none';
        }

        function hideAlerts() {
            document.getElementById('error-alert').style.display = 'none';
            document.getElementById('success-alert').style.display = 'none';
        }

        function uploadDocument() {
            if (!selectedFile) {
                showError('فضلاً اختر ملفاً أولاً من جوالك للطباعة.');
                return;
            }

            hideAlerts();
            const btn = document.getElementById('btn-upload');
            btn.disabled = true;
            btn.innerHTML = '⏳ جاري الرفع والمعالجة...';

            const formData = new FormData();
            formData.append('file', selectedFile);
            formData.append('copies', copies);
            formData.append('color_mode', colorMode);
            formData.append('side_mode', sideMode);

            fetch(`/kiosk-session/${token}/upload`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = '<span>📤 رفع الملف وعرضه بالشاشة</span>';

                if (!res.success) {
                    showError(res.message || 'حدث خطأ أثناء رفع الملف');
                    return;
                }

                showSuccess('تم رفع الملف بنجاح! راقب شاشة الطابعة أمامك.');
                document.getElementById('payment-card').style.display = 'block';
                document.getElementById('final-price-text').innerText = Number(res.data.total_price).toFixed(2) + ' ر.س';
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<span>📤 رفع الملف وعرضه بالشاشة</span>';
                showError('تعذر الاتصال بالخادم. يرجى المحاولة ثانية.');
            });
        }

        function confirmAndPay() {
            hideAlerts();
            const btn = event.currentTarget;
            btn.disabled = true;
            btn.innerHTML = '⏳ جاري معالجة الدفع والطباعة...';

            fetch(`/kiosk-session/${token}/pay`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ payment_method: 'card' })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    showSuccess('تم الدفع بنجاح! جاري خروج الأوراق من الطابعة الآن 🎉');
                    btn.innerHTML = '✅ تمت العملية بنجاح';
                } else {
                    btn.disabled = false;
                    btn.innerHTML = '💳 ادفع الآن واستلم الطباعة';
                    showError(res.message || 'فشلت عملية الدفع.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '💳 ادفع الآن واستلم الطباعة';
                showError('تعذر تأكيد الدفع.');
            });
        }
    </script>
</body>
</html>
