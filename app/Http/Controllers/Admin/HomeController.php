<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|lya']);
    }

    public function index()
    {
        return view('admin.home');
    }

    public function userIndex()
    {
        return view('admin.users.user');
    }

    public function rolesIndex()
    {
        return view('admin.roles.role');
    }

    public function permissionsIndex()
    {
        return view('admin.permissions.permission');
    }

    public function suministrosIndex()
    {
        return view('admin.suministros.suministro');
    }

    public function tercerosIndex()
    {
        return view('admin.terceros.tercero');
    }
}
