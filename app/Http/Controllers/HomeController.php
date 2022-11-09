<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function RedirectToSomeWhere()
    {
        if (auth()->user()->hasRole(['super-admin', 'lya'])) {
            return redirect('/admin');
        } else if (auth()->user()->hasRole('subgerencia')) {
            return redirect('/rotaciones');
        } else {
            return redirect('/user');
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function perfilUsuario($userid)
    {
        /*$perfil = DB::table('users')
            ->where('id', '=', $userid)->get();*/

        $perfil = User::with('doctype')->where('id', '=', $userid)->get();

        if (sizeof($perfil) > 0) {
            return view('shared.perfil.perfil', compact('perfil'));
        } else {
            return view('errors.403');
        }
    }

    public function rotacionesIndex()
    {
        return view('shared.rotaciones.rotacion');
    }

    public function farmaciaRecepcion()
    {
        if (auth()->check()) {
            return view('shared.Farmacia.Farmacia');
        }
    }
}
