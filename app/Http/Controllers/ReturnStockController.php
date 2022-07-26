<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Throwable;

class ReturnStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Return/Stock/Index');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function products()
    {
        return Product::with(['buy', 'sell', 'returnBuy'])
                        ->without(['group'])
                        ->whereHas('details')
                        ->get()
                        ->filter(fn (Product $product) => $product->stock_unit > 0)
                        ->values();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'product' => 'required|integer|exists:products,id',
            'qty' => 'required|integer|min:0',
        ];

        Validator::make($request->all(), $rules)->after(function () use ($request) {
            $product = Product::find($request->product);

            Validator::make($request->all(), [
                'qty' => ['required', 'integer', 'min:0', 'max:' . $product->stock_unit],
            ])->validate();
        })->validate();

        $product = Product::find($request->product);

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'total_cost' => $product->price->cost_selling_per_unit * $request->qty,
            ]);
    
            $transaction->details()->create([
                'product_id' => $product->id,
                'type' => 'return buy',
                'cost_unit' => $product->price->cost_selling_per_unit,
                'qty_unit' => $request->qty,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Produk berhasil di retur');
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', __(
                $e->getMessage(),
            ));
        }

        return redirect()->back()->with('error', __(
            'Tidak dapat menyimpan data, coba kembali nanti',
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
