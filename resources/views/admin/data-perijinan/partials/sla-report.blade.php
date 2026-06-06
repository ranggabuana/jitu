<div class="p-6">
    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-xl flex items-center justify-center border border-blue-200 dark:border-blue-800">
            <i class="mdi mdi-timer-star text-blue-600 dark:text-blue-400 text-2xl"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white line-clamp-1">SLA Report: {{ $application->no_registrasi }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest font-black">{{ $application->perijinan->nama_perijinan }}</p>
        </div>
    </div>

    <div class="space-y-4">
        <!-- Validation Steps SLA -->
        <div>
            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Waktu Pemrosesan Tahapan</h4>
            <div class="overflow-hidden border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Tahapan & Petugas</th>
                            <th class="px-5 py-3 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Waktu Proses (SLA)</th>
                            <th class="px-5 py-3 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Status Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-50 dark:divide-gray-800">
                        @php $totalSla = 0; @endphp
                        @foreach($records as $v)
                            @php $totalSla += ($v->duration_seconds ?? 0); @endphp
                            <tr>
                                <td class="px-5 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-800 dark:text-white uppercase tracking-tight">
                                            {{ $v->validationFlow->role_label ?? 'Tahapan' }}
                                            @php
                                                $assignedOpd = $v->validationFlow->assignedUser->opd ?? null;
                                                $actualOpd = $v->validator->opd ?? null;
                                                $opdToDisplay = $actualOpd ?? $assignedOpd;
                                            @endphp
                                            @if($opdToDisplay)
                                                <span class="text-purple-600">({{ $opdToDisplay->nama_opd }})</span>
                                            @endif
                                        </span>
                                        <span class="text-[10px] text-gray-500 font-medium">
                                            <i class="mdi mdi-account-check-outline mr-1"></i> {{ $v->validator->name ?? ($v->validationFlow->assignedUser->name ?? '-') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg font-mono text-xs font-black">
                                        {{ formatDuration($v->duration_seconds ?? 0) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $v->status_color }} uppercase">
                                        {{ $v->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <td class="px-5 py-3 text-right text-[10px] font-black text-gray-500 uppercase tracking-widest">Total Waktu Pemrosesan (Net SLA)</td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-4 py-1 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-xl font-mono text-sm font-black border border-green-200 dark:border-green-800">
                                    {{ formatDuration($totalSla) }}
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Return History -->
        @if($returnLogs->count() > 0)
        <div class="mt-8">
            <h4 class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em] mb-3 ml-1">Riwayat Pengembalian Berkas</h4>
            <div class="space-y-3">
                @foreach($returnLogs as $log)
                <div class="p-4 bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30 rounded-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-3">
                        <span class="text-[9px] font-mono text-rose-400 font-bold uppercase">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-undo-variant text-rose-600 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] text-gray-800 dark:text-gray-200 leading-tight mb-1">
                                <strong>{{ $log->fromUser->name }}</strong> 
                                <span class="text-gray-500">({{ $log->from_role_label }})</span>
                                <span class="text-rose-600 font-bold mx-1">MENGEMBALIKAN KE</span> 
                                <strong class="text-gray-800 dark:text-gray-100">{{ $log->to_role_label }}</strong>
                            </p>
                            @if($log->catatan)
                                <div class="mt-2 p-2 bg-white/60 dark:bg-gray-800/60 rounded-xl border border-rose-50 italic">
                                    <p class="text-[10px] text-rose-800 dark:text-rose-300 leading-relaxed">
                                        <i class="mdi mdi-comment-text-outline mr-1 opacity-50"></i> "{{ $log->catatan }}"
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

    <div class="mt-8 flex justify-end">
        <button type="button" onclick="closeSlaModal()" class="px-8 py-2.5 bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Tutup Laporan</button>
    </div>
</div>
