<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $ownedBoards = $user->boards()->with('user')->get();
        $memberBoards = $user->memberBoards()->with('user')->get();

        return view('dashboard', compact('ownedBoards', 'memberBoards'));
    }
}