<?php

namespace App\Http\Controllers;

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
            'product' => Product::get(),
        ]);
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

        return Product::where(function (Builder $query) use (&$request, &$columns) {
                            $search = '%' . $request->input('search') . '%';

                            foreach ($columns as $column) {
                                $query->orWhere($column, 'like', $search);
                            }
                        })
                        ->orderBy($request->input('order.key') ?: 'code', $request->input('order.dir') ?: 'asc')
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
            'qty_per_unit' => 'required|integer|min:0',
            'qty_per_box' => 'required|integer|min:0',
            'qty_per_carton' => 'required|integer|min:0',
        ]);

        if ($product = Product::create($post)) {
            return redirect()->back()->with('success', 'produk berhasil dibuat');
        }

        return redirect()->back()->with('error', 'gagal membuat produk');
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
            'qty_per_unit' => 'required|integer|min:0',
            'qty_per_box' => 'required|integer|min:0',
            'qty_per_carton' => 'required|integer|min:0',
        ]);

        if ($product->update($post)) {
            return redirect()->back()->with('success', 'produk berhasil diperbaharui');
        }

        return redirect()->back()->with('error', 'gagal memperbaharui produk');
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
            return redirect()->back()->with('success', 'produk berhasil dihapus');
        }

        return redirect()->back()->with('success', 'gagal menghapus produk');
    }
}
