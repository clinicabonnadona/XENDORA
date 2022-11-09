<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\FORMAFAR;
use App\FORMAPRE;
use App\Http\Controllers\Controller;
use App\SUMINISTROS;
use Illuminate\Http\Request;

class SuministrosController extends Controller
{
    //
    public function getSuministros()
    {
        $suministros = SUMINISTROS::with('formafar', 'formapre')
            ->where('SumEst', '=', '1')
            ->orderBy('SumNomG', 'asc')
            ->get();
        return response()->json($suministros);
    }

    public function getFormaFarmaceutica()
    {
        $ff = FORMAFAR::with('user')->get();
        return response()->json($ff);
    }

    public function getFormaPresentacion()
    {
        $fp = FORMAPRE::with('user')->get();
        return response()->json($fp);
    }

    public function validateIfExistsSumCod($SumCod)
    {
        $query = SUMINISTROS::where('SumCod', '=', $SumCod)
            ->get();

        if (sizeof($query) == 0) {
            return response()->json([
                'msg' => 'Hubo un Error'
            ], 204);
        } else {
            return response()->json([
                'msg' => 'Ya Existe Un Suministro Con Este Código'
            ], 200);
        }
    }

    public function saveSuministro(Request $request)
    {
        $query = SUMINISTROS::where('SumCod', '=', $request->SumCod)
            ->get();

        if (sizeof($query) == 0) {

            $user = auth()->id();
            $altocosto = 0;
            $suministro = new SUMINISTROS();

            if ($request->SumReg == 1) {
                if ($request->SumPreReg > 10000000) {
                    $altocosto = 1;
                }
            }

            $suministro->SumCod = $request->SumCod;
            $suministro->SumNomG = $request->SumNomG;
            $suministro->SumNomC = $request->SumNomC;
            $suministro->formafar_id = $request->formaFar;
            $suministro->formapre_id = $request->formaPre;
            $suministro->SumPosNoPos = $request->SumPosNoPos;
            $suministro->SumReg = $request->SumReg;
            $suministro->SumPreReg = $request->SumPreReg;
            $suministro->SumAltCos = $altocosto;
            $suministro->SumEst = 1;
            $suministro->user_id = $user;
//            $suministro->created_at = date('Y-d-m H:i:s');
//            $suministro->updated_at = date('Y-d-m H:i:s');

            if ($suministro->save()) {

                return response()->json([
                    'msg' => 'Sumistro Creado Correctamente'
                ], 201);

            } else {
                return response()->json([
                    'msg' => 'Hubo un Error'
                ], 500);
            }

        } else {
            return response()->json([
                'msg' => 'Ya Existe Un Suministro Con Este Código'
            ], 200);
        }

    }


    public function updateSuministro(Request $request)
    {
        $altocosto = 0;
        $suministro = SUMINISTROS::find($request->id);

        if ($request->SumReg == 1) {
            if ($request->SumPreReg > 10000000) {
                $altocosto = 1;
            }
        }
        $suministro->SumCod = $request->SumCod;
        $suministro->SumNomG = $request->SumNomG;
        $suministro->SumNomC = $request->SumNomC;
        $suministro->formafar_id = $request->formaFar;
        $suministro->formapre_id = $request->formaPre;
        $suministro->SumPosNoPos = $request->SumPosNoPos;
        $suministro->SumReg = $request->SumReg;
        $suministro->SumPreReg = $request->SumPreReg;
        $suministro->SumAltCos = $altocosto;
//        $suministro->updated_at = date('Y-d-m H:i:s');

        if ($suministro->save()) {
            return response()->json([
                'msg' => 'Sumistro Actualizado Correctamente'
            ], 200);
        } else {
            return response()->json([
                'msg' => 'Hubo un Error'
            ], 500);
        }

    }

    public function destroySuministro(Request $request)
    {
        $suministro = SUMINISTROS::find($request->id);

        if (!$suministro) {
            return response()->json([
                'msg' => 'Hubo un Error'
            ], 500);
        } else {

            $suministro->SumEst = 0;
//            $suministro->updated_at = date('Y-d-m H:i:s');
            $suministro->save();

            return response()->json([
                'msg' => 'Sumistro Desactivado Correctamente'
            ], 201);
        }

    }

}
