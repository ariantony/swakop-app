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
        $columns = array_filter($model->getFillable(), fn ($column) => ! in_array($column, $model->getHidden()));

        $request->validate([
            'search' => 'nullable|string',
            'order.key' => 'nullable|string|in:' . join(',', $columns),
            'order.dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ]);

        return User::where(function (Builder $query) use (&$request, &$model, &$columns) {
                            $search = '%'. $request->input('search') .'%';

                            foreach ($columns as $column) {
                                $query->orWhere($column, 'like', $search);
                            }
                        })
                        ->with(['roles'])
                        ->orderBy($request->input('order.key', 'name'), $request->input('order.dir', 'asc') ?: 'asc')
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
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string',
            'password_confirmation' => 'required|same:password',
        ]);

        $post = $request->only([
            'name', 'username', 'email', 'password'
        ]);
        
        foreach ($post as $key => $val) {
            if ($key !== 'password') {
                $post[$key] = mb_strtolower($val);
            } else {
                $post[$key] = Hash::make($val);
            }
        }

        if ($user = User::create($post)) {
            return redirect()->back()->with('success', __(
                'user has been created',
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t create user',
        ));
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
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'required|string',
            'password_confirmation' => 'required|same:password',
        ]);
        
        foreach ($post as $key => $val) {
            if ($key !== 'password') {
                $post[$key] = mb_strtolower($val);
            }
        }

        if ($user->update($post)) {
            return redirect()->back()->with('success', __(
                'user has been updated',
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t update user',
        ));
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
            return redirect()->back()->with('success', __(
                'user has been deleted',
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t delete user',
        ));
    }
}
