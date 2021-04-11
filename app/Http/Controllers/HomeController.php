<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Node;
use App\Models\Uptime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        $blockChartData = Block::getChartData();

        return view('home', compact('speedChartData', 'blockChartData'));
    }
}
