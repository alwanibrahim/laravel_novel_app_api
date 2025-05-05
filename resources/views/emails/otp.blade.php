<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4158D0, #C850C0, #FFCC70);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header {
            background: linear-gradient(135deg, #3B82F6, #2563EB);
            color: white;
            text-align: center;
            padding: 32px 16px;
            position: relative;
            overflow: hidden;
        }

        .logo-container {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .logo {
            width: 50px;
            height: 50px;
        }

        .header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 28px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
        }

        .content {
            padding: 32px 24px;
            text-align: center;
        }

        .message {
            font-size: 16px;
            line-height: 1.6;
            color: #374151;
            margin-bottom: 24px;
        }

        .otp-container {
            position: relative;
            margin: 32px 0;
            padding: 20px;
            background: linear-gradient(to right, #f0f4ff, #e6f0ff);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        .otp-code {
            font-size: 36px;
            font-weight: 700;
            color: #2563EB;
            letter-spacing: 8px;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            margin: 8px 0;
            user-select: all;
            cursor: copy;
        }

        .copy-instruction {
            font-size: 12px;
            color: #6B7280;
            margin-top: 8px;
        }

        .timer {
            margin-top: 16px;
            font-size: 14px;
            color: #6B7280;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .timer-icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #6B7280;
            border-radius: 50%;
            position: relative;
        }

        .note {
            margin-top: 32px;
            padding: 16px;
            background-color: #F3F4F6;
            border-radius: 8px;
            font-size: 14px;
            color: #6B7280;
            position: relative;
            border-left: 4px solid #3B82F6;
        }

        .note-icon {
            display: inline-flex;
            width: 20px;
            height: 20px;
            background-color: #3B82F6;
            border-radius: 50%;
            color: white;
            font-weight: bold;
            font-size: 14px;
            justify-content: center;
            align-items: center;
            margin-right: 8px;
            vertical-align: middle;
        }

        .security-badges {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 32px;
        }

        .badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #6B7280;
            background-color: #F3F4F6;
            padding: 8px 12px;
            border-radius: 20px;
        }

        .badge-icon {
            width: 16px;
            height: 16px;
            background-color: #3B82F6;
            border-radius: 50%;
        }

        .footer {
            background-color: #F9FAFB;
            font-size: 12px;
            color: #9CA3AF;
            text-align: center;
            padding: 20px;
            border-top: 1px solid #E5E7EB;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin: 16px 0;
        }

        .social-link {
            width: 32px;
            height: 32px;
            background-color: #E5E7EB;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #6B7280;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background-color: #3B82F6;
            color: white;
            transform: translateY(-3px);
        }

        @media screen and (max-width: 500px) {
            .container {
                width: 100%;
                margin: 10px;
            }

            .otp-code {
                font-size: 32px;
                letter-spacing: 6px;
                padding: 12px;
            }

            .header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                    <img src="{{ config('app.url') }}/images/kofi.png" alt="Logo" class="logo">
            </div>
            <h2>Verifikasi Email Kamu</h2>
        </div>
        <div class="content">
            <p class="message">Terima kasih telah mendaftar! Untuk melanjutkan, silakan verifikasi alamat email kamu dengan kode OTP berikut:</p>

            <div class="otp-container">
                <div class="otp-code">{{ $otp }}</div>
                <div class="copy-instruction">Klik kode di atas untuk menyalin</div>
                <div class="timer">
                    <span class="timer-icon"></span>
                    Kode berlaku selama 10 menit
                </div>
            </div>

            <div class="note">
                <span class="note-icon">!</span>
                Jangan bagikan kode ini kepada siapa pun. Tim kami tidak akan pernah meminta kode OTP ini.
            </div>

            <div class="security-badges">
                <div class="badge">
                    <span class="badge-icon"></span>
                    Aman
                </div>
                <div class="badge">
                    <span class="badge-icon"></span>
                    Terenkripsi
                </div>
                <div class="badge">
                    <span class="badge-icon"></span>
                    Terverifikasi
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="social-links">
                <a href="#" class="social-link">FB</a>
                <a href="#" class="social-link">IG</a>
                <a href="#" class="social-link">TW</a>
                <a href="#" class="social-link">YT</a>
            </div>
            <p>Jika kamu tidak mencoba untuk mendaftar, silakan abaikan email ini.</p>
            <p>&copy; {{ date('Y') }} Laravel App. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
