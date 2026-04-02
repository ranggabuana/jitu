<x-pemohon.layout>
    <x-slot:title>Dashboard Pemohon - JITU Banjarnegara</x-slot:title>

    <!-- Navbar -->
    <x-pemohon.navbar></x-pemohon.navbar>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Hero Section -->
        <x-pemohon.hero></x-pemohon.hero>

        <!-- Statistics Cards -->
        <x-pemohon.stats :stats="$stats"></x-pemohon.stats>

        <!-- Main Grid -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Applications (2/3 width) -->
            <div class="lg:col-span-2">
                <x-pemohon.applications :applications="$recentApplications"></x-pemohon.applications>
            </div>

            <!-- Right Column - Profile (1/3 width) -->
            <div class="space-y-6">
                <x-pemohon.profile :user="$user"></x-pemohon.profile>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>
</x-pemohon.layout>
