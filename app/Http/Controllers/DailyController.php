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
            $product = $detail->first()->product;
            $variables = [];
            
            foreach ($detail as $d) {
                $variables[] = $d->getVariableData();
                $subtotal[] = $d->getVariablePrice();
            }
            $variables = collect($variables)->reduce(fn ($a, $b) => [...$a, ...$b], []);
            return array_merge(collect($variables)->groupBy('perqty')->map(function ($arr) {
                return [
                    'perqty' => $arr->first()['perqty'],
                    'perprice' => $arr->first()['perprice'],
                    'qty' => $arr->sum('qty'),
                    'subtotal' => $arr->sum('subtotal'),
                ];
            })->sortBy('perqty')->values()->toArray(), [
                'name' => $product->name,
                'qty_total' => $detail->sum('qty_unit'),
            ]);
        });
        dd($detail->sortBy('name')->values());
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
