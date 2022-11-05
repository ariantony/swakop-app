<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Product/Index')->with([
            'groups' => Group::orderBy('name')->get(),
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function onlyName()
    {
        return Product::select(['id', 'name', 'code', 'barcode'])->without(['buy', 'sell', 'returnBuy', 'retur', 'group', 'price', 'from', 'to'])
                ->orderBy('name')
                ->get()
                ->map(function (Product $product) {
                    return $product->only([
                        'id', 'code', 'barcode', 'name'
                    ]);
                });
    }
    /**
     * @return \Illuminate\Http\Response
     */
    public function whereHasStock()
    {
        return Product::without(['group'])->with(['buy', 'sell', 'returnBuy', 'retur', 'from', 'to', 'price'])->orderBy('name')->get()->map(function (Product $product) {
            return $product->only([
                'id', 'code', 'barcode', 'name', 'stock_unit', 'stock_box', 'stock_carton', 'price',
            ]);
        })->map(function ($product) {
            return array_merge($product, [
                'price' => collect($product['price'])->only([
                    'price_per_unit',
                    'price_per_box',
                    'price_per_carton',
                    'variable_costs',
                ])
            ]);
        })->filter(function ($product) {
            return $product['stock_unit'] > 0;
        })->values();
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function whereHasPrice()
    {
        return Product::select(['id', 'name', 'code', 'barcode'])->with(['buy', 'sell', 'returnBuy', 'retur', 'group', 'price', 'from', 'to'])
                ->whereRelation('price', 'price_per_unit', '>', 0)
                ->orderBy('name')
                ->get()
                ->map(function (Product $product) {
                    return $product->only([
                        'id', 'code', 'barcode', 'name', 'price'
                    ]);
                });
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function withoutGroupAndPrice(Request $request)
    {
        return Product::without(['group', 'price'])
                        ->where(function (Builder $query) use ($request) {
                            $search = '%' . $request->q . '%';

                            $query->where('barcode', 'like', $search)
                                    ->orWhere('name', 'like', $search);
                        })
                        ->with(['buy', 'sell', 'returnBuy', 'retur', 'from', 'to'])
                        ->get(['id', 'code', 'name', 'barcode'])
                        ->map(function (Product $product) {
                            return $product->only([
                                'id', 'code', 'barcode', 'name', 'stock_unit',
                            ]);
                        });
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request)
    {
        $model = new Product();
        $columns = array_filter($model->getFillable(), fn ($column) => ! in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'order.key' => 'nullable|string|in:' . join(',', $columns),
            'order.dir' => 'nullable|string|in:asc,desc',
        ]);

        return Product::with(['buy', 'sell', 'returnBuy', 'retur', 'from', 'to', 'price'])
                        ->where(function (Builder $query) use (&$request, &$columns) {
                            $search = '%' . $request->input('search') . '%';

                            foreach ($columns as $column) {
                                $query->orWhere($column, 'like', $search);
                            }
                        })
                        ->orderBy($request->input('order.key') ?: 'name', $request->input('order.dir') ?: 'asc')
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
        $post = $request->validate([
            // 'code' => 'required|unique:products',
            'name' => 'required|string',
            // 'barcode' => 'required|string|unique:products',
            'group_id' => 'required|integer|exists:groups,id',
        ]);

        $post = $request->only([
            'code', 'name', 'barcode', 'group_id',
        ]);

        if ($product = Product::create($post)) {
            return redirect()->back()->with('success', 'Produk berhasil dibuat.');
        }

        return redirect()->back()->with('error', 'Gagal membuat produk.');
    }

    /**
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function price(Product $product)
    {
        return [
            'price' => $product->price,
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $post = $request->validate([
            // 'code' => ['required', Rule::unique('products')->ignore($product->id)],
            'name' => 'required|string',
            // 'barcode' => ['required', 'string', Rule::unique('products')->ignore($product->id)],
            'group_id' => ['required', 'integer', 'exists:groups,id'],
        ]);

        $post = $request->only([
            'code', 'name', 'barcode', 'group_id',
        ]);
        
        if ($product->update($post)) {
            return redirect()->back()->with('success', 'Produk berhasil diperbaharui.');
        }

        return redirect()->back()->with('error', 'Gagal memperbaharui produk.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->delete()) {
            Log::info('delete product ', [
                'data' => $product,
            ]);

            return redirect()->back()->with('success', 'Produk berhasil dihapus.');
        }

        return redirect()->back()->with('success', 'Gagal menghapus produk.');
    }

    /**
     * Print page per group.
     *
     * @return \Illuminate\Http\Response
     */
    public function print()
    {
        return Inertia::render('Product/Print')->with([
            'groups' => Group::orderBy('name')->get(),
        ]);
    }

    /**
     * Generate print page per group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateGroup(Request $request)
    {
        $post = $request->validate([
            'group_id' => 'required|integer|exists:groups,id',
        ]);

        $products = Product::with('group')->where('group_id', $post['group_id'])->orderBy('name')->get();

        if ($products->count() === 0) {
            return redirect()->back()->with('error', 'Tidak ada produk dalam kelompok barang ini.');
        }

        if ($request->method() === 'GET') {
            return Inertia::render('Product/Print/IframeGroup', [
                'products' => $products,
                'group' => $products->first()->group
            ]);
        }

        return Inertia::render('Product/Print/Group', [
            'products' => $products,
            'group' => $products->first()->group
        ]);
    }

    /**
     * Generate print page for price.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generatePrice(Request $request)
    {
        $post = $request->validate([
            'products' => 'required',
        ]);
        
        if (in_array(0, $post['products'])) {
            $products = Product::with(['group', 'price', 'buy', 'sell', 'returnBuy', 'retur', 'from', 'to'])->orderBy('name')->get();
        } else {
            $products = Product::with(['group', 'price', 'buy', 'sell', 'returnBuy', 'retur', 'from', 'to'])->orderBy('name')->find($post['products']);
        }

        if ($request->method() === 'GET') {
            return view('print/product-price', [
                'products' => $products,
                'ids' => $post['products'],
            ]);
        }

        return Inertia::render('Product/Print/Price', [
            'products' => $products,
            'ids' => $post['products'],
        ]);
    }

    /**
     * Edit stock, make false transaction.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function editStock(Request $request)
    {
        $post = $request->validate([
            'product' => 'required|integer|exists:products,id',
            'type' => 'required|string',
            'qty' => 'required|integer',
        ]);

        $product = Product::findOrFail($request->product);
        
        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'payment_method' => 'cash',
                'total_cost' => 0,
                'pay' => 0,
                'note' => mb_strtoupper("edit $request->type"),
            ]);

            $detail = $transaction->details()->create([
                'product_id' => $product->id,
                'type' => $request->type,
                'qty_unit' => $request->qty,
                'cost_unit' => 0,
                'subtotal' => 0,
            ]);

            Log::info('EDIT STOCK = ', [
                'transaction' => $transaction,
                'detail' => $detail,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Edit stok produk berhasil.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal edit stok produk.');
        }
        return redirect()->back()->with('error', 'Internal Server Error.');
    }
}
