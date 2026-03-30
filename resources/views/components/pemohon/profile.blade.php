<div class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-amber-100">
        <h3 class="font-bold text-gray-800">Profil Pemohon</h3>
        <p class="text-sm text-gray-500">Identitas singkat pemohon</p>
    </div>
    <div class="p-6">
        <div class="flex items-center gap-4 mb-4">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=78350f&color=fff" 
                class="w-14 h-14 rounded-full border border-amber-200" 
                alt="{{ $user->name }}">
            <div>
                <p class="font-extrabold text-gray-800">{{ $user->name }}</p>
                <p class="text-xs text-gray-500">NIK: {{ $user->nip ?? '-' }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-3 text-sm">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                <span class="block text-gray-400 text-xs uppercase">Email</span>
                <span class="font-semibold text-gray-800">{{ $user->email }}</span>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                <span class="block text-gray-400 text-xs uppercase">Telepon</span>
                <span class="font-semibold text-gray-800">{{ $user->no_hp ?? '-' }}</span>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                <span class="block text-gray-400 text-xs uppercase">Status Pemohon</span>
                <span class="font-semibold text-gray-800">
                    @if($user->status_pemohon === 'badan_usaha')
                        <i class="fas fa-building text-purple-600 mr-1"></i> Badan Usaha
                    @else
                        <i class="fas fa-user text-amber-600 mr-1"></i> Perorangan
                    @endif
                </span>
            </div>
        </div>
        <a href="{{ route('profile.edit') }}" class="mt-4 block w-full text-center bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold py-2.5 rounded-xl transition-all text-sm">
            <i class="fas fa-edit mr-1"></i> Edit Profil
        </a>
    </div>
</div>
