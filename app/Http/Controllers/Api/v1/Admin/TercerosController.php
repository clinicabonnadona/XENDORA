<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\TERCEROS;
use App\TIPDOC;
use App\User;
use Illuminate\Http\Request;

class TercerosController extends Controller
{
    //
    public function getTerceros()
    {
        $terceros = TERCEROS::with('tipdoc')
                                ->where('TerEst', '=', '1')
                                ->orderBy('TerRazSoc', 'asc')
                                ->get();
        return response()->json($terceros);
    }

    public function getTDocs()
    {
        $tdocs = TIPDOC::all();
        return response()->json($tdocs);
    }

    public function saveTercero(Request $request)
    {
        $user = auth()->id();
        $query = TERCEROS::where('TerCod', '=', $request->TerCod)->get();

        if (count($query) > 0) {

            return response()->json([
                'msg' => 'Error'
            ], 500);

        } else {

            $tercero = TERCEROS::create([
                'TerCod' => $request->TerCod,
                'tipdoc_id' => $request->tipdoc_id,
                'TerRazSoc' => $request->TerRazSoc,
                'tipterc_id' => 1,
                'TerDir' => $request->TerDir,
                'TerTel' => $request->TerTel,
                'TerActNeg' => 1,
                'TerEst' => 1,
                'user_id' => $user,
            ]);

            return response()->json([
                'msg' => 'Tercero Guardado Correctamente'
            ], 201);

        }
    }

    public function updateTercero(Request $request)
    {
        $tercero = TERCEROS::find($request->id);
        $tercero->TerCod = $request->TerCod;
        $tercero->tipdoc_id = $request->tipdoc_id;
        $tercero->TerRazSoc = $request->TerRazSoc;
        $tercero->TerDir = $request->TerDir;
        $tercero->TerTel = $request->TerTel;
        $tercero->TerActNeg = $request->TerActNeg;

        if ($tercero->save()) {
            return response()->json([
                'msg' => 'Tercero Actualizado Correctamente'
            ], 200);
        } else {
            return response()->json([
                'msg' => 'Hubo un Error'
            ], 500);
        }
    }

    public function destroyTercero(Request $request)
    {
        $tercero = TERCEROS::find($request->id);


        if (!$tercero) {
            return response()->json([
                'msg' => 'Hubo un Error'
            ], 500);
        } else {

            $tercero->TerEst = 0;
            $tercero->save();

            return response()->json([
                'msg' => 'Tercero Creado Correctamente'
            ], 200);
        }

    }

}
