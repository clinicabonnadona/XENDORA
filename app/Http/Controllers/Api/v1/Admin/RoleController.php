<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index()
    {
        //
        $roles = Role::with('permissions')
            ->orderBy('created_at', 'desc')
            ->get();

        if (sizeof($roles) < 0)
        {
            return response()->json([
                'msg' => 'No hay datos para la respuesa solicitada',
                'data' => [],
                'status' => 400
            ], 400);
        }

        return response()->json([
            'msg' => 'ok',
            'data' => $roles,
            'status' => 200
        ], 200);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
        if (!$request->ajax()) {

            return response()->json([
                'msg' => 'Petición no Valida',
                'status' => 400
            ], 400);

        }

        $Validator = Validator::make($request->all(), [
           'name' => 'required|unique:roles',
        ]);

        if ($Validator->fails())
        {

            return response()->json([
                'msg' => 'Error',
                'errors' => $Validator->messages(),
                'status' => 400
            ]);

        }

        $role = Role::create([
            'name' => $request->name
        ]);

        if ($role)
        {

            return response()
                ->json([
                    'msg' => 'Rol creado correctamente',
                    'status' => 201
                ], 201);

        }

            //$role->syncPermissions($request->permissions);
    }


    public function show($id)
    {
        //
    }


    public function edit(Request $request)
    {
        //
        if (!$request->ajax())
        {

            return response()->json([
                'msg' => 'Petición no Valida',
                'status' => 400
            ], 400);

        }

        $role = Role::findByName($request->name);

        if ($role)
        {

            $role->syncPermissions($request->permissions);

            return response()->json([
                'msg' => 'Rol Actualizado Correctamente',
                'data' => $role,
                'status' => 200
            ], 200);

        } else {

            return response()->json([
                'msg' => 'Error al procesar la solicitud',
                'status' => 400
            ], 400);

        }

    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy(Request $request)
    {
        //
        if (!$request->ajax())
        {

            return response()->json([
                'msg' => 'Petición no Valida',
                'status' => 400
            ], 400);

        }

        $role = Role::findByName($request->name);

        if ($role->delete()) {

            return response()
                ->json([
                    'msg' => 'Rol Eliminado Correctamente',
                    'status' => 200
            ], 200);

        } else {

            return response()
                ->json([
                    'msg' => 'Hubo un Error al procesar la solicitud',
                    'status' => 400
            ], 400);

        }
    }

    public function revokePermissionToRole(Request $request)
    {
        $queryRole = Role::findById($request->role);

        if ($queryRole)
        {
            $queryPermission = Permission::findById($request->permission);

            if ($queryPermission)
            {
                //$queryRole->revokePermissionTo($queryPermission->name);
                if ($queryRole->revokePermissionTo($queryPermission->name))
                {
                    return response()->json([
                        'Permiso eliminado'
                    ], 200);
                } else {
                    return response()->json([
                        'Error, No se Pudo Eliminar el Permiso'
                    ], 500);
                }

            } else {
                return response()->json([
                    'Error, No se encontró el Permiso'
                ], 500);
            }

        } else {
            return response()->json([
                'Error, No se encontró el Rol'
            ], 500);
        }
    }
}
