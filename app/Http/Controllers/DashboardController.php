<?php

namespace App\Http\Controllers;

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
}
