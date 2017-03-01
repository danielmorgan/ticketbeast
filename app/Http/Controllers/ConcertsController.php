<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;

class ConcertsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $concert = Concert::published()->findOrFail($id);

        return view('concerts.show', compact('concert'));
    }
}
