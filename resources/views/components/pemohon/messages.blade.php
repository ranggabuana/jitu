<div class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-amber-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-800">Pesan dari Dinas</h3>
            <p class="text-sm text-gray-500">Informasi dan tindak lanjut dari admin perizinan.</p>
        </div>
        <div class="hidden md:flex items-center gap-2 text-xs text-gray-500">
            <i class="fas fa-bell"></i>
            <span id="msgCount">{{ count($messages) }} Pesan</span>
        </div>
    </div>
    <div class="divide-y divide-amber-100" id="messageList">
        @forelse($messages as $msg)
            <div class="p-4 hover:bg-amber-50 transition-colors {{ $msg['type'] === 'PERBAIKAN' ? 'bg-red-50' : '' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            @if($msg['type'] === 'PERBAIKAN')
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            @elseif($msg['type'] === 'SELESAI')
                                <i class="fas fa-check-circle text-green-500"></i>
                            @else
                                <i class="fas fa-info-circle text-amber-500"></i>
                            @endif
                            <h4 class="font-semibold text-gray-800 text-sm">{{ $msg['title'] }}</h4>
                        </div>
                        <p class="text-sm text-gray-600">{{ $msg['content'] }}</p>
                        <p class="text-xs text-gray-400 mt-2"><i class="fas fa-clock mr-1"></i> {{ $msg['date'] }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-inbox text-amber-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">Belum ada pesan</p>
            </div>
        @endforelse
    </div>
</div>
