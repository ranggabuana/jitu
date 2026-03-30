<x-layout>
    <x-slot:title>Detail Berita</x-slot:title>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Berita</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Informasi lengkap berita</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('berita.edit', $berita->id) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                    <i class="mdi mdi-pencil"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('berita.index') }}"
                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                    <i class="mdi mdi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-6xl">
        <!-- Header Image -->
        @if($berita->gambar)
            <div class="mb-6">
                <img src="{{ asset($berita->gambar) }}" alt="{{ $berita->judul }}" 
                    class="w-full h-96 object-cover rounded-lg shadow-md">
            </div>
        @endif

        <!-- Status Badge -->
        <div class="mb-4">
            @php
                $statusColors = [
                    'aktif' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    'tidak_aktif' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                ];
                $statusLabels = [
                    'aktif' => 'Aktif',
                    'tidak_aktif' => 'Tidak Aktif',
                ];
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$berita->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusLabels[$berita->status] ?? $berita->status }}
            </span>
        </div>

        <!-- Title -->
        <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">{{ $berita->judul }}</h2>

        <!-- Meta Info -->
        <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <i class="mdi mdi-eye text-orange-500"></i>
                <span>{{ $berita->views }} views</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="mdi mdi-account text-red-500"></i>
                <span>{{ $berita->user->name ?? 'Admin' }}</span>
            </div>
        </div>

        <!-- Content -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Konten Berita</h3>
            <div class="prose dark:prose-invert max-w-none">
                {!! $berita->konten !!}
            </div>
        </div>

        <!-- Additional Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Slug</p>
                <p class="text-gray-800 dark:text-gray-200 font-mono text-sm">{{ $berita->slug }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Dibuat pada</p>
                <p class="text-gray-800 dark:text-gray-200">{{ $berita->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Terakhir diupdate</p>
                <p class="text-gray-800 dark:text-gray-200">{{ $berita->updated_at->format('d F Y, H:i') }} WIB</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">ID Berita</p>
                <p class="text-gray-800 dark:text-gray-200 font-mono text-sm">#{{ $berita->id }}</p>
            </div>
        </div>
    </div>

    <style>
        .prose {
            line-height: 1.75;
            color: inherit;
        }
        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            color: inherit;
            font-weight: 600;
            margin-top: 1.5em;
            margin-bottom: 0.75em;
        }
        .prose h1 { font-size: 2em; }
        .prose h2 { font-size: 1.5em; }
        .prose h3 { font-size: 1.25em; }
        .prose h4 { font-size: 1.1em; }
        .prose p {
            margin-bottom: 1em;
        }
        .prose ul, .prose ol {
            margin-bottom: 1em;
            padding-left: 1.5em;
        }
        .prose ul {
            list-style-type: disc;
        }
        .prose ol {
            list-style-type: decimal;
        }
        .prose a {
            color: #3b82f6;
            text-decoration: underline;
        }
        .prose blockquote {
            border-left: 4px solid #e5e7eb;
            padding-left: 1em;
            margin: 1em 0;
            font-style: italic;
        }
        .dark .prose blockquote {
            border-left-color: #4b5563;
        }
        .prose table {
            width: 100%;
            border-collapse: collapse;
            margin: 1em 0;
        }
        .prose th, .prose td {
            border: 1px solid #e5e7eb;
            padding: 0.75em;
            text-align: left;
        }
        .dark .prose th, .dark .prose td {
            border-color: #4b5563;
        }
        .prose th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        .dark .prose th {
            background-color: #374151;
        }
        .prose img {
            max-width: 100%;
            height: auto;
            margin: 1em 0;
            border-radius: 0.5rem;
        }
        .prose strong {
            font-weight: 600;
        }
        .prose code {
            background-color: #f3f4f6;
            padding: 0.2em 0.4em;
            border-radius: 0.25rem;
            font-family: monospace;
            font-size: 0.875em;
        }
        .dark .prose code {
            background-color: #374151;
        }
    </style>
</x-layout>
