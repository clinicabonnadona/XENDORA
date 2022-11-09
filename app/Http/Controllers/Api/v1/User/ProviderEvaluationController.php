<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Provider;
use App\ProviderType;
use App\ProviderAgent;
use App\ProviderAgentLine;
use App\ProviderAgentLineProviderAgent;
use App\ProviderAgentProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderEvaluationController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('shared.Farmacia.ProviderEvaluation');
    }

    // FUNCTION TO RETURN THE PROVIDERS INFO FROM HOSVITAL DB
    public function getPharmProvidersFromHosvital(Request $request)
    {

        if (!$request->ajax()) return response()->json([
            'msg' => 'Bad Request',
            'status' => 400
        ], 400);

        try {

            $queryProviders = DB::connection('sqlsrv_hosvital')
                ->select("SELECT * FROM XENDORA_PROVEEDORES_FARMACIA()");

            if (sizeof($queryProviders) < 0) return response()->json([
                'msg' => 'Empty Query Provider Response',
                'status' => 204
            ], 204);

            //return json_decode(json_encode($queryProviders), true);
            $providers = [];

            foreach ($queryProviders as $provider) $providers[] = array(
                'providerNit' => trim($provider->NIT),
                'providerName' => trim($provider->RAZON_SOCIAL),
                'providerAddress' => trim($provider->DIRECCION),
                'providerCity' => trim($provider->CIUDAD),
                /* 'providerPhone' => trim($provider->TELEFONO), */
                /* 'providerEmail' => trim($provider->EMAIL) */
            );

            if (sizeof($providers) < 0) return response()->json([
                'msg' => 'Empty Providers Array',
                'status' => 204
            ], 204);

            return response()->json([
                'msg' => 'Ok',
                'status' => 200,
                'count' => count($providers),
                'data' => $providers
            ], 200);

            //
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // FUNCTION TO RETURN THE PROVIDERS INFO FROM XENDORA DB
    public function getPharmProvidersFromXendora(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'msg' => 'Bad Request',
            'status' => 400
        ], 400);

        try {

            $queryProviders = Provider::with('provider_type')
                ->get();

            if (sizeof($queryProviders) < 0) return response()->json([
                'msg' => 'Empty Query Providers Response',
                'status' => 204
            ], 204);

            $providers = [];

            foreach ($queryProviders as $provider) {
                $providers[] = [
                    'xendoraProviderId' => $provider->id,
                    'xendoraProviderNit' => $provider->nit,
                    'xendoraProviderName' => trim($provider->razon_social),
                    'xendoraProviderAddress' => $provider->direccion,
                    'xendoraProviderCity' => $provider->ciudad,
                    'xendoraProviderCreateDate' => Carbon::parse($provider->created_at)->format('Y-m-d H:m:s'),
                    'xendoraProviderType' => $provider->provider_type->descripcion,
                    '_providerStatus' => $provider->estado,
                ];
            }

            if (sizeof($providers) < 0) return response()->json([
                'msg' => 'Empty Providers Array',
                'status' => 204
            ], 204);

            return response()->json([
                'msg' => 'Ok',
                'count' => count($providers),
                'status' => 200,
                'data' => $providers
            ], 200);

            //return $queryProviders;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // FUNCTION TO RETURN THE PROVIDERS TYPES FROM XENDORA DB
    public function getPharmProvidersTypesFromXendora(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'msg' => 'Bad Request',
            'status' => 400
        ], 400);

        try {

            $queryProvidersTypes = ProviderType::all();

            if (sizeof($queryProvidersTypes) < 0) return response()->json([
                'msg' => 'Empty Query Providers Response',
                'status' => 204
            ], 204);

            $providersTypes = [];

            foreach ($queryProvidersTypes as $type) {
                $providersTypes[] = [
                    'xendoraProviderTypeId' => $type->id,
                    'xendoraProviderTypeDescription' => $type->descripcion,
                    'xendoraProviderTypeCreateDate' => Carbon::parse($type->created_at)->format('Y-m-d H:m:s'),
                    '_providerTypeStatus' => $type->estado,
                ];
            }

            if (sizeof($providersTypes) < 0) return response()->json([
                'msg' => 'Empty Providers Array',
                'status' => 204
            ], 204);

            return response()->json([
                'msg' => 'Ok',
                'count' => count($providersTypes),
                'status' => 200,
                'data' => $providersTypes
            ], 200);

            //return $providersTypes;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // FUNCTION TO RETURN THE PROVIDERS AGENT FROM XENDORA DB
    public function getPharmProvidersAgentFromXendora(Request $request, $provider = '')
    {
        if (!$request->ajax()) return response()->json([
            'msg' => 'Bad Request',
            'status' => 400
        ], 400);

        try {

            $queryProvidersAgents = Provider::with('provider_type', 'provider_agents', 'user')
                ->where('provider.id', $provider)
                ->get();

            //return $queryProvidersAgents;
            if (sizeof($queryProvidersAgents) < 0) return response()->json([
                'msg' => 'Empty Query Providers Response',
                'status' => 204
            ], 204);

            $providersAgents = [];

            foreach ($queryProvidersAgents[0]->provider_agents as $agent) {
                $providersAgents[] = [
                    'xendoraProviderAgentId' => $agent->id,
                    'xendoraProviderAgentName' => $agent->nombres,
                    'xendoraProviderAgentLastName' => $agent->apellidos,
                    'xendoraProviderAgentLetters' => substr($agent->nombres, 0, 1) . '' . substr($agent->apellidos, 0, 1),
                    'xendoraProviderAgentPhone' => $agent->telefono,
                    'xendoraProviderAgentEmail' => $agent->email,
                    'xendoraProviderAgentCreateDate' => Carbon::parse($agent->created_at)->format('Y-m-d H:m:s'),
                    'xendoraProviderAgentCreator' => $agent->userId,
                    '_providerAgentStatus' => $agent->estado,
                ];
            }

            if (
                sizeof($providersAgents) < 0
            ) return response()->json([
                'msg' => 'Empty Providers Array',
                'status' => 204
            ], 204);

            return response()->json([
                'msg' => 'Ok',
                'count' => count($providersAgents),
                'status' => 200,
                'data' => $providersAgents
            ], 200);

            //return $providersTypes;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // FUNCTION TO RETURN THE PROVIDERS AGENTS LINES FROM XENDORA DB
    public function getPharmProvidersAgentLinesFromXendora(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'msg' => 'Bad Request',
            'status' => 400
        ], 400);

        try {

            $queryProvidersAgentLines = ProviderAgentLine::with(['user'])->get();

            if (sizeof($queryProvidersAgentLines) < 0) return response()->json([
                'msg' => 'Empty Query Providers Response',
                'status' => 204
            ], 204);

            $providersAgentLines = [];

            foreach ($queryProvidersAgentLines as $line) {
                $providersAgentLines[] = [
                    'xendoraProviderAgentLineId' => $line->id,
                    'xendoraProviderAgentLineName' => $line->nombre,
                    'xendoraProviderAgentCreateDate' => Carbon::parse($line->created_at)->format('Y-m-d H:m:s'),
                    'xendoraProviderAgentCreator' => $line->user,
                    '_providerAgentLineStatus' => $line->estado,
                ];
            }

            if (
                sizeof($providersAgentLines) < 0
            ) return response()->json([
                'msg' => 'Empty Providers Array',
                'status' => 204
            ], 204);

            return response()->json([
                'msg' => 'Ok',
                'count' => count($providersAgentLines),
                'status' => 200,
                'data' => $providersAgentLines
            ], 200);

            //return $providersTypes;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // FUNCTION TO SAVE THE PROVIDERS INFO TO XENDORA DB
    public function savePharmProviderInfo(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'msg' => 'Bad Request',
            'status' => 400
        ], 400);

        //return $request->all();
        try {

            $this->validate(
                $request,
                [
                    'nit' => 'required',
                    'razon_social' => 'required',
                    'direccion' => 'max:100',
                    'provider_type_id' => 'required'
                ],
                [
                    'nit.required' => 'Por favor Ingrese el Nit',
                    'razon_social.required' => 'Por favor Ingrese la Razon Social',
                    'direccion.max' => 'Este campo no puede ser mayor a 100',
                    'provider_type_id.required' => 'Seleccione el Tipo de Proveedor'
                ]
            );

            $queryIfExist = Provider::where('nit', $request->nit)->first();

            //return $queryIfExist;

            if ($queryIfExist) return response()->json([
                'msg' => 'Ya hay un proveedor registrado con este Nit',
                'status' => 400
            ], 400);

            $provider = new Provider();
            $provider->nit = trim($request->nit);
            $provider->razon_social = $this->replaceCharacter($request->razon_social);
            $provider->direccion = trim($request->direccion);
            $provider->ciudad = trim($request->ciudad);
            $provider->provider_type_id = $request->provider_type_id;
            $provider->user_id = auth()->id();

            if (!$provider->save()) return response()->json([
                'msg' => 'Hubo un error al Registrar el Proveedor',
                'status' => 400
            ], 400);

            return response()->json([
                'msg' => 'Proveedor Registrado Correctamente',
                'status' => 201
            ], 201);

            //
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // FUNCTION TO SAVE THE PROVIDERS AGENTS INFO TO XENDORA DB
    public function savePharmProvidersAgentsInfo(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'msg' => 'Bad Request',
            'status' => 400
        ], 400);

        try {

            $this->validate(
                $request,
                [
                    'nombres' => 'required|max:50',
                    'apellidos' => 'required|max:50',
                    'telefono' => 'required|numeric',
                    'email' => 'required|email',
                ],
                [
                    'nombres.required' => 'Por favor Ingrese el Nombre',
                    'apellidos.required' => 'Por favor Ingrese el Apellido',
                    'telefono.required' => 'Por favor Ingrese el Teléfono',
                    'email.required' => 'Por favor Ingrese el Email'
                ]
            );

            $queryIfAgentExist = ProviderAgent::where('email', $request->email)
                ->orWhere('telefono', $request->telefono)
                ->first();

            if ($queryIfAgentExist) return response()->json([
                'msg' => 'Ya hay un Agente Para este proveedor registrado con estos datos',
                'status' => 400
            ], 400);

            $agent = ProviderAgent::create([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'user_id' => auth()->id(),
            ]);
            /* $agent->nombres = $request->nombres;
            $agent->apellidos = $request->apellidos;
            $agent->telefono = $request->telefono;
            $agent->email = $request->email;
            $agent->user_id = auth()->id(); */

            if (!$agent) return response()->json([
                'msg' => 'Hubo un error al Registrar el Agente',
                'status' => 400
            ], 400);

            $queryIfProviderExist = Provider::find($request->provider_id);

            if (!$queryIfProviderExist) return response()->json([
                'msg' => 'No fue posible encontrar un proveedor con este Id',
                'status' => 400
            ], 400);

            //$agent->providers()->attach($queryIfAgentExist);
            //return $queryIfProviderExist;
            $queryIfProviderExist->provider_agents()->attach($agent);

            return response()->json([
                'msg' => 'Agente Registrado Correctamente',
                'status' => 201
            ], 201);

            //return $queryIfAgentExist;

            //
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // FUNCTION TO DISABLE ANY AGENT FROM PROVIDER IN XENDORA DB
    public function disablePharmProviderAgent(Request $request, $providerAgent = '')
    {
        # code...
    }

    // ============================================================
    // Function to place Special Characters
    // ============================================================

    public function replaceCharacter($text)
    {
        if (!$text) {
            return false;
        }
        return str_replace(array("\r", "\n", "***", "**"), '', $text);
    }
}
