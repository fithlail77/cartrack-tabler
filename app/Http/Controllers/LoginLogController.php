<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logs = LoginLog::with('user')->latest('login_at')->paginate(20);

        return view('settings.login-log', compact('logs'));
    }
}