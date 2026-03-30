<x-layout>
    <x-slot:title>Log Aplikasi</x-slot:title>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <i class="mdi mdi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Log Aplikasi</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Catatan aktivitas dan perubahan data</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-6">
            <form action="{{ route('settings.logs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        <i class="mdi mdi-magnify mr-1"></i> Cari
                    </label>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari deskripsi atau user..."
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Filter by Log Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        <i class="mdi mdi-tag mr-1"></i> Kategori
                    </label>
                    <select name="log_name"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Kategori</option>
                        @foreach($logNames as $name)
                            <option value="{{ $name }}" {{ $logName == $name ? 'selected' : '' }}>{{ ucfirst($name) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter by Event -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        <i class="mdi mdi-lightning mr-1"></i> Event
                    </label>
                    <select name="event"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Event</option>
                        @foreach($events as $evt)
                            <option value="{{ $evt }}" {{ $event == $evt ? 'selected' : '' }}>{{ ucfirst($evt) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg transition-colors font-medium flex items-center justify-center gap-2">
                        <i class="mdi mdi-filter"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('settings.logs') }}"
                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2.5 rounded-lg transition-colors font-medium"
                        title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="mdi mdi-clipboard-text-clock text-gray-500"></i>
                Riwayat Aktivitas
            </h2>
            <span class="text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 px-3 py-1 rounded-full font-medium">
                {{ $logs->total() }} logs
            </span>
        </div>

        @if($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Event
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Deskripsi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Subject
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                IP Address
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Waktu
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $color = $log->event_color;
                                        $icons = [
                                            'created' => 'mdi-plus-circle',
                                            'updated' => 'mdi-pencil',
                                            'deleted' => 'mdi-delete',
                                            'login' => 'mdi-login',
                                            'logout' => 'mdi-logout',
                                            'viewed' => 'mdi-eye',
                                            'exported' => 'mdi-download',
                                            'imported' => 'mdi-upload',
                                            'restored' => 'mdi-restore',
                                        ];
                                        $icon = $icons[$log->event] ?? 'mdi-information';
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-medium bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 text-{{ $color }}-700 dark:text-{{ $color }}-400">
                                        <i class="mdi {{ $icon }}"></i>
                                        {{ ucfirst($log->event ?? 'Activity') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $log->description }}</p>
                                    @if($log->properties && is_array($log->properties))
                                        <div class="mt-1">
                                            @if(isset($log->properties['old']))
                                                <details class="text-xs">
                                                    <summary class="cursor-pointer text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                                        <i class="mdi mdi-history"></i> Lihat perubahan
                                                    </summary>
                                                    <div class="mt-1 p-2 bg-gray-100 dark:bg-gray-900 rounded text-xs">
                                                        @if(isset($log->properties['old']))
                                                            <p class="text-red-600 dark:text-red-400 mb-1">
                                                                <strong>Old:</strong> {{ json_encode($log->properties['old']) }}
                                                            </p>
                                                        @endif
                                                        @if(isset($log->properties['new']))
                                                            <p class="text-green-600 dark:text-green-400">
                                                                <strong>New:</strong> {{ json_encode($log->properties['new']) }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </details>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->user)
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-{{ $log->user->role === 'admin' ? 'purple' : 'blue' }}-100 dark:bg-{{ $log->user->role === 'admin' ? 'purple' : 'blue' }}-900/30 flex items-center justify-center">
                                                <span class="text-{{ $log->user->role === 'admin' ? 'purple' : 'blue' }}-600 dark:text-{{ $log->user->role === 'admin' ? 'purple' : 'blue' }}-400 text-xs font-bold">
                                                    {{ substr($log->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $log->user->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user->role }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">System</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->subject_type && $log->subject_id)
                                        @php
                                            $modelName = class_exists($log->subject_type) ? class_basename($log->subject_type) : $log->subject_type;
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                            <i class="mdi mdi-shape"></i>
                                            {{ $modelName }} #{{ $log->subject_id }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-mono">{{ $log->ip_address ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->created_at->format('d M Y, H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $log->created_at->diffForHumans() }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $logs->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <i class="mdi mdi-clipboard-text-off text-4xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-400 font-medium">Belum ada log aktivitas</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Aktivitas akan tercatat otomatis saat ada perubahan data</p>
            </div>
        @endif
    </div>
</x-layout>
