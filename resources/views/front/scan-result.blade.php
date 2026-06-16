<x-front-layout>
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
                        @if($perizinan->status === 'approved')
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
                                    <p class="text-gray-800 font-bold">{{ $perizinan->approved_at ? $perizinan->approved_at->format('d/m/Y') : '-' }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest block mb-1">Masa Aktif s/d</label>
                                    <p class="text-gray-800 font-bold {{ ($perizinan->masa_aktif && $perizinan->masa_aktif->isPast()) ? 'text-red-600' : '' }}">
                                        {{ $perizinan->masa_aktif ? $perizinan->masa_aktif->format('d/m/Y') : '-' }}
                                    </p>
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
                                @php
                                    $kadinValidasi = $perizinan->validasiRecords->filter(fn($v) => $v->validationFlow && $v->validationFlow->role === 'kadin')->first();
                                    $signedByKadin = ($kadinValidasi && $kadinValidasi->status === 'approved' && !empty($perizinan->file_izin_tte));
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

                                @php
                                    $opdValidasiCount = $perizinan->validasiRecords->filter(fn($v) => $v->validationFlow && $v->validationFlow->role === 'kepala_opd' && $v->status === 'approved')->count();
                                    $isMulti = $perizinan->perijinan->is_multi_opd;
                                @endphp

                                <div class="flex items-center justify-between p-4 rounded-2xl border {{ $opdValidasiCount > 0 ? 'bg-green-50 border-green-100 text-green-800' : 'bg-gray-50 border-gray-200 text-gray-400' }}">
                                    <div class="flex items-center gap-3">
                                        <i class="fas {{ $opdValidasiCount > 0 ? 'fa-check-double' : 'fa-minus-circle' }} text-xl"></i>
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-tight">Kepala OPD Teknis</p>
                                            <p class="text-[10px] font-medium opacity-80">
                                                @if($opdValidasiCount > 0)
                                                    Telah Ditandatangani ({{ $opdValidasiCount }} OPD)
                                                @else
                                                    Belum/Tidak Ada TTE
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @if($opdValidasiCount > 0)
                                        <i class="fas fa-shield-check text-2xl opacity-40"></i>
                                    @endif
                                </div>
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
