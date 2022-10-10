<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Burden;
use App\Models\Transaction;
use Illuminate\Http\Request;

class IncomeStatementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Report/IncomeStatement/Index');
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
            'period' => 'required',
        ]);

        $month = str_pad($request->period['month'] + 1, 2, '0', STR_PAD_LEFT);
        $year = $request->period['year'];
        $period = $year . $month;
        
        $totalSell = Transaction::with('details')->whereRelation('details', 'type', 'sell')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get()->sum('total_cost');
        $totalBuy = Transaction::with('details')->whereRelation('details', 'type', 'buy')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get()->sum('total_cost');
        $totalReturn = Transaction::with('details')->whereRelation('details', 'type', 'return buy')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get()->sum('total_cost');

        $hpp = [
            'totalBuy' => $totalBuy,
            'totalReturn' => $totalReturn,
            'discount' => 0,
            'shippingCost' => 0,
            'total' => $totalBuy - $totalReturn,
        ];

        $grossProfit = $totalSell - $hpp['total'];

        // $salaries = User::with('roles')->whereRelation('roles', 'name', 'kasir')->sum('basic_salary');

        $getBurden = Burden::where('period', $year . $month)->get();
        $burden = [
            'list' => $getBurden,
            'total' => $getBurden->sum('cost'),
        ];

        $netProfit = $grossProfit - $burden['total'];

        return Inertia::render('Report/IncomeStatement/Generate', [
            'totalSell' => $totalSell,
            'hpp' => $hpp,
            'grossProfit' => $grossProfit,
            // 'salaries' => $salaries,
            'burden' => $burden,
            'netProfit' => $netProfit,
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
