<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Filter rentang tanggal berdasarkan start_date
        $start = $request->filled('start') ? Carbon::parse($request->query('start'))->startOfDay() : null;
        $end   = $request->filled('end')   ? Carbon::parse($request->query('end'))->endOfDay()   : null;

        $query = Project::mine();
        if ($start) $query->whereDate('start_date', '>=', $start->toDateString());
        if ($end)   $query->whereDate('start_date', '<=', $end->toDateString());

        $projects = $query->orderBy('start_date')->get();

        // Stats
        $statusCounts = [
            'todo'        => $projects->where('status','todo')->count(),
            'in_progress' => $projects->where('status','in_progress')->count(),
            'review'      => $projects->where('status','review')->count(),
            'done'        => $projects->where('status','done')->count(),
        ];
        $avgProgress = (int) round($projects->avg('progress') ?? 0);
        $total       = $projects->count();

        // Monthly series (maks 12 bulan agar ringkas)
        $firstStart = $projects->min('start_date');
        $lastDate   = $projects->max('end_date') ?? $projects->max('start_date');

        if (!$firstStart) {
            $from = ($start ?: now()->copy()->subMonths(5))->startOfMonth();
            $to   = ($end   ?: now())->endOfMonth();
        } else {
            $from = ($start ?: Carbon::parse($firstStart))->copy()->startOfMonth();
            $to   = ($end   ?: Carbon::parse($lastDate ?: $firstStart))->copy()->endOfMonth();
        }

        // Batasi 12 bulan
        if ($from->diffInMonths($to) > 11) {
            $from = $to->copy()->subMonths(11)->startOfMonth();
        }

        $period = CarbonPeriod::create($from, '1 month', $to);
        $monthlyLabels = [];
        $monthlyCounts = [];
        foreach ($period as $m) {
            $key = $m->format('Y-m');
            $monthlyLabels[] = $m->translatedFormat('M Y');
            $monthlyCounts[] = $projects->filter(fn($p) => optional($p->start_date)?->format('Y-m') === $key)->count();
        }

        // Export CSV (opsional)
        if ($request->query('export') === 'csv') {
            $filename = 'projects-report-'.now()->format('Ymd_His').'.csv';
            return response()->streamDownload(function () use ($projects) {
                $out = fopen('php://output','w');
                fputcsv($out, ['Nama Proyek','PIC','Status','Mulai','Selesai','Progress','Dokumen','Kegiatan']);
                foreach ($projects as $p) {
                    fputcsv($out, [
                        $p->title,
                        $p->pic,
                        $p->status,
                        optional($p->start_date)?->format('Y-m-d'),
                        optional($p->end_date)?->format('Y-m-d'),
                        (int)($p->progress ?? 0),
                        $p->outcome,
                        $p->activity,
                    ]);
                }
                fclose($out);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        return view('Feature.report', [
            'projects'       => $projects,
            'total'          => $total,
            'statusCounts'   => $statusCounts,
            'avgProgress'    => $avgProgress,
            'monthlyLabels'  => $monthlyLabels,
            'monthlyCounts'  => $monthlyCounts,
            'start'          => $start?->toDateString(),
            'end'            => $end?->toDateString(),
        ]);
    }
}
