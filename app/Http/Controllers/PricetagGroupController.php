<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\PricetagGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class PricetagGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('PricetagGroup/Index');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request)
    {
        $model = new PricetagGroup();
        $columns = array_filter($model->getFillable(), fn ($column) => !in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'order.key' => 'nullable|string|in:' . join(',', $columns),
            'order.dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ]);

        return PricetagGroup::where(function (Builder $query) use (&$request,  &$columns) {
                $search = '%' . $request->input('search') . '%';

                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', $search);
                }
            })
            ->orderBy($request->input('order.key', 'name') ?: 'name', $request->input('order.dir', 'asc') ?: 'asc')
            ->paginate($request->input('per_page', 10));
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
            'name' => 'required|string|max:255',
            'products' => 'required|array',
        ]);

        $post['name'] = mb_strtolower($post['name']);
        
        DB::beginTransaction();
        try {
            $pricetagGroup = PricetagGroup::create($post);
            $pricetagGroup->products()->sync($post['products']);
            DB::commit();
            return redirect()->back()->with('success', 'Pricetag group berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
        return redirect()->back()->with('error', 'Pricetag group gagal ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PricetagGroup  $pricetagGroup
     * @return \Illuminate\Http\Response
     */
    public function show(PricetagGroup $pricetagGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PricetagGroup  $pricetagGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(PricetagGroup $pricetagGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PricetagGroup  $pricetagGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PricetagGroup $pricetagGroup)
    {
        $post = $request->validate([
            'name' => 'required|string|max:255',
            'products' => 'required|array',
        ]);
        
        $post['name'] = mb_strtolower($post['name']);
        
        DB::beginTransaction();
        try {
            $pricetagGroup->update($post);
            $pricetagGroup->products()->sync($post['products']);
            DB::commit();
            return redirect()->back()->with('success', 'Pricetag group berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
        return redirect()->back()->with('error', 'Pricetag group gagal diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PricetagGroup  $pricetagGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(PricetagGroup $pricetagGroup)
    {
        DB::beginTransaction();
        try {
            $pricetagGroup->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Pricetag group berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
        return redirect()->back()->with('error', 'Pricetag group gagal dihapus.');
    }

    /**
     * Remove one product from group.
     *
     * @param  \App\Models\PricetagGroup  $pricetagGroup
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function remove(PricetagGroup $pricetagGroup, Product $product)
    {
        DB::beginTransaction();
        try {
            $pricetagGroup->products()->detach($product->id);
            DB::commit();
            return redirect()->back()->with('success', sprintf('Produk %s berhasil dihapus dari grup %s.', mb_strtoupper($product->name), mb_strtoupper($pricetagGroup->name)));
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
        return redirect()->back()->with('error', sprintf('Produk %s gagal dihapus dari grup %s.', mb_strtoupper($product->name), mb_strtoupper($pricetagGroup->name)));
    }
}
