<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard Pemohon - JITU Banjarnegara' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#78350f', // amber-900 - Coklat utama
                        secondary: '#451a03', // amber-950 - Coklat gelap
                        accent: '#d97706', // amber-600 - Coklat terang
                        success: '#16a34a', // green-600
                        warning: '#d97706', // amber-600
                        danger: '#dc2626', // red-600
                    }
                }
            }
        }
    </script>
    <style>
        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-modalSlideUp {
            animation: modalSlideUp 0.3s ease-out forwards;
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex flex-col">
    {{ $slot }}

    @stack('scripts')
</body>

</html>
