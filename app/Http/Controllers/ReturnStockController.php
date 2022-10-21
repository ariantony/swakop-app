<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function products(Request $request)
    {
        Detail::$withoutAppends = true;
        return Product::with(['buy', 'sell', 'returnBuy', 'retur', 'from', 'to'])
                        ->where(function (Builder $query) use ($request) {
                            $search = '%' . $request->q . '%';

                            $query->where('barcode', 'like', $search)
                                    ->orWhere('name', 'like', $search);
                        })
                        ->without(['group', 'price'])
                        ->whereHas('details')
                        ->get()
                        ->filter(fn (Product $product) => $product->stock_unit > 0)
                        ->values();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'per_page' => 'nullable|integer|max:1000',
            'order.key' => 'nullable|string',
            'order.dir' => 'nullable|in:asc,desc',
        ]);

        return Transaction::whereRelation('details', 'type', 'return buy')
                            ->where(function (Builder $query) use ($request) {
                                $search = '%' . $request->search . '%';
                                
                                $query->whereRelation('user', 'name', 'like', $search)
                                        ->orWhereRelation('details', 'qty_unit', 'like', $search)
                                        ->orWhereRelation('details', 'cost_unit', 'like', $search)
                                        ->orWhere('total_cost', 'like', $search);
                            })
                            ->orderBy($request->input('order.key') ?: 'created_at', $request->input('order.dir') ?: 'asc')
                            ->paginate($request->per_page ?: 10);
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
            'qty' => 'required|integer|min:1',
            'note' => 'required|string',
        ];

        Validator::make($request->all(), $rules)->after(function () use ($request) {
            $product = Product::find($request->product);

            Validator::make($request->all(), [
                'qty' => ['required', 'integer', 'min:1', 'max:' . $product->stock_unit],
            ])->validate();
        })->validate();

        $product = Product::find($request->product);

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'total_cost' => $product->price->cost_selling_per_unit * $request->qty,
                'note' => $request->note,
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
