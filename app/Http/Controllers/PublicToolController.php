<?php

namespace App\Http\Controllers;

use App\Enums\ToolVisibility;
use App\Models\Tool;
use Illuminate\Contracts\View\View;

class PublicToolController extends Controller
{
    public function show(string $locale, Tool $tool): View
    {
        abort_unless($tool->visibility === ToolVisibility::Public, 404);

        return view('pages::public.tools.show', ['tool' => $tool]);
    }
}
