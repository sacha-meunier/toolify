<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Contracts\View\View;

class DiscoverController extends Controller
{
    public function index(): View
    {
        return view('pages::public.discover', [
            'tools' => Tool::query()->visibleTo(null)->orderBy('updated_at', 'desc')->paginate(12),
        ]);
    }
}
