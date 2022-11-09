<?php

namespace App\Http\Controllers\Api\v1\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\ShippingPurchaseOrders;
use Carbon\Carbon;

class ShippingPurchaseOrdersController extends Controller
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
        return view('shared.Farmacia.ShippingPurchaseOrders');
    }

    // ==============================================================================
    // Functions
    // ==============================================================================

    public function getToShippingPurchaseOrders(Request $request, $initDate = '', $endDate = '')
    {

        if (!$initDate && !$endDate) {
            return response()->json([
                'status' => 400,
                'message' => 'Parameters Cannot be Empty'
            ]);
        } else {
            try {

                $queryOrders = DB::connection('sqlsrv_hosvital')
                    ->select(
                        DB::raw(
                            "SELECT * FROM XENDORA_ORDENES_COMPRAS('$initDate', '$endDate')"
                        )
                    );

                if (count($queryOrders) > 0) {
                    $records = [];

                    foreach ($queryOrders as $item) {

                        $tempShippingPurchaseOrders = array(
                            'NroOrden' => $item->ORDEN_NRO,
                            'ProveedorNit' => $item->COD_PROVEED,
                            'ProveedorName' => trim($item->PROVEED),
                            'FechaOrden' => $item->FECHA_ORDEN,
                            //'ExtraInfo' => $recordsInt,
                        );

                        if (!empty($tempShippingPurchaseOrders)) {
                            $nroOrdenParameter = $tempShippingPurchaseOrders['NroOrden'];

                            $queryOrdersExtraInfo = DB::connection('sqlsrv')
                                ->select(
                                    DB::raw(
                                        "SELECT * FROM XENDORA_FARMACIA_ORDENES_EXTRA_INFORMATION('$nroOrdenParameter')"
                                    )
                                );

                            if (count($queryOrdersExtraInfo) > 0) {

                                $shippDate = null;
                                $managShippDate = null;
                                $managDevoShippDate = null;

                                foreach ($queryOrdersExtraInfo as $row) {

                                    if ($row->FechaEnvio) {
                                        $shippDate = Carbon::parse($row->FechaEnvio)->format('Y-m-d\TH:i');
                                    }

                                    if ($row->FechaEnvioaGerencia) {
                                        $managShippDate = Carbon::parse($row->FechaEnvioaGerencia)->format('Y-m-d\TH:i');
                                    }

                                    if ($row->FechaDevoGerencia) {
                                        $managDevoShippDate = Carbon::parse($row->FechaDevoGerencia)->format('Y-m-d\TH:i');
                                    }

                                    $tempQueryExtraInfo = array(
                                        'ShippingDate' => $shippDate,
                                        'ManagementShippingDate' => $managShippDate,
                                        'ManagementDevolutionDate' => $managDevoShippDate,
                                        'Comments' => $row->Comments,
                                        'Status' => $row->Status,
                                    );
                                }
                            } else {
                                $tempQueryExtraInfo = array(
                                    'Status' => 0
                                );
                            }
                        }
                        $records[] = array_merge($tempShippingPurchaseOrders, $tempQueryExtraInfo);
                    }

                    if (count($records) > 0) {

                        return response()->json([
                            'status' => 200,
                            'message' => 'Ok',
                            'data' => $records
                        ]);
                    } else {

                        return response()->json([
                            'status' => 204,
                            'message' => 'Empty Orders Array',
                            'data' => []
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 204,
                        'message' => 'Empty Query Order Array',
                        'data' => []
                    ]);
                }
            } catch (\Throwable $e) {
                throw $e;
            }
        }
    }


    public function saveShippingPurchaseOrders(Request $request)
    {

        if (!$request->ajax()) {

            return response()
                ->json([
                    'msg' => 'Petición no Valida',
                    'status' => 400,
                ]);
        } else {


            try {

                $mSDate = NULL;
                $mSDDate = NULL;
                $sDate = NULL;

                if ($request->ManagementShippingDate) {
                    $mSDate = Carbon::parse($request->ManagementShippingDate)->format('Y-m-d H:i');
                }

                if ($request->ManagementDevolutionDate) {
                    $mSDDate = Carbon::parse($request->ManagementDevolutionDate)->format('Y-m-d H:i');
                }

                if ($request->shippingDate) {
                    $sDate = Carbon::parse($request->shippingDate)->format('Y-m-d H:i');
                }

                $commentsR = $request->Comments;

                if (count($request->selectedOrders) > 0) {

                    $records = [];

                    foreach ($request->selectedOrders as $item) {

                        $nroOrden = $item['NroOrden'];
                        $fechaOrden = $item['FechaOrden'];

                        $queryToCheckIfExists = DB::connection('sqlsrv')
                            ->select(
                                "SELECT * FROM getShippedPurchaseOrders('$nroOrden', '$fechaOrden')"
                            );

                        if (count($queryToCheckIfExists) > 0) {
                            return response()
                                ->json([
                                    'msg' => 'Error al Guardar, Registro duplicado',
                                    'status' => 400
                                ]);
                        } else {

                            $records[] = [
                                'NroOrden' => $item['NroOrden'],
                                'ProveedorNit' => $item['ProveedorNit'],
                                'ProveedorName' => trim($item['ProveedorName']),
                                'FechaOrden' => $item['FechaOrden'],
                                'FechaEnvioaGerencia' => $mSDate,
                                'FechaDevoGerencia' => $mSDDate,
                                'FechaEnvio' => $sDate,
                                'Comments' => $commentsR,
                                'Status' => 1,
                                'user_id' => auth()->user()->id,
                                'created_at' => Carbon::parse(Carbon::now())->format('Y-m-d H:m:s'),
                                'updated_at' => Carbon::parse(Carbon::now())->format('Y-m-d H:m:s')
                            ];
                        }
                    }

                    if (count($records) > 0) {
                        $stored = ShippingPurchaseOrders::insert($records);

                        if ($stored) {
                            return response()
                                ->json([
                                    'msg' => 'Ordenes de Compras Enviadas',
                                    'status' => 200,
                                ]);
                        }
                    } else {
                        return response()
                            ->json([
                                'msg' => 'Error al Guardar Ordenes de Compras',
                                'status' => 400,
                                'data' => []
                            ]);
                    }
                }


            } catch (\Throwable $th) {
                throw $th;
            }

            /* try {

                $ManagementShippingDate = "";
                $ManagementDevolutionDate = "";
                $shippingDate = "";

                if ($request->ManagementShippingDate) {
                    //$ManagementShippingDate = Carbon::parse($request->ManagementShippingDate)->format('Y-m-d H:i');
                    $ManagementShippingDate = $request->ManagementShippingDate;
                }

                if ($request->ManagementDevolutionDate) {
                    $ManagementDevolutionDate = Carbon::parse($request->ManagementDevolutionDate)->format('Y-m-d H:i');
                }

                if ($request->shippingDate) {
                    //$shippingDate = Carbon::parse($request->shippingDate)->format('Y-m-d H:i');
                    $shippingDate = $request->shippingDate;
                }

                $commentsR = $request->Comments;

                if (count($request->selectedOrders) > 0) {

                    $records = [];

                    foreach ($request->selectedOrders as $item) {

                        $nroOrden = $item['NroOrden'];
                        $fechaOrden = $item['FechaOrden'];

                        $queryToCheckIfExists = DB::connection('sqlsrv')
                            ->select(
                                "SELECT * FROM getShippedPurchaseOrders('$nroOrden', '$fechaOrden')"
                            );

                        if (count($queryToCheckIfExists) > 0) {
                            return response()
                                ->json([
                                    'msg' => 'Error al Guardar, Registro duplicado',
                                    'status' => 400
                                ]);
                        } else {

                            $records[] = [
                                'NroOrden' => $item['NroOrden'],
                                'ProveedorNit' => $item['ProveedorNit'],
                                'ProveedorName' => trim($item['ProveedorName']),
                                'FechaOrden' => $item['FechaOrden'],
                                'FechaEnvioaGerencia' => $ManagementShippingDate == "" ? "" : $ManagementShippingDate,
                                'FechaDevoGerencia' => $ManagementDevolutionDate,
                                'FechaEnvio' => $shippingDate,
                                'Comments' => $commentsR,
                                'Status' => 1,
                                'user_id' => auth()->user()->id,
                                'created_at' => Carbon::parse(Carbon::now())->format('Y-m-d H:m:s'),
                                'updated_at' => Carbon::parse(Carbon::now())->format('Y-m-d H:m:s')
                            ];
                        }
                    }

                    if (count($records) > 0) {
                        $stored = ShippingPurchaseOrders::insert($records);

                        if ($stored) {
                            return response()
                                ->json([
                                    'msg' => 'Ordenes de Compras Enviadas',
                                    'status' => 200,
                                ]);
                        }
                    } else {
                        return response()
                            ->json([
                                'msg' => 'Error al Guardar Ordenes de Compras',
                                'status' => 400,
                                'data' => []
                            ]);
                    }
                }
            } catch (\Throwable $e) {
                throw $e;
            } */
        }
    }


    public function updatedShippingPurchaseOrders(Request $request)
    {
        if (!$request->ajax()) {

            return response()
                ->json([
                    'msg' => 'Petición no Valida',
                    'status' => 400,
                ]);
        } else {
            try {

                $queryToCheckIfExists = ShippingPurchaseOrders::where([
                    ['NroOrden', '=', $request->selectedOrders[0]['NroOrden']],
                    ['FechaOrden', '=', $request->selectedOrders[0]['FechaOrden']]
                ])
                    ->first();

                if ($queryToCheckIfExists) {


                    $shippingDate = null;

                    if ($request->shippingDate) {
                        $shippingDate = Carbon::parse($request->shippingDate)
                            ->format('Y-m-d H:i:s');
                    }


                    $commentsR = $request->Comments;

                    $queryToCheckIfExists->FechaEnvio = $shippingDate;
                    $queryToCheckIfExists->FechaEnvioaGerencia = $request->ManagementShippingDate != "" ? Carbon::parse($request->ManagementShippingDate)->format('Y-m-d H:i') : "";
                    $queryToCheckIfExists->FechaDevoGerencia = $request->ManagementDevolutionDate != "" ? Carbon::parse($request->ManagementDevolutionDate)->format('Y-m-d H:i') : "";
                    $queryToCheckIfExists->Comments = $commentsR;
                    $queryToCheckIfExists->updated_at = Carbon::parse(Carbon::now())->format('Y-m-d H:m:s');

                    if ($queryToCheckIfExists->save()) {
                        return response()
                            ->json([
                                'msg' => 'Orden Actualizada Correctamente',
                                'status' => 200,
                            ], 200);
                    } else {
                        return response()
                            ->json([
                                'msg' => 'Error al Actualizar la Orden',
                                'status' => 400,
                            ], 400);
                    }
                } else {
                    return response()
                        ->json([
                            'msg' => 'Respuesta vacia',
                            'status' => 204,
                        ], 204);
                }
            } catch (\Throwable $e) {
                throw $e;
            }
        }
    }


    public function getToShippingPurchaseOrdersReport(Request $request, $initDate = '', $endDate = '')
    {

        if (!$request->ajax()) {
            return response()
                ->json([
                    'msg' => 'Petición no Valida',
                    'status' => 400,
                ]);
        } else {
            if (!$initDate && !$endDate) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Parameters Cannot be Empty'
                ]);
            } else {
                try {

                    $queryOrders = DB::connection('sqlsrv')
                        ->select(
                            DB::raw(
                                "SELECT * FROM XENDORA_FARMACIA_ORDENES_ENVIO_REPORTE('$initDate', '$endDate')"
                            )
                        );

                    if (count($queryOrders) > 0) {
                        $records = [];

                        foreach ($queryOrders as $item) {

                            $tempShippingPurchaseOrders = array(
                                'NroOrden' => $item->NroOrden,
                                'ProveedorNit' => $item->ProveedorNit,
                                'ProveedorName' => trim($item->ProveedorName),
                                'FechaOrden' => $item->FechaOrden,
                                'FechaEnvioaGerencia' => Carbon::parse($item->FechaEnvioaGerencia)->format('Y-m-d H:m'),
                                'FechaDevoGerencia' => Carbon::parse($item->FechaDevoGerencia)->format('Y-m-d H:m'),
                                'ShippingDate' => Carbon::parse($item->FechaEnvio)->format('Y-m-d H:m'),
                                'Comments' => $item->Comments,
                            );

                            if (!empty($tempShippingPurchaseOrders)) {
                                $records[] = $tempShippingPurchaseOrders;
                            }
                        }

                        if (count($records) > 0) {

                            return response()->json([
                                'status' => 200,
                                'message' => 'Ok',
                                'data' => $records
                            ]);
                        } else {

                            return response()->json([
                                'status' => 204,
                                'message' => 'Empty Orders Array',
                                'data' => []
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => 204,
                            'message' => 'Empty Query Order Array',
                            'data' => []
                        ]);
                    }
                } catch (\Throwable $e) {
                    throw $e;
                }
            }
        }
    }

    public function checkIfOrderHasAllDates($nroOrden, $fechaOrden) {

        if(!$nroOrden) {
            return response()->json([
                'msg' => 'Parametro Número de orden vacio en funcion para validar fechas'
            ]);
        }

        if(!$fechaOrden) {
            return response()->json([
                'msg' => 'Parametro fecha de orden vacio en funcion para validar fechas'
            ]);
        }

        try {
            $queryToCheckIfExists = DB::connection('sqlsrv')
                            ->select(
                                "SELECT * FROM getShippedPurchaseOrders('$nroOrden', '$fechaOrden') WHERE FechaEnvioaGerencia IS NOT NULL AND FechaDevoGerencia IS NOT NULL AND FechaEnvio IS NOT NULL"
                            );

                            if (count($queryToCheckIfExists) > 0) {
                                return true;
                            } else {
                                return false;
                            }
        } catch (\Throwable $th) {
            throw $th;
        }


    }
}
