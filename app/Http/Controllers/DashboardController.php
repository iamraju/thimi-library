<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $role = $request->user()->role;

        return Inertia::render('Dashboard', [
            'role' => $role,
            'stats' => [
                'books' => Book::count(),
                'active_books' => Book::where('status', 1)->count(),
                'categories' => Category::count(),
                'publishers' => Publisher::count(),
                'users' => $role === 'superadmin' ? User::count() : null,
            ],
            'recentBooks' => Book::with(['category:id,title', 'publisher:id,title'])
                ->latest()->limit(5)->get(['id', 'title', 'category_id', 'publisher_id', 'status', 'created_at']),
        ]);
    }
}
