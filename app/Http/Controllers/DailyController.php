<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Detail;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DailyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Report/Daily/Index', [
            'users' => User::all(),
        ]);
    }

    /**
     * Generate income statement report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'date' => 'required',
        ]);
        
        $date = explode('T', $request->date)[0];
        $date = new Carbon($request->date);

        $sell = Transaction::with('details.product')->whereRelation('details', 'type', 'sell')->where('user_id', $request->user_id)->whereDate('created_at', $date)->get();
        
        $detail = $sell->map(fn ($item) => $item->details)->flatten()->groupBy('product_id')->map(function ($detail) {
            $variables = [];
            
            foreach ($detail as $d) {
                // dd($d->getVariableCost());
                // $qty = $d->qty_unit;
                // $subtotal = 0;
                // while ($qty > 0) {
                //     $cost = $d->product?->price?->variableCosts?->firstWhere('qty', '<=', $qty);
                //     if (!is_null($cost)) {
                //         $q = floor($qty / $cost->qty);
                //         $variables[] = [
                //             'qty' => $cost->qty,
                //             'price' => $cost->price,
                //         ];
                //         $qty -= ($q * $cost->qty);
                //         $subtotal += ($q * $cost->price);
                //     } else {
                //         $left = $qty;
                //         $variables[] = [
                //             'qty' => $qty,
                //             'price' => $d->product?->price?->price_per_unit,
                //         ];
                //         $qty -= $left;
                //         $subtotal += ($left * $d->product?->price?->price_per_unit);
                //     }
                // }
                $variables[] = $d->getVariableData();
                $subtotal[] = $d->getVariablePrice();
            }
            dd($variables, $subtotal);
            return [
                'name' => $detail->first()->product->name,
                'qty_unit' => $detail->sum('qty_unit'),
                'cost_unit' => $detail->first()->cost_unit,
                'total_cost_all' => array_sum($subtotal),
            ];
        });
        
        return Inertia::render('Report/Daily/Generate', [
            'sell' => $detail->sortBy('name')->values(),
            'total' => $sell->sum('total_cost'),
            'cashier' => User::find($request->user_id),
            'day' => $date,

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
