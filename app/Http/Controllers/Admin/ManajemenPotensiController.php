<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ManajemenPotensiController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ManajemenPotensi/Index');
    }
}
