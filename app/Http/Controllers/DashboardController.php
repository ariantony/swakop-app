<?php

namespace App\Http\Controllers;

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
            )->sum('total_cost'),

            'today' => $this->query(now()->startOfDay(), now())->sum('total_cost'),

            'customer' => $this->query(
                $create()->startOfMonth(),
                $create()->endOfMonth(),
            )->count(),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query(Carbon $start, Carbon $end)
    {
        return Transaction::where(function (Builder $query) {
                                $query->whereRelation('details', 'type', 'sell')
                                        ->orDoesntHave('details');
                            })
                            ->whereBetween('created_at', [$start, $end]);
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
                    ->without(['details', 'user'])
                    ->get(['created_at', 'total_cost'])
                    ->each(function (Transaction $transaction) {
                        $transaction->date = $transaction->created_at->format('d');
                    })
                    ->groupBy('date')
                    ->map(fn ($transaction) => $transaction->sum('total_cost'));
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function profit()
    {
        $months = [];

        for ($i = 1; $i < 13; $i++) {
            $months[] = [
                Carbon::createFromDate(month: $i)->startOfMonth()->format('Y-m-d H:i:s'),
                Carbon::createFromDate(month: $i)->endOfMonth()->format('Y-m-d H:i:s'),
            ];
        }

        return collect($months)->map(function ($month) {
            return [
                'buy' => Product::with(['buy'])->whereRelation('buy', function (Builder $query) use ($month) {
                    $query->whereBetween('created_at', $month);
                })->get()->map(fn ($p) => $p->buy->sum('total_cost_all'))->sum(),

                'sell' => Product::with(['sell'])->whereRelation('sell', function (Builder $query) use ($month) {
                    $query->whereBetween('created_at', $month);
                })->get()->map(fn ($p) => $p->sell->sum('total_cost_all'))->sum()
            ];
        });
    }
}
