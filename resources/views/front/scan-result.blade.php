<x-front-layout>
    @php
        $targetOpd = null;
        $isMulti = $perizinan->perijinan->is_multi_opd;
        
        if ($isMulti) {
            if (!empty($opdId)) {
                $targetRecord = $perizinan->validasiRecords->first(function($v) use ($opdId) {
                    return $v->validationFlow && 
                           $v->validationFlow->assignedUser && 
                           $v->validationFlow->assignedUser->opd_id == $opdId;
                });
                if ($targetRecord && $targetRecord->validationFlow->assignedUser->opd) {
                    $targetOpd = $targetRecord->validationFlow->assignedUser->opd;
                }
            }
        } else {
            $targetRecord = $perizinan->validasiRecords->first(function($v) {
                return $v->validationFlow && 
                       in_array($v->validationFlow->role, ['operator_opd', 'kepala_opd']) &&
                       $v->validationFlow->assignedUser && 
                       $v->validationFlow->assignedUser->opd;
            });
            if ($targetRecord && $targetRecord->validationFlow->assignedUser->opd) {
                $targetOpd = $targetRecord->validationFlow->assignedUser->opd;
            }
        }

        $masaAktifRekom = null;
        if ($type === 'rekom') {
            if ($isMulti && !empty($opdId)) {
                $masaAktifRekom = $perizinan->rekom_data_multi[$opdId]['masa_aktif_rekom'] ?? null;
            } else {
                $masaAktifRekom = $perizinan->rekom_data['masa_aktif_rekom'] ?? null;
            }
        }

        $isExpired = false;
        $expiryDate = null;
        if ($type === 'rekom') {
            if ($masaAktifRekom) {
                $expiryDate = \Carbon\Carbon::parse($masaAktifRekom);
                $isExpired = $expiryDate->endOfDay()->isPast();
            }
        } else {
            if ($perizinan->masa_aktif) {
                $expiryDate = \Carbon\Carbon::parse($perizinan->masa_aktif);
                $isExpired = $expiryDate->endOfDay()->isPast();
            }
        }
    @endphp

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-2xl">
            <!-- Header Card -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden mb-8 border border-gray-100">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-center text-white">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                        <i class="fas fa-certificate text-4xl"></i>
                    </div>
                    <h1 class="text-2xl font-black uppercase tracking-tight">Verifikasi Dokumen Elektronik</h1>
                    <p class="text-blue-100 mt-2 font-medium">Sistem Informasi Perizinan Terpadu Banjarnegara</p>
                </div>
                
                <div class="p-8">
                    <!-- Status Badge -->
                    <div class="flex justify-center mb-8">
                        @if($perizinan->is_deactivated)
                            <div class="flex flex-col items-center gap-2">
                                <span class="px-6 py-2 bg-red-100 text-red-700 rounded-full text-sm font-black uppercase tracking-widest border border-red-200 flex items-center gap-2">
                                    <i class="fas fa-ban"></i> Surat {{ $type === 'rekom' ? 'Rekomendasi' : 'Izin' }} Dinonaktifkan oleh Admin DPMPTSP
                                </span>
                            </div>
                        @elseif($perizinan->status === 'diperpanjang')
                            <div class="flex flex-col items-center gap-2">
                                <span class="px-6 py-2 bg-orange-100 text-orange-700 rounded-full text-sm font-black uppercase tracking-widest border border-orange-200 flex items-center gap-2">
                                    <i class="fas fa-history"></i> Dokumen Tidak Berlaku (Telah Diperpanjang)
                                </span>
                            </div>
                        @elseif(in_array($perizinan->status, ['approved', 'diperbaiki']))
                            <div class="flex flex-col items-center gap-2">
                                <span class="px-6 py-2 bg-green-100 text-green-700 rounded-full text-sm font-black uppercase tracking-widest border border-green-200 flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i> Dokumen Sah & Berlaku
                                </span>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Terverifikasi Melalui BSrE E-Sign</p>
                            </div>
                        @elseif($perizinan->status === 'rejected')
                            <span class="px-6 py-2 bg-red-100 text-red-700 rounded-full text-sm font-black uppercase tracking-widest border border-red-200 flex items-center gap-2">
                                <i class="fas fa-times-circle"></i> Dokumen Dibatalkan / Ditolak
                            </span>
                        @else
                            <span class="px-6 py-2 bg-amber-100 text-amber-700 rounded-full text-sm font-black uppercase tracking-widest border border-amber-200 flex items-center gap-2">
                                <i class="fas fa-clock"></i> Dalam Proses Validasi
                            </span>
                        @endif
                    </div>

                    <!-- Deactivation Alert Box -->
                    @if($perizinan->is_deactivated)
                        <div class="mb-8 p-5 bg-red-50 rounded-2xl border border-red-200 text-red-800 flex items-start gap-4 shadow-sm">
                            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-red-600 shrink-0">
                                <i class="fas fa-ban text-xl"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="font-black text-sm uppercase tracking-tight text-red-900">PERINGATAN: SURAT {{ $type === 'rekom' ? 'REKOMENDASI' : 'IZIN' }} DINONAKTIFKAN</h4>
                                <p class="text-xs text-red-700 mt-1 leading-relaxed">
                                    Surat {{ $type === 'rekom' ? 'rekomendasi' : 'izin' }} ini telah dinonaktifkan oleh <strong>Admin DPMPTSP</strong>. Dokumen ini dinyatakan <strong>tidak berlaku lagi</strong>.
                                </p>
                            </div>
                        </div>
                    <!-- Renewed (Diperpanjang) Alert Box -->
                    @elseif($perizinan->status === 'diperpanjang')
                        <div class="mb-8 p-5 bg-orange-50 rounded-2xl border border-orange-200 text-orange-800 flex items-start gap-4 shadow-sm">
                            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600 shrink-0">
                                <i class="fas fa-history text-xl"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="font-black text-sm uppercase tracking-tight text-orange-900">PERINGATAN: DOKUMEN TIDAK BERLAKU</h4>
                                <p class="text-xs text-orange-700 mt-1 leading-relaxed">
                                    Surat {{ $type === 'rekom' ? 'rekomendasi' : 'izin' }} ini <strong>tidak berlaku lagi karena izin telah diperpanjang</strong>. Silakan gunakan surat izin baru yang telah diperpanjang.
                                </p>
                            </div>
                        </div>
                    <!-- Expiration Alert Box -->
                    @elseif($isExpired)
                        <div class="mb-8 p-5 bg-red-50 rounded-2xl border border-red-200 text-red-800 flex items-start gap-4 shadow-sm">
                            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-red-600 shrink-0">
                                <i class="fas fa-exclamation-triangle text-xl"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="font-black text-sm uppercase tracking-tight text-red-900">PERINGATAN: DOKUMEN TIDAK AKTIF</h4>
                                <p class="text-xs text-red-700 mt-1 leading-relaxed">
                                    Masa berlaku {{ $type === 'rekom' ? 'Surat Rekomendasi' : 'Surat Izin' }} ini telah berakhir pada tanggal 
                                    <strong>{{ $expiryDate->format('d/m/Y') }}</strong> ({{ $expiryDate->diffForHumans() }}). 
                                    Dokumen ini sudah tidak dapat digunakan untuk keperluan legalitas atau administrasi apa pun.
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Scanned Document Verification Card -->
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4 text-left w-full">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white {{ $documentStatus === 'Resmi (TTE)' ? 'bg-green-600' : 'bg-red-500' }}">
                                @if($type === 'rekom')
                                    <i class="fas fa-file-alt text-xl"></i>
                                @else
                                    <i class="fas fa-file-signature text-xl"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Jenis Dokumen</h3>
                                <p class="text-lg font-black text-gray-800">{{ $documentType }}</p>
                                @if($type === 'rekom' && $targetOpd)
                                    <p class="text-xs font-bold text-blue-600 dark:text-blue-400 mt-0.5">Rekomendasi dari: {{ $targetOpd->nama_opd }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="w-full md:w-auto text-center md:text-right">
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-black uppercase tracking-wider border {{ $documentStatus === 'Resmi (TTE)' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200' }}">
                                @if($documentStatus === 'Resmi (TTE)')
                                    <i class="fas fa-check-double"></i> Resmi (TTE)
                                @else
                                    <i class="fas fa-exclamation-triangle"></i> Draft Dokumen
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 gap-6">
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest block mb-1">Nomor Registrasi</label>
                                <p class="font-mono text-lg font-bold text-gray-800">{{ $perizinan->no_registrasi }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest block mb-1">Nama Pemohon</label>
                                <p class="text-gray-800 font-bold text-lg capitalize">{{ $perizinan->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $perizinan->user->nama_perusahaan ?: 'Pemohon Perorangan' }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest block mb-1">Jenis Perizinan</label>
                                <p class="text-gray-800 font-bold text-lg">{{ $perizinan->perijinan->nama_perijinan }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest block mb-1">Tanggal Terbit</label>
                                    <p class="text-gray-800 font-bold">{{ isset($tanggalTerbit) && $tanggalTerbit ? $tanggalTerbit->format('d/m/Y') : '-' }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    @if($type === 'rekom')
                                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest block mb-1">Masa Aktif Rekomendasi s/d</label>
                                        @if($masaAktifRekom)
                                            @php
                                                $carbonRekomDate = \Carbon\Carbon::parse($masaAktifRekom);
                                                $isRekomExpired = $carbonRekomDate->endOfDay()->isPast();
                                            @endphp
                                            <p class="text-gray-800 font-bold {{ $isRekomExpired ? 'text-red-600' : '' }}">
                                                {{ $carbonRekomDate->format('d/m/Y') }}
                                            </p>
                                        @else
                                            <p class="text-gray-400 italic font-bold">-</p>
                                        @endif
                                    @else
                                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest block mb-1">Masa Aktif s/d</label>
                                        <p class="text-gray-800 font-bold {{ ($perizinan->masa_aktif && $perizinan->masa_aktif->endOfDay()->isPast()) ? 'text-red-600' : '' }}">
                                            {{ $perizinan->masa_aktif ? $perizinan->masa_aktif->format('d/m/Y') : '-' }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr class="border-dashed border-gray-200">

                        <!-- TTE Info -->
                        <div>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-shield-alt"></i> Detail Tanda Tangan Elektronik
                            </h3>
                            
                            <div class="space-y-3">
                                @if(empty($type) || $type === 'izin')
                                    @php
                                        $signedByKadin = ($documentStatus === 'Resmi (TTE)') && !empty($perizinan->file_izin_tte);
                                    @endphp

                                    <div class="flex items-center justify-between p-4 rounded-2xl border {{ $signedByKadin ? 'bg-indigo-50 border-indigo-100 text-indigo-800' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas {{ $signedByKadin ? 'fa-check-double' : 'fa-minus-circle' }} text-xl"></i>
                                            <div>
                                                <p class="text-xs font-black uppercase tracking-tight">Kadin DPMPTSP</p>
                                                <p class="text-[10px] font-medium opacity-80">{{ $signedByKadin ? 'Telah Ditandatangani Elektronik' : 'Belum/Tidak Ada TTE' }}</p>
                                            </div>
                                        </div>
                                        @if($signedByKadin)
                                            <i class="fas fa-shield-check text-2xl opacity-40"></i>
                                        @endif
                                    </div>
                                @endif

                                @if(empty($type) || $type === 'rekom')
                                    @php
                                        if ($isMulti) {
                                            if (!empty($opdId)) {
                                                $opdSigned = !empty($perizinan->file_rekom_multi_tte[$opdId]);
                                            } else {
                                                $opdSignedCount = is_array($perizinan->file_rekom_multi_tte) ? count(array_filter($perizinan->file_rekom_multi_tte)) : 0;
                                                $opdSigned = $opdSignedCount > 0;
                                            }
                                        } else {
                                            $opdSigned = !empty($perizinan->file_rekom_tte);
                                        }

                                        $signedByOpd = ($documentStatus === 'Resmi (TTE)') && $opdSigned;
                                    @endphp

                                    <div class="flex items-center justify-between p-4 rounded-2xl border {{ $signedByOpd ? 'bg-green-50 border-green-100 text-green-800' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                                        <div class="flex items-center gap-3">
                                            <i class="fas {{ $signedByOpd ? 'fa-check-double' : 'fa-minus-circle' }} text-xl"></i>
                                            <div>
                                                <p class="text-xs font-black uppercase tracking-tight">
                                                    {{ $targetOpd ? 'Kepala ' . $targetOpd->nama_opd : 'Kepala OPD Teknis' }}
                                                </p>
                                                <p class="text-[10px] font-medium opacity-80">
                                                    @if($signedByOpd)
                                                        @if($isMulti && empty($opdId))
                                                            Telah Ditandatangani ({{ is_array($perizinan->file_rekom_multi_tte) ? count(array_filter($perizinan->file_rekom_multi_tte)) : 0 }} OPD)
                                                        @else
                                                            Telah Ditandatangani Elektronik
                                                        @endif
                                                    @else
                                                        Belum/Tidak Ada TTE
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        @if($signedByOpd)
                                            <i class="fas fa-shield-check text-2xl opacity-40"></i>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Text -->
                <div class="p-6 bg-gray-50 border-t border-gray-100 text-center">
                    <p class="text-[10px] text-gray-400 leading-relaxed">
                        Data ini dihasilkan secara otomatis oleh sistem JITU (Layanan Perizinan Terpadu Kabupaten Banjarnegara). 
                        Segala bentuk pemalsuan terhadap dokumen ini dapat dipidana sesuai hukum yang berlaku di Republik Indonesia.
                    </p>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="text-center">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-blue-600 font-bold hover:text-blue-700 transition-colors text-sm">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</x-front-layout>
