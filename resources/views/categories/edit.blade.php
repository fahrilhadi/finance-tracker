<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Kategori {{ $category->name }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 ring-1 ring-gray-200">
                <form action="{{ route('categories.update', $category) }}" method="post" id="form">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="type" value="Tipe" />
                            <x-select-input id="type" name="type" class="w-full block mt-1" x-model="type"
                                required>
                                <option value="">— Pilih —</option>
                                <option value="income" @selected(old('type', $category->type) == 'income')>Income</option>
                                <option value="expense" @selected(old('type', $category->type) == 'expense')>Expense</option>
                            </x-select-input>

                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="name" value="Nama Kategori" />
                            <x-text-input id="name" type="text" name="name" class="w-full block mt-1"
                                value="{{ old('name', $category->name) }}" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row items-center gap-3 pt-2">
                        <x-primary-button class="w-full md:w-max" id="submitBtn">
                            <span id="iconbtn">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </span>
                            <span id="spinner" class="hidden">
                                <i class='bx bx-loader-alt bx-spin bx-rotate-90'></i>
                            </span>
                            <span id="textBtn">Simpan</span>
                        </x-primary-button>
                        <x-secondary-link class="w-full md:w-max" href="{{ route('categories.index') }}">
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

    @include('components.script-loading')
</x-app-layout>