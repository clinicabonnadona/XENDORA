<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        $permissions = Permission::orderBy('created_at', 'desc')->get();

        if (sizeof($permissions) < 0)
        {
            return response()
                ->json([
                    'msg' => 'No hay datos para la respuesta a la solicitud',
                    'status' => 400
                ]);
        }

        return response()->json($permissions);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'msg' => 'Petición no Valida',
                'status' => 400
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error',
                'errors' => $validator->messages(),
                'status' => 400
            ]);
        }

        $permission = Permission::create([
            'name' => $request->name,
        ]);

        if ($permission)
        {

            return response()->json([
                'msg' => 'Permiso Creado Correctamente',
                'status' => 201
            ], 201);

        }



    }


    public function update(Request $request)
    {
        //
        $permission = Permission::findById($request->id);

        $permission->name = $request->name;
        $permission->save();

        return response()->json([
            'msg' => 'Permiso Editado Correctamente'
        ], 200);

    }

    public function destroy(Request $request)
    {
        //
        if (!$request->ajax()) {
            return response()->json([
                'msg' => 'Petición no Valida',
                'status' => 500
            ], 500);
        }

        $permission = Permission::findById($request->id);

        if (!$permission) {
            return response()->json([
                'msg' => 'Hubo un error al intentar eliminar el permiso',
                'status' => 400
            ], 400);
        }

        $permission->delete();

        return response()->json([
            'msg' => 'Permiso Eliminado Correctamente',
            'status' => 200
        ], 200);


    }
}
