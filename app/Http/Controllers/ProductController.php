<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
            'groups' => Group::get(),
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function whereHasStock()
    {
        return Product::without(['group'])->with(['buy', 'sell'])->get()->map(function (Product $product) {
            return $product->only([
                'id', 'code', 'barcode', 'name', 'stock_unit', 'stock_box', 'stock_carton', 'price'
            ]);
        })->map(function ($product) {
            return array_merge($product, [
                'price' => collect($product['price'])->only([
                    'price_per_unit',
                    'price_per_box',
                    'price_per_carton',
                ])
            ]);
        })->filter(function ($product) {
            return $product['stock_unit'] > 0;
        })->values();
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function withoutGroupAndPrice()
    {
        return Product::without(['group', 'price'])->get(['id', 'code', 'name', 'barcode'])->map(function (Product $product) {
            return $product->only([
                'id', 'code', 'barcode', 'name',
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

        return Product::with(['buy', 'sell'])
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
            'code' => 'required|unique:products',
            'name' => 'required|string',
            'barcode' => 'required|string|unique:products',
            'group_id' => 'required|integer|exists:groups,id',
        ]);

        if ($product = Product::create($post)) {
            return redirect()->back()->with('success', 'Produk berhasil dibuat.');
        }

        return redirect()->back()->with('error', 'Gagal membuat produk.');
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
            'code' => ['required', Rule::unique('products')->ignore($product->id)],
            'name' => 'required|string',
            'barcode' => ['required', 'string', Rule::unique('products')->ignore($product->id)],
            'group_id' => ['required', 'integer', 'exists:groups,id'],
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
            'groups' => Group::get(),
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

        $products = Product::with('group')->where('group_id', $post['group_id'])->get();

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

        $products = Product::find($post['products']);
        
        return Inertia::render('Product/Print/Price', [
            'products' => $products,
        ]);
    }
}
