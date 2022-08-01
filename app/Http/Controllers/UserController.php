<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('User/Index');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request)
    {
        $model = new User();
        $columns = array_filter($model->getFillable(), fn ($column) => !in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'order.key' => 'nullable|string|in:' . join(',', $columns),
            'order.dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ]);

        return User::where(function (Builder $query) use (&$request, &$model, &$columns) {
            $search = '%' . $request->input('search') . '%';

            foreach ($columns as $column) {
                $query->orWhere($column, 'like', $search);
            }
        })
            ->where('id', '!=', 1)
            ->with(['roles'])
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
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string',
            'password_confirmation' => 'required|same:password',
            'basic_salary' => 'required|numeric',
        ]);

        $post = $request->only([
            'name', 'username', 'password', 'basic_salary',
        ]);

        foreach ($post as $key => $val) {
            if ($key !== 'password') {
                $post[$key] = mb_strtolower($val);
            } else {
                $post[$key] = Hash::make($val);
            }
        }

        $post['email'] = $post['username'] . '@swakop.app';
        if ($user = User::create($post)) {
            $user->assignRole('kasir');
            return redirect()->back()->with('success', 'User berhasil ditambahkan.');
        }

        return redirect()->back()->with('error', 'User gagal ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $post = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'required|string',
            'password_confirmation' => 'required|same:password',
            'basic_salary' => 'required|numeric',
        ]);

        foreach ($post as $key => $val) {
            if ($key !== 'password') {
                $post[$key] = mb_strtolower($val);
            }
        }

        if ($user->update($post)) {
            return redirect()->back()->with('success', 'User berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'User gagal diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->delete()) {
            return redirect()->back()->with('success', 'User berhasil dihapus.');
        }

        return redirect()->back()->with('success', 'User gagal dihapus.');
    }
}
