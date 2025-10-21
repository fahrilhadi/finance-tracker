<x-app-layout>
    <div x-data="reviewExtracted({{ json_encode($prefill) }})">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-900">Review Hasil Ekstraksi Struk</h2>
        </x-slot>

        <div>
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Preview Struk -->
                    <div class="lg:col-span-4">
                        <div class="rounded-xl bg-white p-3 ring-1 ring-gray-200">
                            <img src="{{ asset('storage/' . $prefill['receipt_path']) }}"
                                class="rounded-lg w-full object-contain max-h-[560px]" alt="Receipt">
                        </div>
                    </div>

                    <!-- Form Konfirmasi -->
                    <div class="lg:col-span-8">
                        <div class="rounded-xl bg-white p-6 ring-1 ring-gray-200">
                            <form method="POST" action="{{ route('transactions.confirm') }}" class="space-y-6"
                                id="form">
                                @csrf
                                <input type="hidden" name="receipt_path" value="{{ $prefill['receipt_path'] }}">
                                <input type="hidden" name="type" value="{{ $prefill['type'] }}">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="category_id" :value="__('Kategori')" />
                                        <x-select-input name="category_id" id="category_id" class="mt-1 w-full block">
                                            @foreach ($categories as $c)
                                                <option value="{{ $c->id }}" @selected($prefill['category_id'] == $c->id)>
                                                    {{ $c->name }}</option>
                                            @endforeach
                                        </x-select-input>
                                    </div>
                                    <div>
                                        <x-input-label for="date" :value="__('Tanggal')" />
                                        <x-text-input id="date" type="date" name="date" x-model="date"
                                            class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <x-input-label for="amount" :value="__('Total')" />
                                        <x-text-input id="amount" type="number" name="amount" x-model="amount"
                                            class="mt-1 block w-full" required />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="title" :value="__('Judul')" />
                                    <x-text-input id="title" type="text" name="title" x-model="title"
                                        class="mt-1 block w-full" required maxlength="100" />
                                </div>

                                <div>
                                    <x-input-label for="note" :value="__('Catatan')" />
                                    <x-textarea id="note" name="note" x-model="note" rows="2"
                                        class="mt-1 block w-full" />
                                </div>

                                <div class="pt-4 border-t border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-gray-900">Item Terdeteksi</h3>
                                        <button type="button" @click="addItem()"
                                            class="inline-flex items-center gap-1 rounded-lg bg-gray-900 px-3 py-2 text-sm text-white hover:bg-black">
                                            + Tambah Item
                                        </button>
                                    </div>

                                    <template x-for="(it, idx) in items" :key="idx">
                                        <div class="mt-3 grid grid-cols-1 md:grid-cols-12 gap-3">
                                            <div class="md:col-span-6">
                                                <label class="block text-xs font-medium text-gray-600">Nama Item</label>
                                                <input :name="`items[${idx}][name]`" x-model="it.name"
                                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-medium text-gray-600">Qty</label>
                                                <input type="number" step="0.01" :name="`items[${idx}][qty]`"
                                                    x-model.number="it.qty"
                                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-xs font-medium text-gray-600">Harga</label>
                                                <input type="number" step="0.01" :name="`items[${idx}][price]`"
                                                    x-model.number="it.price"
                                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                            <div class="md:col-span-1 flex items-end">
                                                <button type="button" @click="removeItem(idx)"
                                                    class="w-full md:w-auto rounded-lg border border-red-300 px-3 py-2 text-sm text-red-700 hover:bg-red-50">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Ringkasan kecil -->
                                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                        <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-200">
                                            <div class="text-gray-600">Jumlah Item</div>
                                            <div class="text-gray-900 font-semibold" x-text="items.length"></div>
                                        </div>
                                        <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-200">
                                            <div class="text-gray-600">Subtotal Item</div>
                                            <div class="text-gray-900 font-semibold" x-text="currency(sumItems())">
                                            </div>
                                        </div>
                                        <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-200">
                                            <div class="text-gray-600">Total (Form)</div>
                                            <div class="text-gray-900 font-semibold" x-text="currency(amount||0)"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row items-center gap-3 pt-2">
                                    <x-primary-button class="w-full md:w-max" type="submit" id="submitBtn">
                                        <span id="iconbtn">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </span>
                                        <span id="spinner" class="hidden">
                                            <i class='bx bx-loader-alt bx-spin bx-rotate-90'></i>
                                        </span>
                                        <span id="textBtn">Simpan</span>
                                    </x-primary-button>
                                    <x-secondary-link class="w-full md:w-max"
                                        href="{{ route('transactions.create') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                        </svg>
                                        Kembali
                                    </x-secondary-link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                function reviewExtracted(prefill) {
                    return {
                        date: prefill.date,
                        amount: prefill.amount,
                        title: prefill.title,
                        note: prefill.note,
                        items: prefill.items || [],
                        addItem() {
                            this.items.push({
                                name: '',
                                qty: 1,
                                price: null
                            });
                        },
                        removeItem(i) {
                            this.items.splice(i, 1);
                        },
                        sumItems() {
                            return this.items.reduce((s, it) => {
                                const q = Number(it.qty || 0),
                                    p = Number(it.price || 0);
                                return s + (q * p);
                            }, 0);
                        },
                        currency(n) {
                            try {
                                return new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    maximumFractionDigits: 0
                                }).format(n);
                            } catch (e) {
                                return 'Rp ' + (n || 0).toLocaleString('id-ID');
                            }
                        }
                    }
                }

                document.getElementById('form').addEventListener('submit', function(e) {
                    const btn = document.getElementById('submitBtn');
                    const iconbtn = document.getElementById('iconbtn');
                    const spinner = document.getElementById('spinner');
                    const textBtn = document.getElementById('textBtn');

                    textBtn.innerText = 'Memproses...';
                    spinner.classList.remove('hidden');

                    iconbtn.classList.add('hidden');

                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                });
            </script>
        @endpush
    </div>
</x-app-layout>