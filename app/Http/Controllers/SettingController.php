<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Setting/Show');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }

    /**
     * Update the master password.
     * 
     * @param  \Illuminate\Http\Request  $request
     */
    public function masterPasswordUpdate(Request $request)
    {
        $setting = Setting::where('key', 'master_password')->first();

        Validator::make($request->all(), [
            'password_before' => 'required|string|min:6',
            'password_new' => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6|same:password_new',
        ])->after(function ($validator) use ($request, $setting) {
            if (! isset($request->password_before) || ! Hash::check($request->password_before, $setting?->value['password'])) {
                $validator->errors()->add('password_before', 'Password sebelumnya yang anda masukan salah.');
            }
            if ($request->password_before == $request->password_new) {
                $validator->errors()->add('password_new', 'Password baru yang anda masukan tidak boleh sama dengan password sebelumnya.');
            }
        })->validate();
        
        $setting->value = ['password' => Hash::make($request->password_new)];
        if ($setting->save()){
            return redirect()->back()->with('success', 'Master password berhasil diganti.');
        }
        return redirect()->back()->with('error', 'Terjadi kesalahan, coba lagi nanti.');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function compare(Request $request)
    {
        $code = Setting::where('key', 'master_password')->first()->value;

        if (Hash::check($request->code, $code['password'])) {
            return [
                'message' => 'ok',
            ];
        }

        return abort(401);
    }
}
