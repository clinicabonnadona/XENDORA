<?php

namespace App\Http\Controllers\Api\v1\User;

use App\FamilyIncomeRecord;
use App\Http\Controllers\Controller;
use App\VisitorRecord;
use App\VisitorRecordTime;
use App\VisitorType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserOrientationController extends Controller
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
        return view('shared.UserOrientation.UserOrientation');
    }

    // ==============================================================================
    // Functions
    // ==============================================================================

    public function getCenso(Request $request)
    {

        if ($request->ajax()) {
            try {
                // CONSULTA PATA OBTENER LAS TORRES QUE ESTEN ACTIVAS EN LA ORGANIZACIÓN
                $query_torres = DB::connection('sqlsrv_xendora')
                    ->select("SELECT * FROM TORRES WHERE towerState = 1");

                if (count($query_torres) > 0) {
                    $torres = [];

                    foreach ($query_torres as $tower) {

                        // CONSULTA PARA OBTENER LA RELACIÓN DE TORRE PABELLÓN TENIENDO COMO PARAMETRO EL CÓDIGO DE LA TORRE
                        $query_torres_pavs = DB::connection('sqlsrv_xendora')
                            ->select("SELECT * FROM HITO_TOWER_PAVILIONS('$tower->towerCode') ORDER BY pavFloor DESC");

                        if (count($query_torres_pavs) > 0) {

                            $torres_pav = [];

                            foreach ($query_torres_pavs as $tower_pav) {

                                // CONSULTA PARA OBTENER LOS PABELLONES TENIIENDO COMO PARAMETRO EL CÓDIGO DEL PABELLÓN
                                $query = DB::connection('sqlsrv_hosvital')
                                    ->select("SELECT * FROM HITO_PABELLONES('$tower_pav->pavCode')");

                                if (count($query) > 0) {

                                    $records = [];

                                    foreach ($query as $item) {

                                        // CONSULTA PARA TRAER PACIENTES DE LAS HABITACIONES ENVIANDO COMO PARAMETRO EL CÓDIGO DEL PABELLÓN
                                        $query2 = DB::connection('sqlsrv_hosvital')
                                            ->select("SELECT * FROM HITO_CENSOREAL('$item->CODIGO_PABELLON')");

                                        if (count($query2) > 0) {

                                            $habs = [];

                                            foreach ($query2 as $cat) {

                                                if ($cat->PREALTA != null) {
                                                    $cat->PREALTA = 1;
                                                } else {
                                                    $cat->PREALTA = 0;
                                                }

                                                $temp1 = array(
                                                    'pavCode' => $cat->COD_PAB,
                                                    'pavName' => $cat->PABELLON,
                                                    'habitation' => $cat->CAMA,
                                                    'hab_status' => $cat->ESTADO,
                                                    'patient_doc' => trim($cat->NUM_HISTORIA),
                                                    'patient_doctype' => trim($cat->TI_DOC),
                                                    'patient_name' => $cat->NOMBRE_PACIENTE,
                                                    'patient_eps_nit' => $cat->EPS_NIT,
                                                    'patient_eps' => $cat->EPS,
                                                    'patient_eps_email' => $cat->EPS_EMAIL,
                                                    'contract' => $cat->CONTRATO,
                                                    'attention_type' => $cat->TIPO,
                                                    'admission_date' => $cat->FECHA_INGRESO,
                                                    'admission_num' => trim($cat->INGRESO),
                                                    'age' => $cat->EDAD,
                                                    'gender' => $cat->SEXO,
                                                    'real_stay' => $cat->EstanciaReal,
                                                    'diagnosis' => $cat->DX,
                                                    'prealta' => $cat->PREALTA,
                                                );

                                                $habs[] = $temp1;
                                            }

                                            $temp2 = array(
                                                //'towerCode' => $tower_pav->towerCode,
                                                'pavCode' => $item->CODIGO_PABELLON,
                                                'pavName' => $item->NOMBRE_PABELLON,
                                                'pavFloor' => $tower_pav->pavFloor,
                                                'habs' => $habs
                                            );

                                            // ARRAY QUE ALMACENA LA INFORMACIÓN DE CADA CAMA POR PABELLÓN
                                            $records = $temp2;
                                        } else {
                                            return response()
                                                ->json([
                                                    'msg' => 'El query de pabellones no ha devuelto niguna respuesta',
                                                    'data' => [],
                                                    'status' => 400
                                                ]);
                                        }
                                    }
                                }
                                // ARRAY QUE ALMACENA PARA CADA PABELLÓN LA INFORMACIÓN DE LA CAMAS
                                $torres_pav[] = $records;
                            }
                        }

                        $temp5 = array(
                            'towerCode' => $tower->towerCode,
                            'towerDescription' => $tower->towerDescription,
                            'pavilions' => $torres_pav
                        );

                        // ARRAY QUE ALMACENA LA INFORMACIÓN DE LAS TORRES CON LA INFORMACIÓN DE LOS PABELLONES Y CAMAS
                        $torres[] = $temp5;
                    }

                    return response()
                        ->json([
                            'msg' => 'Ok',
                            'data' => $torres,
                            'status' => 200
                        ]);
                } else {

                    return response()
                        ->json([
                            'msg' => 'empty response in towers request',
                            'data' => [],
                            'status' => 400
                        ]);
                }
            } catch (\Throwable $e) {
                throw $e;
            }
        }
    }

    // CREATE A VISITOR RECORD
    public function savePatientVisitors(Request $request)
    {
        if ($request->ajax()) {

            try {

                if ($this->checkIfPatientStillInCenso($request->patientDocument, $request->patientDocumentType, $request->patientAdmConsecutive)) { // VALIDAR SI EL PACIENTE CONTINUA EN EL CENSO

                    // VALIDACIÓN DE CAMPOS ESENCIALES PARA EL RESTO DE VALIDACIONES
                    $validator = Validator::make($request->all(), [
                        'patientDocument' => 'required',
                        'patientDocumentType' => 'required',
                        'patientAdmConsecutive' => 'required',
                        'patientVisitorDocument' => 'required',
                        'patientVisitorName' => 'required'
                    ]);

                    if ($validator->fails()) { // SI FALLA LA VALIDACIÓN DE CAMPOS ESENCIALES PARA EL RESTO DEL GÓDIGO SE RETORNA ESTE MENSAJE

                        return response()
                            ->json([
                                'msg' => 'Couldnt Save the Info',
                                'status' => 500,
                                'errors' => $validator->errors()
                            ]);

                        //
                    } else { // SI NO FALLA ENTRA ACÁ

                        if (!$this->checkIfPatientExistInDB($request->patientDocument, $request->patientDocumentType, $request->patientAdmConsecutive)) { // VALIDACIÓN SI EL PACIENTE NO EXISTE EN LA BD

                            // AQUÍ SE CREA EL REGISTRO DEL PACIENTE
                            $patientInfo = FamilyIncomeRecord::create([
                                'patientName' => $request->patientName,
                                'patientDocument' => $request->patientDocument,
                                'patientDocumentType' => $request->patientDocumentType,
                                'patientAdmConsecutive' => $request->patientAdmConsecutive,
                                'patientAdmDate' => $request->patientAdmDate,
                                'patientHabitation' => $request->patientHabitation,
                                'user_id' => auth()->id()
                            ]);

                            if (!$this->checkIfPatientVisitorExistInDB($request->patientVisitorDocument)) { // VALIDACIÓN SI EL FAMILIAR DEL PACIENTE NO EXISTE

                                // BUSQUEDA DEL TIPO DE VISITANTE
                                $visitorType = VisitorType::find($request->patientVisitorType);

                                if ($visitorType) {

                                    // CREACIÓN DEL VISITANTE AQUÍ
                                    $visitorRecord = VisitorRecord::create([
                                        'visitorDocument' => $request->patientVisitorDocument,
                                        'visitorDocumentType' => "CC",
                                        'visitorName' => $request->patientVisitorName,
                                        'visitorRelationship' => $request->patientVisitorRelationship,
                                        'visitor_type_id' => $visitorType->id,
                                        'family_income_record_id' => $patientInfo->id,
                                    ]);

                                    // NUEVAMENTE VALIDACIÓN SI EL FAMILIAR DEL PACIENTE SE CREO CORRECTAMENTE Y EXISTE
                                    if ($this->checkIfPatientVisitorExistInDB($visitorRecord->visitorDocument)) {

                                        //BUSQUEDA DE REGISTRO DEL FAMILIAR Y EL PACIENTE CORRESPONDIENTE PARA EXTRAER IDS
                                        $visitorInfo = VisitorRecord::find($visitorRecord->id);
                                        $patientRegisteredInfo = FamilyIncomeRecord::find($visitorRecord->family_income_record_id);

                                        if ($visitorInfo) {

                                            //return $patientRegisteredInfo;
                                            /*  if ($this->checkIfPatientVisitorIsStillInClinic($visitorInfo->visitorDocument)) { // VALIDACIÓN SI EL FAMILIAR AÚN SE ENCUENTRA EN LA CLÍNICA

                                                return response() // SE RETORNA ESTE MENSAJE SI AÚN ESTA
                                                    ->json([
                                                        'msg' => 'Cannot Create Time Record, Visitor is Still in Clinic',
                                                        'status' => 500,
                                                    ]);

                                                //
                                            } else { */ // SI EL FAMILIAR YA NO ESTA EN LA CLÍNICA SE PUEDE CREAR UN NUEVO REGISTRO

                                            // SE CREA EL REGISTRO DE TIEMPOS DEL FAMILIAR AQUÍ SOLAMENTE CON FECHA DE INGRESO
                                            //return 'ok hasta aquí';
                                            $visitorRecordTime = VisitorRecordTime::create([
                                                'visitorEntryDate' => $request->patientVisitorAdmDate,
                                                'family_income_record_id' => $patientRegisteredInfo->id,
                                                'patientAdmConsecutive' => $patientRegisteredInfo->patientAdmConsecutive,
                                                'visitor_record_id' => $visitorInfo->id,
                                            ]);

                                            // VALIDACIÓN DE CREACIÓN DE DEL REGISTRO DE LOS TIEMPOS
                                            if ($visitorRecordTime) {
                                                return response()
                                                    ->json([
                                                        'msg' => 'Record Saved Successfully!',
                                                        'status' => 201,
                                                    ], 201);

                                                // CASO EN QUE FALLE GUARDADO
                                            } else {
                                                return response()
                                                    ->json([
                                                        'msg' => 'Couldnt Save the Info, check the Visitor Record Time Method',
                                                        'status' => 409,
                                                        'errors' => $validator->errors()
                                                    ]);

                                                //
                                            }

                                            //
                                            /* } */
                                        } else { // CASO EN QUE HAYA PROBLEMAS AL CONSULTAR LA INFORMACION DEL VISITANTE

                                            return response()
                                                ->json([
                                                    'msg' => 'We Couldnt Find this Visitors Info in DB',
                                                    'status' => 204,
                                                ]);

                                            //
                                        }

                                        // CASO EN QUE EL VISITANTE NO EXISTA
                                    } else {
                                        return response()
                                            ->json([
                                                'msg' => 'We Couldnt Find this Visitors Info in DB',
                                                'status' => 204,
                                            ]);

                                        //
                                    }

                                    //CASO CUANDO HAY PROBLEMAS PARA CONSULTAR LA INFORMACIÓN DE LOS TIPOS DE VISITANTE
                                } else {

                                    return response()
                                        ->json([
                                            'msg' => 'We Couldnt Find this Visitors Type in DB',
                                            'status' => 204,
                                        ]);

                                    //
                                }
                            } else { // SI EL FAMILIAR DEL PACIENTE EXISTE SE RETORNA ESTO

                                return response() //NOTIFICACIÓN CUANDO EL FAMILIAR DEL PACIENTE NO EXISTA
                                    ->json([
                                        'msg' => 'Couldnt Save the Info, the Patient Visitor Already Exists in Database',
                                        'status' => 409,
                                        'errors' => $validator->errors()
                                    ]);

                                //
                            }

                            //
                        } else if ($this->checkIfPatientExistInDB($request->patientDocument, $request->patientDocumentType, $request->patientAdmConsecutive)) { // VALIDACIÓN SI EL PACIENTE YA EXISTE EN LA BD

                            if (!$this->checkIfPatientVisitorExistInDB($request->patientVisitorDocument)) { // VALIDAR SI EL FAMILIAR DEL PACIENTE NO EXISTE EN LA BD

                                // SE CONSULTA INFORMACIÓN DEL PACIENTE PARA HACER LA RELACIÓN CON LA TABLA FAMILIAR
                                $patientInfo = FamilyIncomeRecord::where([
                                    ['patientDocument', '=', $request->patientDocument],
                                    ['patientDocumentType', '=', $request->patientDocumentType],
                                    ['patientAdmConsecutive', '=', $request->patientAdmConsecutive],
                                ])->get();


                                // VALIDAR INFORMACIÓN DEL TIPO DE VISITANTE
                                $visitorType = VisitorType::find($request->patientVisitorType);

                                if ($visitorType) { // SI SE ENCUENTRA DICHA INFORMACIÓN PASA ESTA VALIDACIÓN

                                    // SE CREA EL REGISTRO DEL FAMILIAR
                                    $visitorRecord = VisitorRecord::create([
                                        'visitorDocument' => $request->patientVisitorDocument,
                                        'visitorDocumentType' => "CC",
                                        'visitorName' => $request->patientVisitorName,
                                        'visitorRelationship' => $request->patientVisitorRelationship,
                                        'visitor_type_id' => $visitorType->id,
                                        'family_income_record_id' => $patientInfo[0]->id,
                                    ]);

                                    if ($this->checkIfPatientVisitorExistInDB($visitorRecord->visitorDocument)) { // SE VALIDA NUEVAMENTE SI EL FAMILIAR DEL PACIENTE SE CREÓ CORRECTAMENTE

                                        // CONSULTAR INFORMACIÓN DEL VISITANTE POR EL ID
                                        $visitorInfo = VisitorRecord::find($visitorRecord->id);
                                        // CONSULTAR INFORMACIÓN DEL PACIENTE POR EL ID RELACIONADO
                                        $patientRegisteredInfo = FamilyIncomeRecord::find($visitorRecord->family_income_record_id);

                                        if ($visitorInfo) { // SI ES CORRECTA LA BUSQUEDA DE LA INFORMACIÓN DEL VISITANTE
                                            //return 'ok hasta aquí validando y consultando la info del visitante y del paciente';
                                            /* if ($this->checkIfPatientVisitorIsStillInClinic($visitorInfo->visitorDocument)) { // VALIDACIÓN SI EL FAMILIAR DEL PACIENTE AÚN ESTA EN LA CLÍNICA

                                                return response() // SI AÚN ESTA SE ENVIA ESTA NOTIFICACIÓN
                                                    ->json([
                                                        'msg' => 'Cannot Create Time Record, Visitor is Still in Clinic',
                                                        'status' => 500,
                                                    ]);

                                                // 
                                            } else { */ // SI EL FAMILIAR YA NO ESTA EN LA CLÍNICA CONTINUA ACÁ

                                            // SE CREA EL REGISTRO DE TIEMPOS DEL FAMILIAR
                                            $visitorRecordTime = VisitorRecordTime::create([
                                                'visitorEntryDate' => $request->patientVisitorAdmDate,
                                                'family_income_record_id' => $patientRegisteredInfo->id,
                                                'patientAdmConsecutive' => $patientRegisteredInfo->patientAdmConsecutive,
                                                'visitor_record_id' => $visitorInfo->id,
                                            ]);

                                            // SI SE HA CREADO EL REGISTO CORRECTAMENTE
                                            if ($visitorRecordTime) {

                                                //RETORNA MENSAJE SATISFACTORIO
                                                return response()
                                                    ->json([
                                                        'msg' => 'Record Saved Successfully!',
                                                        'status' => 201,
                                                    ], 201);

                                                //
                                            } else { //SI NO SE HA PODIDO CREAR EL REGISTRO, SE ENVIA MENSAJE INFORMATIVO

                                                return response()
                                                    ->json([
                                                        'msg' => 'Couldnt Save the Info, check the Visitor Record Time Method',
                                                        'status' => 409,
                                                        'errors' => $validator->errors()
                                                    ]);

                                                //
                                            }

                                            //

                                            /* } */
                                        } else {  // SI HAY PROBLEMAS EN CONSULTAR LA INFORMACIÓN DEL VISITANTE SE NOTIFICA ESTO

                                            return response()
                                                ->json([
                                                    'msg' => 'We Couldnt Find this Visitors Info in DB',
                                                    'status' => 204,
                                                ]);

                                            //
                                        }

                                        //
                                    } else { // PACIENTE CON INTENTO DE CREACIÓN Y HUBO PROBLEMAS EN DICHA VALIDACIÓN

                                        return response() // SI NO SE CREA SE ENVIA ESTE MENSAJE
                                            ->json([
                                                'msg' => 'We Couldnt Find this Visitors Info in DB',
                                                'status' => 204,
                                            ]);

                                        //
                                    }

                                    //
                                } else { // SI NO SE EJECUTA CORRECTAMENTE LA QUERY INFORMACIÓN DEL VISITANTE, SE ENVIA ESTE MENSAJE

                                    return response()
                                        ->json([
                                            'msg' => 'We Couldnt Find this Visitors Type in DB',
                                            'status' => 204,
                                        ]);

                                    //
                                }
                            } else if ($this->checkIfPatientVisitorExistInDB($request->patientVisitorDocument)) { // SI EL VISITANTE EXISTE EN LA BD CONTINUA ACÁ

                                /* if ($this->checkIfPatientVisitorExistInDB($request->patientVisitorDocument)) {*/

                                // SE CONSULTA LA INFORMACIÓN DEL VISITANTE A LA BD POR DOCUMENO DEL VISITANTE
                                $visitorInfo = VisitorRecord::where([
                                    ['visitorDocument', '=', $request->patientVisitorDocument]
                                ])->get();

                                // SE CONSULTA LA INFORMACIÓN DEL PACIENTE REGISTRADO POR LA RELACIÓN QUE ESTA EN LA TABLA VISITANTE
                                $patientRegisteredInfo = FamilyIncomeRecord::find($visitorInfo[0]->family_income_record_id);


                                if ($visitorInfo) { // SI LA INFORMACIÓN DEL VISITANTE SE CAPTURA CORRECTAMENTE

                                    if ($this->checkIfPatientVisitorIsStillInClinic($$visitorInfo[0]->visitorDocument)) { // VALIDACIÓN SI EL FAMILIAR DEL PACIENTE AÚN SE ENCUENTRA EN LA CLÍNICA

                                        return response() // SI AÚN ESTA SE ENVIA ESTA NOTIFICACIÓN
                                            ->json([
                                                'msg' => 'Cannot Create Time Record, Visitor is Still in Clinic',
                                                'status' => 500,
                                            ]);

                                        // 
                                    } else { // SI YA EL FAMILIAR NO ESTA EN LA CLÍNICA SIGUE AQUÍ

                                        // SE CREA EL REGISTRO DE LOS TIEMPOS DEL FAMILIAR
                                        $visitorRecordTime = VisitorRecordTime::create([
                                            'visitorEntryDate' => $request->patientVisitorAdmDate,
                                            'family_income_record_id' => $patientRegisteredInfo->id,
                                            'patientAdmConsecutive' => $patientRegisteredInfo->patientAdmConsecutive,
                                            'visitor_record_id' => $visitorInfo[0]->id,
                                        ]);


                                        if ($visitorRecordTime) { // SE SE GUARDA CORRECTAMENTE, SE RETORNA EL MENSAJE SATISFACTORIO

                                            return response()
                                                ->json([
                                                    'msg' => 'Record Saved Successfully!',
                                                    'status' => 201,
                                                ], 201);

                                            //
                                        } else { // SI NO SE GUARDA CORRECTAMENTE SE ENVIA ESTA NOTIFICACIÓN

                                            return response()
                                                ->json([
                                                    'msg' => 'Couldnt Save the Info, check the Visitor Record Time Method',
                                                    'status' => 409,
                                                ]);

                                            //
                                        }

                                        //

                                    }
                                } else { // SI LA INFORMACIÓN CONSULTADA DEL VISITANTE NO SE RETORNA CORRECTAMENTE SE NOTIFICA ESTO

                                    return response()
                                        ->json([
                                            'msg' => 'We Couldnt Find this Visitors Info in DB',
                                            'status' => 204,
                                        ]);

                                    //
                                }

                                //
                                /*  } else {
                                    return response()
                                        ->json([
                                            'msg' => 'We Couldnt Find this Visitors Info in DB',
                                            'status' => 204,
                                        ]);

                                    //
                                } */

                                //
                            } else { // NOTIFIACIÓN CUANDO NO SE ENCUENTRA EL VISITANTE EN LA BD POR ERRORES

                                return response()
                                    ->json([
                                        'msg' => 'Couldnt Save the Info, Please check Patient Visitor Validation, We Couldnt Find this Visitors Info in DB',
                                        'status' => 409,
                                    ]);

                                //
                            }

                            //
                        } else { // SI NO SE PUEDE CONSULTAR LA INFORMACIÓN DEL PACIENTE NOTIFICACIÓN ERROR
                            return response()
                                ->json([
                                    'msg' => 'Couldnt Check if Patient Exist Check the Error',
                                    'status' => 500,
                                ]);

                            //

                        }
                    }
                } else { // SI EL PACIENTE NO ESTA EN EL CENSO SE NOTIFICA ESTO Y SE DEBE VALIDAR EN EL FRONT QUE NO PERMITA GUARDAR NADA Y RECARGUE EL CENSO

                    return response()
                        ->json([
                            'msg' => 'Patient Isnt in Censo',
                            'status' => 500
                        ]);

                    //
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }

    // GET PATIENT VISITORS
    public function getPatientVisitors(Request $request, $patientDoc = '', $patientDoctype = '', $patientAdmissionNum = '')
    {

        if ($request->ajax()) {

            if (!$patientDoc || !$patientDoctype || !$patientAdmissionNum) {
                return response()
                    ->json([
                        'msg' => 'Parameters Cannot Be Empty',
                        'status' => 400
                    ]);
            } else {

                try {

                    $queryPatientVisitors = DB::connection('sqlsrv')
                        ->select("SELECT * FROM getPatientActiveVisitors('$patientDoc', '$patientDoctype', '$patientAdmissionNum') ORDER BY CREACION_REGISTRO DESC");

                    if (count($queryPatientVisitors) > 0) {

                        $visitors = [];

                        foreach ($queryPatientVisitors as $item) {

                            $tempVisitors = array(
                                'patientID' => $item->ID_PACIENTE,
                                'patientName' => $item->NOMBRE_PACIENTE,
                                'patientDocument' => $item->DOCUMENTO_PACIENTE,
                                'patientDocumentType' => $item->TIPO_DOCUMENTO_PACIENTE,
                                'patientAdmConsecutive' => $item->CTVO_INGRESO_PACIENTE,
                                'patientVisitorID' => $item->ID_VISITANTE,
                                'patientVisitorDocument' => $item->DOCUMENTO_VISITANTE,
                                'patientVisitorName' => $item->NOMBRE_VISITANTE,
                                'patientVisitorTypeID' => $item->TIPO_VISITANTE_ID,
                                'patientVisitorType' => $item->TIPO_VISITANTE_NOMBRE,
                                'patientVisitorRelationship' => $item->PARENTESCO_VISITANTE,
                                'patientVisitorTimeRecordID' => $item->ID_REGISTRO_TIEMPO,
                                'patientVisitorAdmDate' => $item->ENTRADA_VISITANTE,
                                'patientVisitorOutDate' => $item->SALIDA_VISITANTE,
                                'patientVisitorHoursDuration' => $item->ENTRADA_VISITANTE,
                            );

                            $visitors[] = $tempVisitors;
                        }

                        if (count($visitors) > 0) {

                            return response()
                                ->json([
                                    'msg' => 'Ok',
                                    'data' => $visitors,
                                    'status' => 200
                                ]);

                            //
                        } else {

                            return response()
                                ->json([
                                    'msg' => 'Empty Visitors Array',
                                    'status' => 204
                                ]);

                            //
                        }
                    } else {

                        return response()
                            ->json([
                                'msg' => 'Empty Visitors Query Result',
                                'status' => 204
                            ]);

                        //
                    }
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        }
    }

    // CHECK IF PATIENT EXIST IN LOCAL DB
    public function checkIfPatientExistInDB($document = '', $docType = '', $admConsecutive = '')
    {

        if (!$document || !$docType || !$admConsecutive) {
            return response()
                ->json([
                    'msg' => 'Parameters Cannot Be Empty',
                    'status' => 400
                ]);
        } else {

            try {

                $queryPatientInDB = DB::connection('sqlsrv')
                    ->table('family_income_records')
                    ->where([
                        ['patientDocument', '=', $document],
                        ['patientDocumentType', '=', $docType],
                        ['patientAdmConsecutive', '=', $admConsecutive]
                    ])->get();

                if (count($queryPatientInDB) > 0) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }

    // CHECK IF PATIENT VISITOR EXIST IN LOCAL DB
    public function checkIfPatientVisitorExistInDB($visitorDocument = '')
    {

        if (!$visitorDocument) {
            return response()
                ->json([
                    'msg' => 'Parameters Cannot Be Empty',
                    'status' => 400
                ]);
        } else {

            try {

                $queryPatientVisitorInDB = DB::connection('sqlsrv')
                    ->table('visitor_records')
                    ->where([
                        ['visitorDocument', '=', $visitorDocument]
                    ])->get();

                if (count($queryPatientVisitorInDB) > 0) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }

    public function checkIfPatientVisitorIsStillInClinic($visitorDocument = '')
    {

        if ($visitorDocument) {
            if ($this->checkIfPatientVisitorExistInDB($visitorDocument)) {

                $visitorInfo = VisitorRecord::where([
                    'visitorDocument', '=', $visitorDocument
                ])->get();

                $visitorStillInClinic = VisitorRecordTime::where([
                    ['visitor_record_id', '=', $visitorInfo[0]->visitor_record_id],
                    ['visitorOutDate', '=', null]
                ])->get();

                if ($visitorStillInClinic) {
                    return true;
                } else {
                    return false;
                }

                //
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // CHECK IF PATIENT IS STILL IN CENSO
    public function checkIfPatientStillInCenso($document = '', $docType = '', $admConsecutive = '')
    {
        if (!$document || !$docType || !$admConsecutive) {
            return response()
                ->json([
                    'msg' => 'Parameters Cannot Be Empty',
                    'status' => 400
                ]);
        } else {

            try {

                $queryPatientInCenso = DB::connection('sqlsrv_hosvital')
                    ->select("SELECT * FROM MAEPAB1 WHERE MPUced = '$document' AND MPUDoc = '$docType' AND MPCtvIn = '$admConsecutive'");

                if (count($queryPatientInCenso) > 0) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}
