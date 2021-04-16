<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Proposal;
use App\Models\Uptime;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Uptime $uptime)
    {
        $speedChartData = Uptime::getChartData();

        return view('home', compact('speedChartData'));
    }
}
