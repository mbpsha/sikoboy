<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\NotificationFeed;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/NotifAdmin', [
            'notifications' => NotificationFeed::forAdmin(250)->values()->all(),
        ]);
    }
}
