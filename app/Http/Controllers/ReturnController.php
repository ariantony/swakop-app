<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Report/Return/Index');
    }

    /**
     * Generate goods return report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        $request->validate([
            'period' => 'required',
        ]);

        $month = str_pad($request->period['month'] + 1, 2, '0', STR_PAD_LEFT);
        $year = $request->period['year'];
        $period = $year . $month;
        
        $return = Transaction::with('details.product')->whereRelation('details', 'type', 'return buy')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
        
        $detail = $return->map(fn ($item) => $item->details)->flatten()->groupBy('product_id')->map(function ($detail) {
            return [
                'name' => $detail->first()->product->name,
                'qty_unit' => $detail->sum('qty_unit'),
                'cost_unit' => $detail->first()->cost_unit,
                'total_cost_all' => $detail->sum('total_cost_all'),
            ];
        });
        
        return Inertia::render('Report/Return/Generate', [
            'retur' => $detail,
            'total' => $return->sum('total_cost'),
            'period' => $period,

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
