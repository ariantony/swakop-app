<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Price/Index');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request, Product $product)
    {
        $model = new Price();
        $columns = array_filter($model->getFillable(), fn ($column) => ! in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'order.key' => 'nullable|string',
            'order.dir' => 'nullable|in:asc,desc',
        ]);

        return $product->price()
                        ->where(function (Builder $query) use (&$request, &$columns) {
                            $search = '%' . $request->input('search') . '%';

                            foreach ($columns as $column) {
                                $query->orWhere($column, 'like', $search);
                            }
                        })
                        ->orderBy($request->input('order.key') ?: 'created_at', $request->input('order.dir') ?: 'asc')
                        ->paginate($request->input('per_page') ?: 10);
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
        $post = $request->validate([
            'product_id' => 'required|exists:products,id',
            'cost_selling_per_unit' => 'required|numeric',
            // 'cost_selling_per_box' => 'required|numeric',
            // 'cost_selling_per_carton' => 'required|numeric',
            'price_per_unit' => 'required|numeric',
            // 'price_per_box' => 'required|numeric',
            // 'price_per_carton' => 'required|numeric',
            'variables.*.qty' => 'required|integer',
            'variables.*.price' => 'required|numeric',
        ]);

        $post['cost_selling_per_box'] = 0;
        $post['cost_selling_per_carton'] = 0;
        $post['price_per_box'] = 0;
        $post['price_per_carton'] = 0;
        
        $product = Product::findOrFail($post['product_id']);

        $last = Price::where('product_id', $post['product_id'])->latest()->first();
        
        if ($last) {
            $last->update([
                'expire_date' => now(),
            ]);
        } 

        if ($price = Price::create($post)) {
            $product->variableCosts()->delete();
            $product->variableCosts()->insert(array_map(
                fn ($variable) => array_merge($variable, [
                    'product_id' => $product->id,
                ]), $post['variables']
            ));

            return redirect()->back()->with([
                'success' => 'Harga baru berhasil ditambahkan.',
            ]);
        }

        return redirect()->back()->with([
            'error' => 'Harga baru gagal ditambahkan.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function show(Price $price)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function edit(Price $price)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Price $price)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'cost_selling_per_unit' => 'required|integer',
            'cost_selling_per_box' => 'required|integer',
            'cost_selling_per_carton' => 'required|integer',
            'price_per_unit' => 'required|integer',
            'price_per_box' => 'required|integer',
            'price_per_carton' => 'required|integer',
        ]);

        if ($price->update($request->all())) {
            return redirect()->back()->with([
                'success' => 'Harga berhasil diperbarui.',
            ]);
        }

        return redirect()->back()->with([
            'error' => 'can\'t update price'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function destroy(Price $price)
    {
        if ($price->delete()) {
            return redirect()->back()->with([
                'success' => 'Harga berhasil di hapus.',
            ]);
        }

        return redirect()->back()->with([
            'error' => 'can\'t delete price'
        ]);
    }
}
