<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // contoh data dummy; silakan ganti dari DB-mu
        $stats = [
            'user_name'          => $request->user()->name ?? 'Fafian Ahnaf',
            'total_complete'     => 0,
            'total_incomplete'   => 3,
            'total_overdue'      => 0,
            'total_project'      => 3,
            'incomplete_breakdown' => [
                'todo'  => 3,
                'doing' => 0,
                'done'  => 0,
            ],
            'task_completion' => [
                'complete'   => 0,
                'incomplete' => 3,
            ],
        ];

        return view('Auth.dashboard', compact('stats'));
    }
}
