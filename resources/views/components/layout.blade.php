<!DOCTYPE html>
<html lang="en" class="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <!-- Tailwind CSS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <script>
        // Default to light mode, only use dark mode if explicitly set by user
        const theme = localStorage.getItem('theme');

        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar {
            transition: all 0.3s ease;
        }

        /* Collapsed sidebar state - width becomes 0, content hidden */
        .sidebar.collapsed {
            width: 0 !important;
            overflow: hidden;
        }

        .sidebar.collapsed .sidebar-header,
        .sidebar.collapsed .sidebar-user-info,
        .sidebar.collapsed .sidebar-nav-content {
            opacity: 0;
            visibility: hidden;
            width: 0;
            padding: 0;
            margin: 0;
            border: none;
        }

        .sidebar-header,
        .sidebar-user-info {
            transition: opacity 0.3s ease, visibility 0.3s ease, width 0.3s ease, padding 0.3s ease;
        }

        .sidebar-nav-content {
            transition: opacity 0.3s ease, visibility 0.3s ease, width 0.3s ease;
        }


        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .submenu.open {
            max-height: 500px;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-gradient {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .active-menu {
            background: linear-gradient(to right, #dbeafe, #e0e7ff) !important;
            color: #2563eb !important;
            border-left: 4px solid #2563eb;
        }

        .dark .active-menu {
            background: linear-gradient(to right, #1e3a8a, #1e40af) !important;
            color: #93c5fd !important;
            border-left: 4px solid #3b82f6;
        }

        /* Submenu active state */
        .submenu .active-menu {
            background: linear-gradient(to right, #dbeafe, #e0e7ff) !important;
            color: #2563eb !important;
            border-left: none !important;
        }

        .dark .submenu .active-menu {
            background: linear-gradient(to right, #1e3a8a, #1e40af) !important;
            color: #93c5fd !important;
        }

        .menu-item {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .menu-item:hover::before {
            left: 100%;
        }

        .menu-item:hover {
            background-color: #f0f9ff !important;
            color: #0369a1 !important;
        }

        .dark .menu-item:hover {
            background-color: #1e3a8a !important;
            color: #bfdbfe !important;
        }

        .submenu-item {
            transition: all 0.3s ease;
        }

        .submenu-item:hover {
            background-color: #f0f9ff !important;
            color: #0369a1 !important;
        }

        .dark .submenu-item:hover {
            background-color: #1e3a8a !important;
            color: #bfdbfe !important;
        }

        .dark .dark\:bg-gray-800 {
            background-color: #1f2937;
        }

        .dark .dark\:bg-gray-900 {
            background-color: #111827;
        }

        .dark .dark\:text-white {
            color: #f9fafb;
        }

        .dark .dark\:border-gray-700 {
            border-color: #374151;
        }

        .dark .dark\:hover\:bg-gray-700:hover {
            background-color: #374151;
        }

        .dark .dark\:text-gray-300 {
            color: #d1d5db;
        }

        .dark .dark\:bg-gray-700 {
            background-color: #374151;
        }

        .dark .dark\:text-gray-200 {
            color: #e5e7eb;
        }

        .dark .dark\:bg-gray-600 {
            background-color: #4b5563;
        }

        .dark .dark\:text-gray-400 {
            color: #9ca3af;
        }

        .dark .dark\:bg-gray-800\/50 {
            background-color: rgba(31, 41, 55, 0.5);
        }

        .dark .dark\:ring-gray-700 {
            box-shadow: 0 0 0 1px #374151;
        }

        .dark .dark\:placeholder-gray-400::placeholder {
            color: #9ca3af;
        }

        .dark .dark\:focus\:ring-blue-500:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .dark .dark\:focus\:ring-red-500:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.3);
        }

        .dark .dark\:focus\:ring-green-500:focus {
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.3);
        }

        .dark .dark\:focus\:ring-yellow-500:focus {
            box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.3);
        }

        .dark .dark\:focus\:ring-purple-500:focus {
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.3);
        }

        .dark .dark\:focus\:ring-indigo-500:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }

        .dark .dark\:focus\:ring-pink-500:focus {
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.3);
        }

        .dark .dark\:focus\:ring-teal-500:focus {
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.3);
        }

        .dark .dark\:focus\:ring-orange-500:focus {
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.3);
        }

        .dark .dark\:focus\:ring-cyan-500:focus {
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.3);
        }

        .dark .dark\:focus\:ring-emerald-500:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
        }

        .dark .dark\:focus\:ring-rose-500:focus {
            box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.3);
        }

        .dark .dark\:focus\:ring-slate-500:focus {
            box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.3);
        }

        .dark .dark\:focus\:ring-zinc-500:focus {
            box-shadow: 0 0 0 3px rgba(113, 113, 122, 0.3);
        }

        .dark .dark\:focus\:ring-neutral-500:focus {
            box-shadow: 0 0 0 3px rgba(115, 115, 115, 0.3);
        }

        .dark .dark\:focus\:ring-stone-500:focus {
            box-shadow: 0 0 0 3px rgba(120, 113, 108, 0.3);
        }

        .dark .dark\:focus\:ring-gray-500:focus {
            box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.3);
        }

        .dark .dark\:focus\:ring-blue-600:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
        }

        .dark .dark\:focus\:ring-red-600:focus {
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.3);
        }

        .dark .dark\:focus\:ring-green-600:focus {
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.3);
        }

        .dark .dark\:focus\:ring-yellow-600:focus {
            box-shadow: 0 0 0 3px rgba(202, 138, 4, 0.3);
        }

        .dark .dark\:focus\:ring-purple-600:focus {
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.3);
        }

        .dark .dark\:focus\:ring-indigo-600:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
        }

        .dark .dark\:focus\:ring-pink-600:focus {
            box-shadow: 0 0 0 3px rgba(219, 39, 119, 0.3);
        }

        .dark .dark\:focus\:ring-teal-600:focus {
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.3);
        }

        .dark .dark\:focus\:ring-orange-600:focus {
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.3);
        }

        .dark .dark\:focus\:ring-cyan-600:focus {
            box-shadow: 0 0 0 3px rgba(8, 145, 178, 0.3);
        }

        .dark .dark\:focus\:ring-emerald-600:focus {
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.3);
        }

        .dark .dark\:focus\:ring-rose-600:focus {
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.3);
        }

        .dark .dark\:focus\:ring-slate-600:focus {
            box-shadow: 0 0 0 3px rgba(71, 85, 105, 0.3);
        }

        .dark .dark\:focus\:ring-zinc-600:focus {
            box-shadow: 0 0 0 3px rgba(82, 82, 91, 0.3);
        }

        .dark .dark\:focus\:ring-neutral-600:focus {
            box-shadow: 0 0 0 3px rgba(82, 82, 82, 0.3);
        }

        .dark .dark\:focus\:ring-stone-600:focus {
            box-shadow: 0 0 0 3px rgba(87, 83, 78, 0.3);
        }

        .dark .dark\:focus\:ring-gray-600:focus {
            box-shadow: 0 0 0 3px rgba(75, 85, 99, 0.3);
        }

        /* Sidebar logout button styling */
        .sidebar {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .sidebar nav {
            flex: 1;
            overflow-y: auto;
        }

        /* User dropdown mobile responsiveness */
        @media (max-width: 640px) {
            #user-dropdown {
                right: 0 !important;
                left: auto !important;
                width: calc(100vw - 2rem) !important;
                max-width: calc(100vw - 2rem) !important;
                min-width: 200px !important;
            }
        }

        /* Breadcrumb mobile responsiveness */
        @media (max-width: 640px) {
            .breadcrumb-nav {
                display: none;
            }
        }


        /* Smooth dropdown animation */
        #user-dropdown {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: top right;
        }

        #user-dropdown.visible {
            opacity: 1 !important;
            visibility: visible !important;
        }

        #user-dropdown.animate-open {
            animation: fadeInSlideDown 0.3s ease-out forwards;
        }

        #user-dropdown.animate-close {
            animation: fadeOutSlideUp 0.3s ease-out forwards;
        }

        @keyframes fadeInSlideDown {
            0% {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes fadeOutSlideUp {
            0% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

            100% {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex">
    <!-- Sidebar -->
    <x-sidebar />

    <!-- Main Content -->
    <div id="main-content" class="flex-1 ml-0 lg:ml-64 transition-all duration-300 ease-in-out">
        <!-- Header -->
        <x-header />

        <!-- Dashboard Content -->
        <main class="p-6">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <x-footer />
</body>

</html>
