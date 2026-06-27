<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JamaahController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('dashboard.jamaah', [
            'jamaah' => $request->user(),
        ]);
    }
}
