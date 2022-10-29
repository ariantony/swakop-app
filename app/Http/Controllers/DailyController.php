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
        
        $date = new Carbon($request->date);
        $date = $date->addHour(7)->format('Y-m-d');

        $sell = Transaction::with('details.product')->whereRelation('details', 'type', 'sell')->where('user_id', $request->user_id)->whereDate('created_at', $date)->get();
        
        $detail = $sell->map(fn ($item) => $item->details)->flatten()->groupBy('product_id')->map(function ($detail) {
            $product = $detail->first()->product;
            $variables = [];
            
            foreach ($detail as $d) {
                $variables[] = $d->getVariableData();
                $subtotal[] = $d->getVariablePrice();
            }

            $variables = collect($variables)->reduce(fn ($a, $b) => [...$a, ...$b], []);
            // by price id
            $variables = collect($variables)->groupBy('id')->map(function ($item) {
                return $item;
            });
            // by perqty
            $variables = $variables->map(function ($item) {
                return $item->groupBy('min_qty')->map(function ($item) {
                    return [
                        'perqty' => $item->first()['perqty'],
                        'perprice' => $item->first()['perprice'],
                        'min_qty' => $item->first()['min_qty'],
                        'qty' => $item->sum('qty'),
                        'subtotal' => $item->sum('subtotal'),
                    ];
                })->sortBy('min_qty')->values()->toArray();
            });
            // merge properties
            $variables = array_merge($variables->toArray(), [
                'name' => $product->name,
                'qty_total' => $detail->sum('qty_unit'),
                'total' => array_sum($subtotal),
            ]);
            return $variables;
            /*
            return array_merge(collect($variables)->groupBy('id')->map(function ($arr) {
                $arr->groupBy('perqty')->map(function ($item) {
                    return [
                        'perqty' => $item->first()['perqty'],
                        'perprice' => $item->first()['perprice'],
                        'qty' => $item->sum('qty'),
                        'subtotal' => $item->sum('subtotal'),
                    ];
                });
                return $arr->toArray();
            })->sortBy('perqty')->values()->toArray(), [
                'name' => $product->name,
                'qty_total' => $detail->sum('qty_unit'),
                'total' => array_sum($subtotal),
            ]);
            */
        });

        $send = [
            'sell' => $detail->sortBy('name')->values(),
            'total' => $sell->sum('total_cost'),
            'cashier' => User::find($request->user_id),
            'day' => $date,
        ];

        if ($request->method() === 'GET') {
            return Inertia::render('Report/Daily/IframeGenerate', $send);
        }
        return Inertia::render('Report/Daily/Generate', $send);
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
