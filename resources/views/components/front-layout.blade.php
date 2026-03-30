<div>
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>JITU - Layanan Perizinan Terpadu Banjarnegara</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#1e40af',
                            secondary: '#1e293b',
                            accent: '#3b82f6',
                            success: '#10b981',
                            warning: '#f59e0b',
                            danger: '#ef4444',
                        }
                    }
                }
            }
        </script>
        <style>
            .hero-pattern {
                background-color: #1e40af;
                background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }

            .service-card:hover {
                transform: translateY(-5px);
            }

            .btn-elevate {
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }

            .btn-elevate:hover {
                transform: translateY(-1px);
                box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
            }

            /* Slider Animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fadeInUp {
                animation: fadeInUp 0.8s ease-out forwards;
            }

            /* Slider dot hover effect */
            .slider-dot:hover {
                transform: scale(1.2);
            }
        </style>
    </head>

    <body class="bg-gray-50 text-gray-800 font-sans antialiased">

        <x-front-navbar></x-front-navbar>

        {{ $slot }}

        <x-front-footer></x-front-footer>

        <!-- Back to Top Button -->
        <button id="backToTop" onclick="scrollToTop()"
            class="fixed bottom-8 right-8 bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 opacity-0 pointer-events-none hover:scale-110 z-50"
            aria-label="Back to top">
            <i class="fas fa-chevron-up"></i>
        </button>

        <script>
            // Back to Top Button
            const backToTopBtn = document.getElementById('backToTop');

            // Show/hide button on scroll
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    backToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                } else {
                    backToTopBtn.classList.add('opacity-0', 'pointer-events-none');
                }
            });

            // Scroll to top function
            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        </script>
    </body>

    </html>

</div>
