<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Group/Index');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request)
    {
        $model = new Group();
        $columns = array_filter($model->getFillable(), fn ($column) => !in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'order.key' => 'nullable|string|in:' . join(',', $columns),
            'order.dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ]);

        return Group::where(function (Builder $query) use (&$request, &$model, &$columns) {
            $search = '%' . $request->input('search') . '%';

            foreach ($columns as $column) {
                $query->orWhere($column, 'like', $search);
            }
        })
        ->orderBy($request->input('order.key', 'code') ?: 'code', $request->input('order.dir', 'asc') ?: 'asc')
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
            'code' => 'required|string|unique:groups,code',
            'name' => 'required|string',
        ]);

        $post['code'] = mb_strtoupper($post['code']);
        $post['name'] = mb_strtolower($post['name']);

        if ($group = Group::create($post)) {
            return redirect()->back()->with('success', 'Kelompok barang berhasil ditambahkan.');
        }

        return redirect()->back()->with('error', 'Kelompok barang gagal ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        $post = $request->validate([
            'code' => 'required|string|unique:groups,code,' . $group->id,
            'name' => 'required|string',
        ]);

        $post['code'] = mb_strtoupper($post['code']);
        $post['name'] = mb_strtolower($post['name']);

        if ($group->update($post)) {
            return redirect()->back()->with('success', 'Kelompok barang berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Kelompok barang gagal diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        if ($group->delete()) {
            return redirect()->back()->with('success', 'Kelompok barang berhasil dihapus.');
        }

        return redirect()->back()->with('success', 'Kelompok barang gagal dihapus.');
    }
}
