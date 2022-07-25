<?php

namespace App\Http\Controllers;

use App\Models\Burden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BurdenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Burden/Index');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request)
    {
        $model = new Burden();
        $columns = array_filter($model->getFillable(), fn ($column) => !in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'order.key' => 'nullable|string|in:' . join(',', $columns),
            'order.dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ]);

        return Burden::where(function (Builder $query) use (&$request, &$model, &$columns) {
            $search = '%' . $request->input('search') . '%';

            foreach ($columns as $column) {
                $query->orWhere($column, 'like', $search);
            }
        })
            ->orderBy($request->input('order.key', 'period') ?: 'period', $request->input('order.dir', 'desc') ?: 'desc')
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
            'cost' => 'required|numeric',
            'period' => 'required',
        ]);
        
        $post['period']['month'] += 1;
        $post['period'] = $post['period']['year'] . str_pad($post['period']['month'], 2, '0', STR_PAD_LEFT);

        if ($burden = Burden::create($post)) {
            return redirect()->back()->with('success', 'Beban pengeluaran berhasil ditambahkan.');
        }

        return redirect()->back()->with('error', 'Beban pengeluaran gagal ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Burden  $burden
     * @return \Illuminate\Http\Response
     */
    public function show(Burden $burden)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Burden  $burden
     * @return \Illuminate\Http\Response
     */
    public function edit(Burden $burden)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Burden  $burden
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Burden $burden)
    {
        $post = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cost' => 'required|numeric',
            'period' => 'required',
        ]);

        $post['period']['month'] += 1;
        $post['period'] = $post['period']['year'] . str_pad($post['period']['month'], 2, '0', STR_PAD_LEFT);

        if ($burden->update($post)) {
            return redirect()->back()->with('success', 'Beban pengeluaran berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Beban pengeluaran gagal diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Burden  $burden
     * @return \Illuminate\Http\Response
     */
    public function destroy(Burden $burden)
    {
        if ($burden->delete()) {
            return redirect()->back()->with('success', 'Beban pengeluaran berhasil dihapus.');
        }

        return redirect()->back()->with('success', 'Beban pengeluaran gagal dihapus.');
    }
}
