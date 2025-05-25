<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novel Laravel - Loading</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #18181b; /* Zinc-900, hitam sebagai background utama */
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .splash-container {
            text-align: center;
            padding: 2rem;
            max-width: 400px;
            width: 100%;
        }

        .logo-container {
            margin-bottom: 2rem;
            position: relative;
        }

        .book-image {
            width: 120px;
            height: 150px;
            margin: 0 auto;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            border-radius: 4px;
            position: relative;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .book-cover {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            border-radius: 4px 4px 4px 4px;
        }

        .book-spine {
            position: absolute;
            top: 0;
            left: 0;
            width: 15px;
            height: 100%;
            background: linear-gradient(to right, #4338ca, #4f46e5);
            border-radius: 4px 0 0 4px;
        }

        .book-pages {
            position: absolute;
            top: 5px;
            right: 5px;
            bottom: 5px;
            width: 100px;
            background-color: #f8fafc;
            border-radius: 2px;
        }

        .book-line {
            position: absolute;
            height: 1px;
            background-color: rgba(255, 255, 255, 0.3);
            width: 50px;
            left: 50%;
            transform: translateX(-50%);
        }

        .line-1 { top: 30px; }
        .line-2 { top: 50px; }
        .line-3 { top: 70px; }
        .line-4 { top: 90px; }

        .app-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(90deg, #4f46e5, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .loading-text {
            font-size: 1rem;
            color: #94a3b8;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .progress-container {
            width: 100%;
            height: 6px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            margin: 0 auto;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #4f46e5, #8b5cf6);
            border-radius: 3px;
            transition: width 0.2s ease;
        }

        .progress-percentage {
            font-size: 0.875rem;
            color: #94a3b8;
            margin-top: 0.75rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="splash-container">
        <div class="logo-container">
            <div class="book-image">
                <div class="book-spine"></div>
                <div class="book-pages"></div>
                <div class="book-cover">
                    <div class="book-line line-1"></div>
                    <div class="book-line line-2"></div>
                    <div class="book-line line-3"></div>
                    <div class="book-line line-4"></div>
                </div>
            </div>
        </div>

        <div class="app-name">Mamam Novel</div>
        <div class="loading-text">Memuat konten...</div>
        <div class="progress-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        <div class="progress-percentage" id="progress-percentage">0%</div>
    </div>

    <script>
        // Sederhana loading bar tanpa animasi kompleks
        const progressBar = document.getElementById('progress-bar');
        const progressPercentage = document.getElementById('progress-percentage');
        let progress = 0;
        const loadingDuration = 3000; // 3 detik
        const intervalTime = 30;
        const totalSteps = loadingDuration / intervalTime;
        const progressIncrement = 100 / totalSteps;

        const loadingInterval = setInterval(() => {
            progress += progressIncrement;
            if (progress >= 100) {
                progress = 100;
                clearInterval(loadingInterval);
                setTimeout(() => {
                    window.location.href = "/admin";
                }, 300);
            }
            progressBar.style.width = `${progress}%`;
            progressPercentage.textContent = `${Math.round(progress)}%`;
        }, intervalTime);
    </script>
</body>
</html>
