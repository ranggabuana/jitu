<x-layout>
    <x-slot:title>Laporan SLA - {{ $application->no_registrasi }}</x-slot:title>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-3">
            <a href="{{ route('data-perijinan.selesai') }}"
                class="inline-flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                <i class="mdi mdi-arrow-left"></i>
                <span>Kembali ke Daftar Selesai</span>
            </a>
        </div>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/40 rounded-2xl flex items-center justify-center border border-blue-200 dark:border-blue-800 flex-shrink-0">
                    <i class="mdi mdi-timer-star text-blue-600 dark:text-blue-400 text-4xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">Laporan SLA: {{ $application->no_registrasi }}</h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                        <span class="text-sm text-gray-500 font-bold uppercase tracking-wider">{{ $application->perijinan->nama_perijinan }}</span>
                        <div class="h-4 w-px bg-gray-300 hidden md:block"></div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black uppercase rounded-full border border-green-200">
                            {{ $application->status_label }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-4 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-700 pt-4 md:pt-0 md:pl-8">
                @php $totalSla = $records->sum('duration_seconds'); @endphp
                <div class="text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Total Waktu Pemrosesan (Net SLA)</p>
                    <div class="text-3xl font-mono font-black text-green-600 dark:text-green-400">
                        {{ formatDuration($totalSla) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Timeline & Stats -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Processing Table -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
                    <h2 class="text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest flex items-center gap-2">
                        <i class="mdi mdi-table-clock text-blue-500 text-xl"></i> Waktu Pemrosesan Per Tahapan
                    </h2>
                </div>
                <div class="p-0">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Tahapan & Petugas</th>
                                <th class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Durasi SLA</th>
                                <th class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu Validasi</th>
                                <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($records as $v)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-800 dark:text-white mb-0.5">
                                            {{ $v->validationFlow->role_label ?? 'Tahapan' }}
                                            @php
                                                $assignedOpd = $v->validationFlow->assignedUser->opd ?? null;
                                                $actualOpd = $v->validator->opd ?? null;
                                                $opdToDisplay = $actualOpd ?? $assignedOpd;
                                            @endphp
                                            @if($opdToDisplay)
                                                <span class="text-indigo-600 dark:text-indigo-400 font-bold">({{ $opdToDisplay->nama_opd }})</span>
                                            @endif
                                        </span>
                                        <span class="text-xs text-gray-500 flex items-center gap-1.5 font-medium">
                                            <i class="mdi mdi-account-circle-outline"></i>
                                            {{ $v->validator->name ?? ($v->validationFlow->assignedUser->name ?? '-') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="inline-block px-4 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-xl font-mono text-sm font-black border border-blue-100 dark:border-blue-800">
                                        {{ formatDuration($v->duration_seconds ?? 0) }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center text-xs text-gray-500 font-bold uppercase">
                                    {{ $v->validated_at ? $v->validated_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $v->status_color }} uppercase tracking-tighter">
                                        {{ $v->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Return History Section -->
            @if($returnLogs->count() > 0)
            <div class="space-y-4">
                <h3 class="text-sm font-black text-rose-500 uppercase tracking-[0.2em] ml-2 flex items-center gap-2">
                    <i class="mdi mdi-history text-xl"></i> Riwayat Pengembalian Berkas
                </h3>
                <div class="space-y-4">
                    @foreach($returnLogs as $log)
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-rose-100 dark:border-rose-900/30 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4">
                            <span class="text-[10px] font-mono font-black text-rose-400 bg-rose-50 dark:bg-rose-900/40 px-3 py-1 rounded-full uppercase">{{ $log->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center flex-shrink-0 border border-rose-200 dark:border-rose-800 shadow-sm">
                                <i class="mdi mdi-undo-variant text-rose-600 dark:text-rose-400 text-2xl"></i>
                            </div>
                            <div class="flex-1 min-w-0 pr-20">
                                <div class="text-sm text-gray-800 dark:text-gray-200 leading-snug mb-3">
                                    <strong class="text-gray-900 dark:text-white">{{ $log->fromUser->name }}</strong> 
                                    <span class="text-gray-500">({{ $log->from_role_label }})</span>
                                    <span class="text-rose-600 font-black mx-1 tracking-tighter uppercase text-xs">MENGEMBALIKAN KE</span> 
                                    <strong class="text-gray-900 dark:text-white">{{ $log->to_role_label }}</strong>
                                </div>
                                @if($log->catatan)
                                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-800 italic relative">
                                        <i class="mdi mdi-format-quote-open text-gray-200 dark:text-gray-700 absolute top-2 left-2 text-3xl"></i>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed pl-4">
                                            "{{ $log->catatan }}"
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar: Applicant Info -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <h2 class="text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest flex items-center gap-2">
                        <i class="mdi mdi-account-card-outline text-blue-500 text-xl"></i> Identitas Pemohon
                    </h2>
                </div>
                <div class="p-8 space-y-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center border border-indigo-200 dark:border-indigo-800 flex-shrink-0">
                            <i class="mdi mdi-account text-indigo-600 dark:text-indigo-400 text-3xl"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-gray-800 dark:text-white text-base truncate">{{ $application->user->name }}</h3>
                            <span class="inline-block px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-[9px] font-black uppercase tracking-widest mt-1">
                                {{ $application->user->status_pemohon_label }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Email Pemohon</p>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $application->user->email }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Telepon / WA</p>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $application->user->no_hp ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">NIK / NIP</p>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $application->user->nip ?? ($application->user->nik ?? '-') }}</p>
                        </div>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('data-perijinan.show', $application->id) }}" 
                            class="flex items-center justify-center gap-2 w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg active:scale-95">
                            <i class="mdi mdi-eye text-lg"></i> Lihat Detail Berkas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
