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
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Inertia::render('Transaction/Index', [
            'transaction' => Transaction::with('details')->find($request->id),
        ]);
    }

    /**
     * @param \Illuminate\Support\Collection $products
     * @param array $transactions
     * @return float
     */
    private function calculateTotalTransactionPrice($products, $transactions)
    {
        $total = 0;

        foreach ($transactions as $id => $transaction) {
            $product = $products->find($id);

            $total += $this->getPriceByTransactionWithVariable($product, $transaction);
        }

        return $total;
    }

    /**
     * @param \App\Models\Product $product
     * @param array $transaction
     * @return float
     */
    private function getPriceByTransactionWithVariable(Product $product, array $transaction)
    {
        $qty = array_key_exists('qty_unit', $transaction) ? $transaction['qty_unit'] : $transaction['qty'];
        $variableCosts = $product->price->variableCosts;

        if ($variableCosts->count() > 0) {
            $variable = $variableCosts->where('min_qty', '<=', $qty)->first();
            if ($variable) {
                return $variable->price * $qty;
            }
        }

        return $product->price->price_per_unit * $qty;
    }

    /**
     * @param \App\Models\Product $product
     * @param int $qty
     * @return float
     */
    private function getProductPricePerUnit(Product $product, int $qty)
    {
        $variableCosts = $product->price->variableCosts;
        
        if ($variableCosts->count() > 0) {
            $variable = $variableCosts->where('min_qty', '<=', $qty)->first();
            if ($variable) {
                return $variable->price;
            }
        }

        return $product->price->price_per_unit;
    }

    /**
     * @param float $total
     * @return float
     */
    private function addQrisTax(float $total)
    {
        return ceil($total + ($total * 0.007));
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
            'pay' => 'required|integer|min:0',
        ]);

        $productIds = array_unique(array_column($request->transactions, 'product_id'));
        $products = Product::whereIn('id', $productIds)->get();

        $transactionByProducts = [];

        foreach ($request->transactions as $transaction) {
            $product = $products->where('id', $transaction['product_id'])->first();

            $transactionByProducts[$product->id] = array_merge(array_key_exists($product->id, $transactionByProducts) ? $transactionByProducts[$product->id] : [], [
                'product_id' => $product->id,
                'type' => 'sell',
                'price_id' => $product->price->id,
                'qty_unit' => $transaction['qty'],
                'cost_unit' => $this->getProductPricePerUnit($product, $transaction['qty']),
                'subtotal' => $this->getPriceByTransactionWithVariable($product, $transaction),
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
        }

        $products = Product::find(array_keys($transactionByProducts));

        $total = $original = $this->calculateTotalTransactionPrice($products, $transactionByProducts);
        
        if ($request->useQris) {
            $total = $this->addQrisTax($total);
        }

        $tx = Transaction::firstOrCreate([
            'user_id' => $request->user()->id,
            'total_cost' => $total,
            'pay' => $request->useQris ? $original : $request->pay,
            'payment_method' => $request->useQris ? 'qris' : 'cash',
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]);

        if ($tx) {
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
                Log::error('transaction ', [
                    'data' => json_encode($transactionByProducts),
                ]);
                return redirect()->back()->with('error', $error->getMessage());
            }
            Log::info('transaction ', [
                'data' => json_encode($transactionByProducts),
            ]);
            return redirect()->back()->with('success', 'Transaksi berhasil');
        }
        return redirect()->back()->with('error', 'Transaksi gagal. Silahkan coba lagi.');
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
        $request->validate([
            'search' => 'nullable|string',
            'order.key' => 'nullable|string',
            'order.dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ]);

        return Transaction::with('details')
                            ->join('users', 'users.id', '=', 'transactions.user_id')
                            ->when(!$request->user()->hasRole('admin'), fn ($query) => $query->where('user_id', $request->user()->id))
                            ->whereRelation('details', 'type', 'sell')
                            ->where(function (Builder $query) use (&$request) {
                                $search = '%' . $request->input('search') . '%';

                                return $query->where('users.name', 'like', $search)
                                            ->orWhere('transactions.total_cost', 'like', $search)
                                            ->orWhere('transactions.pay', 'like', $search)
                                            ->orWhere('transactions.payment_method', 'like', $search)
                                            ->orWhere('transactions.id', 'like', $search);
                            })
                            ->orderBy($request->input('order.key', 'transactions.created_at') ?: 'transactions.created_at', $request->input('order.dir', 'desc') ?: 'desc')
                            ->select([
                                'transactions.id',
                                'transactions.user_id',
                                'transactions.total_cost',
                                'transactions.pay',
                                'transactions.payment_method',
                                'transactions.created_at',
                                'users.name'
                            ])
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

    /**
     * @return \Illuminate\Http\Response
     */
    public function returnHistory()
    {
        return Inertia::render('Transaction/Return/History');
    }

    /**
     * @param \App\Models\Transaction $transaction
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function returnPaginate(Request $request, Transaction $transaction)
    {
        $model = new Transaction();
        $columns = array_filter($model->getFillable(), fn ($column) => !in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'order.key' => 'nullable|string',
            'order.dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ]);

        return Transaction::with('details')->whereRelation('details', 'type', 'return sell')->where(function (Builder $query) use (&$request, &$model, &$columns) {
            $search = '%' . $request->input('search') . '%';

            foreach ($columns as $column) {
                $query->orWhere($column, 'like', $search);
            }

            $query->orWhere('id', 'like', $search);
        })
        ->orderBy($request->input('order.key', 'updated_at') ?: 'updated_at', $request->input('order.dir', 'desc') ?: 'desc')
        ->paginate($request->input('per_page', 10));
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function retur()
    {
        return Inertia::render('Transaction/Return/Index');
    }

    /**
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function find(Transaction $transaction)
    {
        $find = Transaction::whereRelation('details', 'type', 'sell')->with(['details'])->find($transaction->id);

        if ($find) {
            return $find;
        }
        return response()->json([
            'message' => 'Transaksi tidak ditemukan.'
        ], 404);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function returnCreate(Request $request, Transaction $transaction)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        if ($transaction->details()->update(['type' => 'return sell']) && $transaction->update(['note' => $request->note])) {
            return redirect()->to("/transaction?id=$transaction->id")->with('success', __(
                'Transaksi berhasil di kembalikan, silahkan lakukan checkout kembali.',
            ));
        }

        return redirect()->back()->with('error', __(
            'Proses void penjualan gagal, coba lagi beberapa saat',
        ));
    }

    /**
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function returnPrint(Transaction $transaction)
    {
        $find = Transaction::with('details.product')->find($transaction->id);

        if ($find) {
            return Inertia::render('Transaction/Return/Print', [
                'transaction' => $find,
            ]);
        }

        return redirect()->back()->with('error', __(
            'Proses pengembalian gagal, coba lagi beberapa saat',
        ));
    }
    
    /**
     * Print the invoice of the transaction.
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function invoicePrint(Transaction $transaction)
    {
        $find = Transaction::with('details.product')->find($transaction->id);
        if ($find) {
            return Inertia::render('Transaction/Invoice', [
                'transaction' => $find,
                'totalItem' => $find->details->sum('qty_unit'),
            ]);
        }
        
        return redirect()->back()->with('error', __(
            'Proses pengembalian gagal, coba lagi beberapa saat',
        ));
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function latestInvoicePrint()
    {
        return $this->invoicePrint(
            Transaction::latest()->first(),
        );
    }
}
