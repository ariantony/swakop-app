<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $create = fn () => Carbon::createFromDate($request->year, $request->month + 1);

        return [
            'month' => $this->query(
                $create()->startOfMonth(),
                $create()->endOfMonth(),
            )->get()->map(fn ($detail) => $detail->total_cost_all)->sum(),

            'today' => $this->query(
                now()->startOfDay(),
                now()->endOfDay()
            )->get()->map(fn ($detail) => $detail->total_cost_all)->sum(),

            'customer' => Transaction::whereBetween('created_at', [
                $create()->startOfMonth(),
                $create()->endOfMonth(),
            ])->whereRelation('details', 'type', 'sell')->count(),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query(Carbon $start, Carbon $end)
    {
        return Detail::whereBetween('created_at', [$start, $end])
                        ->where('type', 'sell');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function selling(Request $request)
    {
        $from = Carbon::createFromDate($request->year, $request->month + 1)->startOfMonth();
        $to = Carbon::createFromDate($request->year, $request->month + 1)->endOfMonth();

        return $this->query($from, $to)
                    ->get()
                    ->each(function (Detail $detail) {
                        $detail->date = $detail->created_at->format('d');
                    })
                    ->groupBy('date')
                    ->map(fn ($details) => $details->map(fn ($detail) => $detail->total_cost_all)->sum());
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function profit(Request $request)
    {
        $months = [];

        for ($i = 1; $i < 13; $i++) {
            $months[] = [
                Carbon::createFromDate($request->year, $i)->startOfMonth()->format('Y-m-d H:i:s'),
                Carbon::createFromDate($request->year, $i)->endOfMonth()->format('Y-m-d H:i:s'),
            ];
        }

        return collect($months)->map(function ($month) {
            $results = Detail::whereBetween('created_at', $month)->get();

            return [
                'buy' => $results->where('type', 'buy')->sum('total_cost_all'),
                'sell' => $results->where('type', 'sell')->sum('total_cost_all'),
            ];

            return [
                'buy' => Detail::where('type', 'buy')->whereBetween('created_at', $month)->get(['cost_unit', 'qty_unit'])->map(function (Detail $detail) {
                    return $detail->total_cost_all;
                })->sum(),

                'sell' => Detail::where('type', 'sell')->whereBetween('created_at', $month)->get(['cost_unit', 'qty_unit'])->map(function (Detail $detail) {
                    return $detail->total_cost_all;
                })->sum()
            ];
        });
    }
}
