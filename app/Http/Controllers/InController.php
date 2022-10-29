<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use App\Models\Group;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class InController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('In/Index')->with([
            'groups' => Group::get(),
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request)
    {
        $model = new Detail();
        $columns = array_filter($model->getFillable(), fn ($column) => !in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'order.key' => 'nullable|string',
            'order.dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ]);

        return Detail::with(['product', 'transaction'])
                        ->where('type', 'buy')
                        ->where(function (Builder $query) use ($request) {
                            $search = '%' . $request->search . '%';

                            // $query->where(function (Builder $query) use ($search) {
                            //     $query->orWhere('qty_unit', 'like', $search)
                            //             ->orWhere('cost_unit', 'like', $search)
                            //             ->orWhere('created_at', 'like', $search);
                            // });

                            $query->orWhereRelation('product', 'name', 'like', $search);
                                    // ->orWhereRelation('transaction', function (Builder $query) use ($search) {
                                    //     $query->orWhereRelation('user', 'name', 'like', $search);
                                    // });
                        })
                        ->orderBy($request->input('order.key') ?: 'created_at', $request->input('order.dir') ?: 'desc')
                        ->paginate($request->per_page);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            // 'code' => 'required|string|unique:products,code',
            'name' => 'required|string',
            // 'barcode' => 'required|string|unique:products,barcode',
            'group_id' => 'required|integer|exists:groups,id',
            'price.buy.unit' => 'required|integer',
            'price.sell.unit' => 'required|integer',
            'qty' => 'required|integer',
            'variables.*.qty' => 'required|integer|min:1',
            'variables.*.min_qty' => 'required|integer',
            'variables.*.price' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::create([
                'code' => $request->code,
                'name' => $request->name,
                'barcode' => $request->barcode,
                'group_id' => $request->group_id,
            ]);

            $price = $product->prices()->create([
                'cost_selling_per_unit' => $request->input('price.buy.unit', 0),
                'cost_selling_per_box' => $request->input('price.buy.box', 0),
                'cost_selling_per_carton' => $request->input('price.buy.carton', 0),
                'price_per_unit' => $request->input('price.sell.unit', 0),
                'price_per_box' => $request->input('price.sell.box', 0),
                'price_per_carton' => $request->input('price.sell.carton', 0),
            ]);

            if ($request->input('variables')) {
                $price->variableCosts()->insert(array_map(
                    fn ($variable) => array_merge([
                        'price_id' => $price->id,
                        'qty' => $variable['qty'],
                        'price' => $variable['price'],
                        'min_qty' => $variable['min_qty'] ?: $variable['qty'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]), $request->input('variables', [])
                ));
            }

            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'total_cost' => $price->cost_selling_per_unit * $request->qty,
            ]);

            $detail = $transaction->details()->create([
                'product_id' => $product->id,
                'price_id' => $price->id,
                'type' => 'buy',
                'qty_unit' => $request->qty,
                'cost_unit' => $price->cost_selling_per_unit,
                'subtotal' => $price->cost_selling_per_unit * $request->qty,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'produk berhasil dibuat dan stock berhasil di tambahkan');
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $request->validate([
            'product' => 'required|integer|exists:products,id',
            'qty' => 'required|integer',
        ]);

        $product = Product::findOrFail($request->product);
        $price = $product->price;

        if (empty($price)) {
            return redirect()->back()->with('error', 'harga produk belum ditentukan');
        }

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'total_cost' => $request->qty * $price->cost_selling_per_unit,
            ]);

            $detail = $transaction->details()->create([
                'product_id' => $product->id,
                'price_id' => $price->id,
                'type' => 'buy',
                'qty_unit' => $request->qty,
                'cost_unit' => $price->cost_selling_per_unit,
                'subtotal' => $request->qty * $price->cost_selling_per_unit,
            ]);

            DB::commit();

            Log::info('in ', [
                'transaction' => $transaction,
                'detail' => $detail,
            ]);

            return redirect()->back()->with('success', 'stock berhasil ditambahkan');
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param \App\Models\Detail $detail
     * @return \Illuminate\Http\Response
     */
    public function show(Detail $detail)
    {
        return $detail;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:details,id',
        ]);

        $detail = Detail::find($request->id);
        $transaction = $detail->transaction;

        if (empty($detail)) {
            return redirect()->back()->with('error', 'Detail data transaksi tidak ditemukan.');
        }

        if (($detail->product->stock_unit - $detail->qty_unit) < 0) {
            return redirect()->back()->with('error', 'Stock product akan minus jika data terhapus dihapus.');
        }

        
        DB::beginTransaction();
        try {
            $detail->delete();

            Log::info('delete in ', [
                'transaction' => $transaction,
                'detail' => $detail,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Stock berhasil dihapus.');
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
