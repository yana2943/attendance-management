<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminStaffController extends Controller
{
    public function list()
    {
        $staffs = User::all();
        return view('admin.staff.list', compact('staffs'));
    }
}