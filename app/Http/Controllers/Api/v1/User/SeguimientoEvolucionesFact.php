<?php

namespace App\Http\Controllers\Api\v1\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SeguimientoEvolucionesFact extends Controller
{
    //

    public function __construct()
    {
        $this->middleware([
            'auth'
        ]);
    }


    // ==============================================================================
    // Function To return View
    // ==============================================================================

    public function index()
    {
        return view('shared.reportes.seguimientoEvolucionesFact');
    }

    // ==============================================================================
    // Functions
    // ==============================================================================

    public function getSegEvolutionsFact(Request $request, $initDate = '', $endDate = '')
    {
        if ($request->ajax()) {


            if (!$initDate && !$endDate) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Parameters Cannot be Empty'
                ]);
            } else {
                try {

                    $querySegEvolutions = DB::connection('sqlsrv_hosvital')
                        ->select("SELECT * FROM SEGUI_EVOLUCIONES_FACT('$initDate','$endDate')");


                    if (count($querySegEvolutions) > 0) {

                        $evolutions = [];

                        foreach ($querySegEvolutions as $item) {

                            $tempEvolutionsFact = array(
                                'doctorCode' => $item->CODIGO,
                                'doctorName' => $item->MEDICO,
                                'doctorSpeciality' => $item->ESPECIALIDAD,
                                'patientDocument' => $item->DOC,
                                'patientDocumentType' => $item->T_DOC,
                                'patientName' => $item->PACIENTE,
                                'patientPavilion' => $item->PABELLON,
                                'patientEvoDate' => $item->FECHA_EVO,
                            );

                            $evolutions[] = $tempEvolutionsFact;
                        }

                        if (count($evolutions) > 0) {

                            return response()
                                ->json([
                                    'msg' => 'Ok',
                                    'status' => 200,
                                    'data' => $evolutions
                                ]);

                        } else {
                            return response()
                                ->json([
                                    'msg' => 'Seg Evolutions Array Empty',
                                    'status' => 204,
                                    'data' => []
                                ]);
                        }
                    } else {

                        return response()
                            ->json([
                                'msg' => 'Query Seg Evolutions Empty',
                                'status' => 204,
                                'data' => []
                            ]);
                    }
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        } else {
            return response()
                ->json([
                    'msg' => 'Bad Request',
                    'status' => 500
                ]);
        }
    }
}
