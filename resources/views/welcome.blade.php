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
            background-color: #111827;
            font-family: 'Arial', sans-serif;
            color: white;
            overflow: hidden;
        }
        #canvas-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        .loading-container {
            position: fixed;
            left: 0;
            bottom: 10%;
            width: 100%;
            text-align: center;
            z-index: 2;
        }
        .app-name {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
            background: linear-gradient(90deg, #4f46e5, #8b5cf6, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 10px rgba(79, 70, 229, 0.3);
        }
        .loading-text {
            font-size: 1.2rem;
            color: #e2e8f0;
            margin-bottom: 15px;
        }
        .progress-container {
            width: 250px;
            height: 6px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }
        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #4f46e5, #8b5cf6, #ec4899);
            border-radius: 3px;
            transition: width 0.3s ease;
            position: relative;
        }
        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                rgba(255,255,255,0) 0%,
                rgba(255,255,255,0.5) 50%,
                rgba(255,255,255,0) 100%);
            animation: shine 1.5s infinite;
        }
        .progress-percentage {
            font-size: 0.9rem;
            color: #e2e8f0;
            margin-top: 10px;
        }
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }
        @keyframes shine {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }
    </style>
</head>
<body>
    <div id="canvas-container"></div>
    <div class="loading-container">
        <div class="app-name">Mamam Novel</div>
        <div class="loading-text">Memuat konten...</div>
        <div class="progress-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        <div class="progress-percentage" id="progress-percentage">0%</div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // Three.js setup
        const container = document.getElementById('canvas-container');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });

        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        container.appendChild(renderer.domElement);

        // Lighting
        const ambientLight = new THREE.AmbientLight(0x404040, 2);
        scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
        directionalLight.position.set(1, 1, 1);
        scene.add(directionalLight);

        // Book geometry
        const createBook = () => {
            const bookGroup = new THREE.Group();

            // Book cover
            const coverGeometry = new THREE.BoxGeometry(5, 7, 0.5);
            const coverMaterial = new THREE.MeshPhongMaterial({
                color: 0x4f46e5,
                specular: 0x555555,
                shininess: 30
            });
            const cover = new THREE.Mesh(coverGeometry, coverMaterial);
            bookGroup.add(cover);

            // Book pages
            const pagesGeometry = new THREE.BoxGeometry(4.8, 6.8, 0.4);
            const pagesMaterial = new THREE.MeshPhongMaterial({
                color: 0xffffff,
                specular: 0x222222,
                shininess: 10
            });
            const pages = new THREE.Mesh(pagesGeometry, pagesMaterial);
            pages.position.z = 0.05;
            bookGroup.add(pages);

            // Book title
            const titleGeometry = new THREE.PlaneGeometry(4, 1);
            const titleMaterial = new THREE.MeshBasicMaterial({
                color: 0xffffff,
                transparent: true,
                opacity: 0.8
            });
            const title = new THREE.Mesh(titleGeometry, titleMaterial);
            title.position.z = 0.26;
            title.position.y = 1.5;
            bookGroup.add(title);

            return bookGroup;
        };

        const book = createBook();
        scene.add(book);

        // Particles
        const particlesGeometry = new THREE.BufferGeometry();
        const particlesCount = 500;

        const posArray = new Float32Array(particlesCount * 3);

        for(let i = 0; i < particlesCount * 3; i++) {
            // Random positions for particles in a sphere around the book
            posArray[i] = (Math.random() - 0.5) * 50;
        }

        particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));

        const particlesMaterial = new THREE.PointsMaterial({
            size: 0.1,
            color: 0x8b5cf6,
            transparent: true,
            opacity: 0.8,
            sizeAttenuation: true
        });

        const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
        scene.add(particlesMesh);

        // Camera position
        camera.position.z = 10;

        // Animation function
        const animate = () => {
            requestAnimationFrame(animate);

            // Rotate the book
            book.rotation.y += 0.01;
            book.rotation.x = Math.sin(Date.now() * 0.001) * 0.1;

            // Animate particles
            particlesMesh.rotation.y += 0.001;

            renderer.render(scene, camera);
        };

        // Handle window resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        // Start animation
        animate();

        // Progress bar animation
        const progressBar = document.getElementById('progress-bar');
        const progressPercentage = document.getElementById('progress-percentage');
        let progress = 0;
        const loadingDuration = 3500; // 3.5 seconds
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
