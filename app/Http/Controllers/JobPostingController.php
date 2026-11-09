<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    /**
     * GET /jobposting/create : formulaire de candidature (candidats uniquement).
     */
    public function create()
    {
        return view('jobposting.create');
    }
}
