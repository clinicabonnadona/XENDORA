<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::middleware('auth:api')->get('/user', 'Api\v1\Admin\UserController@AuthRouteAPI');

Route::middleware('auth')->group(function () {
    Route::group([
        'prefix' => 'v1/admin',
        'as' => 'api.v1.admin.',
        'namespace' => 'Api\v1\Admin',
    ], function () {

        Route::get('users', 'UserController@index');
        Route::post('users', 'UserController@store');
        Route::post('users/update', 'UserController@update');
        Route::post('users/delete', 'UserController@destroy');

        Route::get('permissions', 'PermissionController@index');
        Route::post('permissions', 'PermissionController@store');
        Route::post('permissions/update', 'PermissionController@update');
        Route::post('permissions/delete', 'PermissionController@destroy');

        Route::get('roles', 'RoleController@index');
        Route::post('roles', 'RoleController@store');
        Route::post('roles/edit', 'RoleController@edit');
        Route::post('roles/delete', 'RoleController@destroy');
        Route::post('role/delete/permission', 'RoleController@revokePermissionToRole');

        Route::get('doctypes', 'DoctypeController@index');

        Route::get('rotacion/{id}', 'GeneralController@getRotacionInfo');
        Route::get('suministros/rotacion/{sumcod}', 'GeneralController@getRotacion');
        Route::get('suministros/rotacion/meses/{sumcod}', 'GeneralController@getRotacionMeses');



        /** Rotation Initial Method To List All Products With Balance */
        Route::get('suministros/rotacion', 'GeneralController@getSumToRotacion');

        Route::get('suministros/rotacion/meses/msreso/{sumcod?}', 'GeneralController@getRotacionMesesComplete');
        Route::get('suministros/rotacion/entradas/{sumcod}', 'GeneralController@getSumEntradas');
        Route::get('suministros/rotacion/despachos/{sumcod}', 'GeneralController@getSumDespachos');

        /** */
        Route::get('suministros/rotacion/invoice-details/med/{sumcod}/month/{mes}/year/{anio}', 'GeneralController@getInvoiceDetails');
        Route::get('suministros/rotacion/despachos/super-alto-costo-two/{sumcod?}', 'DespachosController@getSumDispatchSuperAcTwo');
        /*  Route::get('suministros/rotacion/despachos/super-alto-costo-two-v/{sumcod?}', 'DespachosController@monthsReturns');
        Route::get('suministros/rotacion/despachos/super-alto-costo-two-v2/{sumcod?}', 'DespachosController@getSumDispatchSuperAcTwo'); */
        Route::get('suministros/rotacion/despachos/super-alto-costo-two-w/{sumcod?}', 'DespachosController@countingByGroupArryData');

        //
        Route::get('terceros', 'TercerosController@getTerceros');
        Route::get('terceros/tdocs', 'TercerosController@getTDocs');
        Route::post('terceros/savetercero', 'TercerosController@saveTercero');
        Route::post('terceros/delete', 'TercerosController@destroyTercero');
        Route::post('terceros/updatetercero', 'TercerosController@updateTercero');


        Route::get('suministros', 'SuministrosController@getSuministros');
        Route::get('suministros/getformaf', 'SuministrosController@getFormaFarmaceutica');
        Route::get('suministros/getformapre', 'SuministrosController@getFormaPresentacion');
        Route::post('suministros/savesuministro', 'SuministrosController@saveSuministro');
        Route::post('suministros/updatesuministro', 'SuministrosController@updateSuministro');
        Route::post('suministros/delete', 'SuministrosController@destroySuministro');
        Route::get('suministros/validate/exists/suministro/{sumcod}', 'SuministrosController@validateIfExistsSumCod'); //PARA VALIDAR CODIGO SUM SI EXISTE

    });

    Route::prefix('v1')->group(function () {

        Route::get('reportes/get-pre-alta', 'ReportesController@getPrealtaData');

        Route::get('/report1getdata/{fecini}/fin/{fecfin}', 'ReportesController@reporteNomina');

        Route::get('/report2getdata/{fec}', 'ReportesController@reporteConceptosDuplicados');

        Route::get('/report3getdata/{fecini?}/fin/{fecfin?}/{document?}', 'ReportesController@reporteHuellero');

        Route::get('reportes/get-cumpleanios/{mes?}', 'ReportesController@reporteCumpleanios');

        Route::get('reportes/permisos-aprobados/{fecini}/fin/{fecfin}', 'ReportesController@reportePermisosAprobados');

        Route::get('reportes/get-censo-real', 'ReportesController@getCensoReal');

        Route::get('reportes/get-auditoria-med-amb/{doc_pac}', 'ReportesController@getAuditoriaMedAmb');

        Route::get('reportes/get-marcaciones-egresos/{pcte_doc}/paciente', 'ReportesController@getMarcacionesEgresos');

        /** reportes calidad */
        Route::get('reportes/get/auditoria/{fechaini?}/{fechafin?}', 'ReportesController@reporteCalidadAuditorias');
        Route::get('reportes/get/comites/{fechaini?}/{fechafin?}', 'ReportesController@reporteCalidadComites');

        /* HANDLE FARMACIA REPORTS ROUTES */
        // Glucometrias
        Route::get('/reportes/get-gluco/{initdate}', 'Api\v1\User\GlucometriasController@getActivoGlucometries');

        // Devoluciones
        Route::get('/reportes/get-returns/{initdate}', 'Api\v1\User\DevolucionesController@getReturns');
        // Devoluciones TOTAL
        Route::get('/reportes/get-total-returns/{docpac?}', 'Api\v1\User\DevolucionesController@getTotalReturns');
        // Seg. Evoluciones Fact
        Route::get('/reportes/seg-evolutions-fact/{initdate?}/to/{enddate?}', 'Api\v1\User\SeguimientoEvolucionesFact@getSegEvolutionsFact');

        /* HANDLE FARMACIA ROUTES */
        Route::get('farmacia/get/active-orders/{initdate}/to/{enddate}', 'Api\v1\User\RecepcionFarmaciaController@getActiveOrder');
        Route::post('farmacia/post/active-order', 'Api\v1\User\RecepcionFarmaciaController@saveOrder');
        Route::post('farmacia/put/active-order/{id}', 'Api\v1\User\RecepcionFarmaciaController@editingActiveOrder');
        Route::get('farmacia/get/received-orders/{initdate}/to/{enddate}', 'Api\v1\User\RecepcionFarmaciaController@getReceivedOrdersReport');
        Route::get('farmacia/get/active-orders-to-shipping/{initDate?}/to/{endDate?}', 'Api\v1\User\ShippingPurchaseOrdersController@getToShippingPurchaseOrders');
        Route::get('farmacia/get/active-orders-to-shipping-report/{initDate?}/to/{endDate?}', 'Api\v1\User\ShippingPurchaseOrdersController@getToShippingPurchaseOrdersReport');
        Route::post('farmacia/post/shipping-orders', 'Api\v1\User\ShippingPurchaseOrdersController@saveShippingPurchaseOrders');
        Route::post('farmacia/update/shipping-orders', 'Api\v1\User\ShippingPurchaseOrdersController@updatedShippingPurchaseOrders');

        /* HANDLE USER ORIENTATION ROUTES */
        Route::get('orientacionusuario/get/censo', 'Api\v1\User\UserOrientationController@getCenso');
        Route::get('orientacionusuario/get/patient-visitors/{patientdoc?}/{patientdoctype?}/{patientadmctve?}', 'Api\v1\User\UserOrientationController@getPatientVisitors');
        Route::post('orientacionusuario/post/patient-visitor', 'Api\v1\User\UserOrientationController@savePatientVisitors');

        /* HANDLE COMPRAS SEGUIMIENTOS */
        Route::get('compras/get/inventory/{initdate?}/{enddate?}', 'Api\v1\User\ComprasController@getComprasSeguimientos');

        /** SUPER DESPACHOS ALTO COSTO */
        Route::get('suministros/rotacion/despachos/super-alto-costo-two-v/{sumcod?}', 'Api\v1\Admin\DespachosController@getSumDispatchSuperAcTwo');

        /** PROVIDERS EVALUATIONS ROUTES */
        Route::get('provider-evaluation/get/all-providers/from-hosvital', 'Api\v1\User\ProviderEvaluationController@getPharmProvidersFromHosvital');
        Route::get('provider-evaluation/get/all-providers/from-xendora', 'Api\v1\User\ProviderEvaluationController@getPharmProvidersFromXendora');
        Route::get('provider-evaluation/get/providers-types/from-xendora', 'Api\v1\User\ProviderEvaluationController@getPharmProvidersTypesFromXendora');
        Route::post('provider-evaluation/post/provider-to-evaluate', 'Api\v1\User\ProviderEvaluationController@savePharmProviderInfo');

        Route::get('provider-evaluation/get/all-agents/from-xendora/{provider?}', 'Api\v1\User\ProviderEvaluationController@getPharmProvidersAgentFromXendora');
        Route::post('provider-evaluation/post/provider-agent/to-xendora', 'Api\v1\User\ProviderEvaluationController@savePharmProvidersAgentsInfo');
        //
        Route::get('provider-evaluation/get/all-agents-lines/from-xendora', 'Api\v1\User\ProviderEvaluationController@getPharmProvidersAgentLinesFromXendora');
    });
});
