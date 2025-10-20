<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-900">Transaksi</h2>
            <x-primary-link href="{{ route('transactions.create') }}">
                <i class="bx bx-plus"></i> Tambah
            </x-primary-link>
        </div>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg bg-green-50 text-green-700 px-4 py-3 ring-1 ring-green-200 mb-3">
                    {{ session('success') }}
                </div>
            @endif

            @foreach ($transactions as $item)
                <a href="{{ route('transactions.show', $item) }}"
                    class="block bg-white border border-gray-200 mb-1 rounded-xl p-6 transition hover:border-green-500">
                    <div class="sm:flex items-center gap-2 sm:gap-6">
                        <div>
                            <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">{{ $item->title }}</h3>
                            <span class="text-xs md:text-sm text-gray-500">
                                {{ $item->date->translatedFormat('d F Y') }} &middot;
                                {{ $item->category->name ?? 'Tanpa Kategori' }}
                            </span>
                        </div>
                        <div class="sm:ms-auto mt-2 sm:mt-0">
                            <span
                                class="sm:ms-auto w-max block px-3 py-1 rounded-xl font-semibold text-xs sm:text-sm {{ $item->type == 'income' ? 'text-green-700 bg-green-200' : 'text-red-700 bg-red-200' }}">
                                {{ $item->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                            <p class="text-base sm:text-xl font-bold mt-1 text-gray-900 sm:text-end">
                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach

            <div>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>