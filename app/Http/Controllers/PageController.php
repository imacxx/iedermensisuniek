<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PageController extends Controller
{
    /**
     * Display the home page.
     */
    public function home()
    {
        $page = Page::where('slug', 'home')
            ->published()
            ->firstOrFail();

        return view('page', compact('page'));
    }

    /**
     * Display the specified page.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->published()
            ->firstOrFail();

        return view('page', compact('page'));
    }
}
