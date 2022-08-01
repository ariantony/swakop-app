<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Report/Presence/Index')->with([
            'users' => User::get()
        ]);
    }

    /**
     * Generate presence report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'daterange' => 'required',
        ]);

        $from = new Carbon($request->daterange[0]);
        $to = new Carbon($request->daterange[1]);
        $from->addDays(1);
        $to->addDays(1);
        $user = User::find($request->user_id);

        $presences = $user->presences()
                    ->when($from == $to, function ($query) use ($from) {
                        return $query->whereDate('created_at', $from);
                    })
                    ->when($from != $to, function ($query) use ($from, $to) {
                        return $query->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
                    })
                    ->oldest()
                    ->get()->each(fn ($p) => $p->date = $p->created_at->format('Y-m-d'))->groupBy('date');

        $presences = $presences->map(fn ($p) => [
            'in_time' => @$p[0]?->time,
            'out_time' => @$p[1]?->time,
        ]);

        return Inertia::render('Report/Presence/Generate', [
            'data' => $presences,
            'cashier' => $user,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'length' => count($presences),
        ]);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
