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

            <!-- Right Column - Profile & Messages (1/3 width) -->
            <div class="space-y-6">
                <x-pemohon.profile :user="$user"></x-pemohon.profile>
                <x-pemohon.messages :messages="$messages"></x-pemohon.messages>
            </div>
        </section>

    </main>

    <!-- Detail Modal -->
    <x-pemohon.detail-modal></x-pemohon.detail-modal>

    <!-- Footer -->
    <x-pemohon.footer></x-pemohon.footer>

    <!-- Scripts -->
    <script>
        // Sample data for demonstration
        const stats = @json($stats);

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initStats();
            initMessages();
            initApplications();
        });

        function initStats() {
            document.getElementById('statTotal').textContent = stats.total;
            document.getElementById('statInProgress').textContent = stats.in_progress;
            document.getElementById('statFix').textContent = stats.needs_fix;
            document.getElementById('statDone').textContent = stats.completed;
        }

        function initMessages() {
            // Initialize messages list
        }

        function initApplications() {
            // Initialize applications table
        }

        function openDetail(id) {
            // Open detail modal
        }

        function closeDetail() {
            document.getElementById('detailModal').classList.add('hidden');
            document.getElementById('detailModal').classList.remove('flex');
        }
    </script>
</x-pemohon.layout>
