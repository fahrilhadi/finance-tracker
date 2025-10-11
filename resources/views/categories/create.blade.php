<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kategori Transaksi') }}
            </h2>
            <div>
                <x-primary-link href="{{ route('categories.create') }}">
                    <i class='bx bx-plus'></i> Tambah
                </x-primary-link>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg bg-green-50 text-green-700 px-4 py-3 ring-1 ring-green-200 mb-3">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-50 text-red-700 px-4 py-3 ring-1 ring-red-200 mb-3">
                    {{ session('error') }}
                </div>
            @endif

            @foreach ($categories as $item)
                <div class="bg-white border border-gray-200 rounded-xl p-6 mb-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $item->name }}</h3>
                            <span class="text-sm {{ $item->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                Tipe: {{ ucfirst($item->type) }}
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('categories.edit', $item->id) }}"
                                class="text-xl text-gray-600 hover:text-gray-900 transition ease-in-out duration-150">
                                <i class="bx bx-edit"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $item->id) }}" method="post"
                                onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-xl text-red-600 hover:text-red-800 transition ease-in-out duration-150">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>