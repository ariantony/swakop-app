<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use Throwable;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Transaction/Index')->with([
            'products' => Product::has('price')->get(),
        ]);
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
        $request->validate([
            'transactions.*.product_id' => 'required|exists:products,id',
            'transactions.*.qty' => 'required|integer|min:1',
            'transactions.*.type' => 'required|string|in:unit,box,carton',
        ]);

        $productIds = array_unique(array_column($request->transactions, 'product_id'));
        $products = Product::whereIn('id', $productIds)->get();

        $transactionByProducts = [];

        foreach ($request->transactions as $transaction) {
            $product = $products->where('id', $transaction['product_id'])->first();

            $transactionByProducts[$product->id] = array_merge(array_key_exists($product->id, $transactionByProducts) ? $transactionByProducts[$product->id] : [], [
                'product_id' => $product->id,
                'type' => 'sell',
                'qty_' . $transaction['type'] => $transaction['qty'],
                'cost_' . $transaction['type'] => match ($transaction['type']) {
                    'unit' => $product->price->price_per_unit,
                    'box' => $product->price->price_per_box,
                    'carton' => $product->price->price_per_carton,
                },
            ]);
        }

        // e.g product A, unit is out of stock
        $format = 'product %s, %s is out of stock';

        foreach ($transactionByProducts as $transaction) {
            $product = $products->where('id', $transaction['product_id'])->first();

            if ($product->stock_unit < $transaction['qty_unit']) {
                return redirect()->back()->with('error', sprintf(
                    $format, $product->name, 'unit',
                ));
            }

            if (array_key_exists('qty_box', $transaction) && $product->stock_box < $transaction['qty_box']) {
                return redirect()->back()->with('error', sprintf(
                    $format, $product->name, 'box',
                ));
            }

            if (array_key_exists('qty_box', $transaction) && $product->stock_carton < $transaction['qty_carton']) {
                return redirect()->back()->with('error', sprintf(
                    $format, $product->name, 'carton',
                ));
            }
        }

        $total = array_reduce($transactionByProducts, fn (float $last, array $transaction) => (
            $last + ($transaction['qty_unit'] * $transaction['cost_unit'] + @$transaction['qty_box'] * @$transaction['cost_box'] + @$transaction['qty_carton'] * @$transaction['cost_carton'])
        ), 0);
        
        $tx = Transaction::create([
            'user_id' => $request->user()->id,
            'total_cost' => $total,
        ]);

        if ($tx) {
            /**
             * @var \Throwable $error
             */
            $error = null;

            DB::transaction(function () use (&$error, &$transactionByProducts, &$tx) {
                try {
                    collect($transactionByProducts)->each(fn ($transaction) => $tx->details()->create($transaction));
                } catch (Throwable $e) {
                    $error = $e;

                    return throw $e;
                }
            });
            
            if ($error) {
                return redirect()->back()->with('error', $error->getMessage());
            }

            return redirect()->back()->with('success', 'Transaksi berhasil');
        }

        return redirect()->back()->with('error', 'Transaksi gagal. Silahkan coba lagi.');
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

    /**
     * Transaction History.
     *
     * @return \Illuminate\Http\Response
     */
    public function history()
    {
        return Inertia::render('Transaction/History');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request)
    {
        $model = new Transaction();
        $columns = array_filter($model->getFillable(), fn ($column) => !in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'order.key' => 'nullable|string',
            'order.dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ]);

        return Transaction::with('details')->whereRelation('details', 'type', 'sell')->where(function (Builder $query) use (&$request, &$model, &$columns) {
            $search = '%' . $request->input('search') . '%';

            foreach ($columns as $column) {
                $query->orWhere($column, 'like', $search);
            }
        })
        ->orderBy($request->input('order.key', 'created_at') ?: 'created_at', $request->input('order.dir', 'desc') ?: 'desc')
        ->paginate($request->input('per_page', 10));
    }

    /**
     * @param \App\Models\Transaction $transaction
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function detailPaginate(Request $request, Transaction $transaction)
    {
        $model = new Detail();
        $columns = array_filter($model->getFillable(), fn ($column) => !in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'order.key' => 'nullable|string|in:' . join(',', $columns),
            'order.dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ]);

        return $transaction->details()->with('product')->where(function (Builder $query) use (&$request, &$model, &$columns) {
            $search = '%' . $request->input('search') . '%';

            foreach ($columns as $column) {
                $query->orWhere($column, 'like', $search);
            }
        })
        ->orderBy($request->input('order.key', 'created_at') ?: 'created_at', $request->input('order.dir', 'asc') ?: 'asc')
        ->paginate($request->input('per_page', 10));
    }
}
