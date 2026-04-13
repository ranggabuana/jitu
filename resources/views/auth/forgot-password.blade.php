<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - JITU Banjarnegara</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        .floating-delay-1 {
            animation-delay: 0.5s;
        }

        .floating-delay-2 {
            animation-delay: 1s;
        }

        @keyframes floating {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .decoration-element {
            position: absolute;
            z-index: -1;
        }

        .decoration-1 {
            top: 10%;
            left: 5%;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, #ff9a9e, #fad0c4);
            opacity: 0.6;
        }

        .decoration-2 {
            bottom: 15%;
            right: 7%;
            width: 120px;
            height: 120px;
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            background: linear-gradient(45deg, #a1c4fd, #c2e9fb);
            opacity: 0.5;
        }

        .decoration-3 {
            top: 25%;
            right: 15%;
            width: 60px;
            height: 60px;
            border-radius: 20% 80% 20% 80% / 80% 20% 80% 20%;
            background: linear-gradient(45deg, #ffecd2, #fcb69f);
            opacity: 0.7;
        }

        .decoration-4 {
            bottom: 25%;
            left: 10%;
            width: 100px;
            height: 100px;
            border-radius: 40% 60% 30% 70% / 60% 40% 70% 30%;
            background: linear-gradient(45deg, #84fab0, #8fd3f4);
            opacity: 0.6;
        }

        .input-field:focus-within {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <!-- Decorative elements -->
    <div class="decoration-element decoration-1 floating"></div>
    <div class="decoration-element decoration-2 floating floating-delay-1"></div>
    <div class="decoration-element decoration-3 floating floating-delay-2"></div>
    <div class="decoration-element decoration-4 floating floating-delay-1"></div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            <div class="login-card p-8 relative overflow-hidden">
                <div class="text-center mb-8">
                    <!-- Logo -->
                    <div class="flex justify-center mb-4">
                        <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <i class="mdi mdi-lock-reset text-white text-4xl"></i>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Lupa Password?</h1>
                    <p class="text-gray-600">Masukkan email Anda untuk menerima link reset password</p>
                </div>

                <!-- Info Banner -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="mdi mdi-information text-blue-600 text-xl mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Petunjuk:</p>
                            <p>Kami akan mengirimkan link reset password ke email Anda. Link berlaku selama 60 menit.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf
                    <div class="input-field bg-gray-50 rounded-xl p-1 transition-all duration-300">
                        <div class="flex items-center px-4 py-3">
                            <i class="mdi mdi-email text-gray-400 text-xl mr-3"></i>
                            <input type="email" id="email" name="email" placeholder="Alamat Email"
                                class="bg-transparent border-0 text-gray-800 placeholder-gray-400 focus:ring-0 focus:outline-none flex-1"
                                required value="{{ old('email') }}">
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-[1.02] cursor-pointer">
                        <i class="mdi mdi-send mr-2"></i>Kirim Link Reset Password
                    </button>
                </form>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}"
                        class="text-gray-600 hover:text-gray-800 text-sm flex items-center justify-center gap-2">
                        <i class="mdi mdi-arrow-left"></i> Kembali ke Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil! 📧',
                html: '{{ session('success') }}',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'OK, Mengerti',
                width: '450px',
                padding: '2rem',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal! ❌',
                html: '{{ session('error') }}',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK, Mengerti',
                width: '450px',
                padding: '2rem',
            });
        @endif
    </script>
</body>

</html>
