<?php

/** @noinspection PhpUnreachableStatementInspection */

namespace App\Http\Controllers\Api\v1\Admin;

use App\Doctype;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index(Request $request)
    {
        //
        if (!$request->ajax()) {
            return response()
                ->json([
                    'msg' => 'Petición no Valida',
                    'status' => 400
                ], 400);
        }

        $query = User::with('doctype', 'roles')->get();

        if (sizeof($query) > 0) {

            return response()
                ->json([
                    'msg' => 'Ok',
                    'status' => 200,
                    'data' => $query
                ], 200);
        } else {

            return response()
                ->json([
                    'msg' => 'No hay datos en respuesta a la solicitud',
                    'status' => 400,
                    'data' => []
                ], 400);
        }

        //return response()->json(User::with('doctype', 'roles')->get());
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        /*return response()
            ->json([
                'data' => $request->all(),
                'status' => 200
            ], 200);*/

        $Validator = Validator::make($request->all(), [
            'name' => 'required',
            'lastName' => 'required',
            'username' => 'required',
            'document' => 'required|numeric',
            'phone' => 'nullable|numeric',
            'email' => 'email|required|unique:users',
            'password' => 'required',
        ]);

        if ($Validator->fails()) {

            return response()->json([
                'msg' => 'Error',
                'errors' => $Validator->messages(),
                'status' => 400
            ]);
        } else {

            $user = new User();
            $user->name = strtoupper($request->name);
            $user->lastName = strtoupper($request->lastName);
            $user->username = strtoupper($request->username);
            $user->document = $request->document;
            $user->address = strtoupper($request->address);
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            if ($user->save()) {
                $doctype = Doctype::find($request->tDocument);
                $doctype->user()->save($user);

                return response()
                    ->json([
                        'msg' => 'Usuario Creado Correctamente',
                        'status' => 201
                    ], 201);
            } else {

                return response()->json([
                    'msg' => 'Hubo un error al guardar el Usuario',
                    'status' => 400
                ], 400);
            }
        }
    }


    public function update(Request $request)
    {
        //

        /*return response()
                ->json([
                    'msg' => 'Ok',
                    'data' => $request->all(),
                    'status' => '200'
                ]);*/

        $Validator = Validator::make($request->all(), [
            'name' => 'required',
            'lastName' => 'required',
            'username' => 'required',
            'document' => 'required|numeric',
            'phone' => 'nullable|numeric',
            'email' => 'required',
            'roles' => 'required'
        ]);

        if ($Validator->fails()) {

            return response()->json([
                'msg' => 'Error',
                'errors' => $Validator->messages(),
                'status' => 400
            ]);
        } else {

            $user = User::findOrFail($request->id);

            $user->name = strtoupper($request->name);
            $user->lastName = strtoupper($request->lastName);
            $user->username = strtoupper($request->username);
            $user->doctype_id = $request->tDocument;
            $user->document = $request->document;
            $user->email = $request->email;

            $doctype = Doctype::find($request->tDocument);
            $doctype->user()->save($user);

            if ($user->syncRoles($request->roles)) {

                return response()
                    ->json([
                        'msg' => 'Usuario Editado Correctamente',
                        'data' => $user,
                        'status' => 200
                    ], 200);
            } else {

                return response()
                    ->json([
                        'msg' => 'Hubo un error al Editar el Usuario',
                        'status' => 400
                    ], 400);
            }
        }
    }


    public function destroy(Request $request)
    {
        //
        $user = User::find($request->id);

        if ($user->delete()) {
            return response()->json(['msg' => 'Usuario Eliminado Correctamente'], 202);
        } else {
            return response()->json('Hubo un error al eliminar el usuario', 200);
        }
    }

    /*     public function publicHomeMethod(Request $request)
    {
        if (auth()->user()->hasRole(['super-admin', 'lya'])) {
            return redirect('/admin');
        } else if (auth()->user()->hasRole('subgerencia')) {
            return redirect('/rotaciones');
        } else {
            return redirect('/user');
        }
    } */

    public function AuthRouteAPI(Request $request)
    {
        return $request->user();
    }
}
