<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with('category', 'items')
            ->where('user_id', Auth::user()->id)
            ->latest()->paginate(12);

        $totalMonthly = Transaction::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return view('transactions.index', compact('transactions', 'totalMonthly'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::select(['id', 'name', 'type'])
            ->orderBy('name')->get();
        $defaultType = 'expense';

        return view('transactions.create', compact('categories'));
    }

    /**
     * Flow manual (tanpa struk)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'title' => 'required|string|max:100',
            'note' => 'nullable|string',
            'items.*.name' => 'nullable|string',
            'items.*.qty' => 'nullable|numeric',
            'items.*.price' => 'nullable|numeric',
        ], [
            'type.requuired' => 'Tipe transaksi wajib diisi.',
            'type.in' => 'Tipe transaksi tidak valid.',
            'category_id.required' => 'Kategori wajib diisi.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Tanggal tidak valid.',
            'amount.required' => 'Jumlah wajib diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'title.required' => 'Judul wajib diisi.',
            'title.string' => 'Judul harus berupa teks.',
            'title.max' => 'Judul maksimal 100 karakter.',
            'note.string' => 'Keterangan harus berupa teks.',
            'items.*.name.string' => 'Nama item harus berupa teks.',
            'items.*.qty.numeric' => 'Kuantitas item harus berupa angka.',
            'items.*.price.numeric' => 'Harga item harus berupa angka.',
        ]);

        $catType = Category::where('id', $validated['category_id'])->value('type');
        if ($catType !== $validated['type']) {
            return back()->withInput()->withErrors(['category_id' => 'Kategori tidak sesuai dengan tipe transaksi.']);
        }

        DB::transaction(function () use ($validated) {
            $tx = Transaction::create([
                'user_id'     => Auth::user()->id,
                'category_id' => $validated['category_id'],
                'title'       => $validated['title'],
                'type'        => $validated['type'],
                'date'        => $validated['date'],
                'amount'      => $validated['amount'],
                'note' => $validated['note'] ?? null,
            ]);

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $it) {
                    if (!empty($it['name'])) {
                        Item::create([
                            'transaction_id' => $tx->id,
                            'name'           => $it['name'],
                            'quantity'       => $it['qty'] ?? 1,
                            'price'          => $it['price'] ?? 0,
                            'subtotal'       => ($it['qty'] ?? 1) * ($it['price'] ?? 0),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi tersimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
