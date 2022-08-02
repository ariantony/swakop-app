<?php

namespace App\Http\Controllers;

use App\Models\Conversion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Conversion/Index');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request)
    {
        $model = new Conversion();
        $columns = array_filter($model->getFillable(), fn ($column) => !in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'order.key' => 'nullable|string',
            'order.dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ]);

        return Conversion::with(['from', 'to'])
                            ->where(function (Builder $query) use (&$request, &$model, &$columns) {
                                $search = '%' . $request->input('search') . '%';

                                foreach ($columns as $column) {
                                    $query->orWhere($column, 'like', $search);
                                }
                                $query->orWhereRelation('from', 'name', 'like', $search);
                                $query->orWhereRelation('to', 'name', 'like', $search);
                            })
                            ->orderBy($request->input('order.key', 'created_at') ?: 'created_at', $request->input('order.dir', 'desc') ?: 'desc')
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
            'from_id' => 'required|integer|exists:products,id',
            'to_id' => 'required|integer|exists:products,id',
            'large' => 'required|numeric',
            'small_per_large' => 'required|numeric',
        ]);

        if ($conversion = Conversion::create($post)) {
            return redirect()->back()->with('success', 'Konversi stok berhasil dilakukan.');
        }

        return redirect()->back()->with('error', 'Konversi stok gagal dilakukan.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Conversion  $conversion
     * @return \Illuminate\Http\Response
     */
    public function edit(Conversion $conversion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conversion  $conversion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Conversion $conversion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Conversion  $conversion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conversion $conversion)
    {
        //
    }
}
