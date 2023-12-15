<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//llamamos a nuestro controlador (lo ultimo es el nombre del controlador)
use App\Http\Controllers\authController;
//importo la pagina
use App\Http\Controllers\ConsultaController;
//importamos la ruta
use  App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//vamos a generar la ruta

Route::group(['middleware' => ['cors']], function () {
    //Rutas a las que se permitirá acceso

    
Route::controller(UserController::Class)->group(function(){
    // momentos: get,post,put,delete
    //nombre de la ruta ('/register')--nombre de la funcion('register')
        Route::post('/register','register');
    //nombre de la ruta ('/login')--nombre de la funcion('login')
        Route::post('/login','login');    
        
    });
    
    
    // Aqui es donde vamos a validar que ya exista un token de login, la ruta es la del auth controler donde esta el servicio
    Route::middleware('auth:sanctum')->delete('/logout', [UserController::class, 'logout']);
    
    // generamos la ruta del UserController
    //la primera ruta puede ser como yo quiera 
    Route::get('/users/show/{id}', [UserController::class,'showById']);
    
    
    //Route::get('/updateRandom/{id}', [UserController::class,'updateRandomPassword']);
    Route::put('/updateManualPassword/{id}/{newPassword}', [UserController::class,'updateManualPassword']);
    
    Route::put('/updateRandom/{email}', [UserController::class,'updateRandomPassword']);
    //Route::put('/updateRandom', [UserController::class,'updateRandomPassword']);
    
    
    
    //metodos de leafar para BeHappy
    //metodo get
    Route::get('/guardar/{id}',[ConsultaController::class,'guardar']);
    Route::get('/importes/{id}/{id2}',[ConsultaController::class,'ImporteTicket']);
    
    //ARTICULOS
    Route::post('/insertArticulo', [authController::class, 'insertArticulo']);
    
    //CLientes
    
    Route::get('/ConsultaClientes/',[ConsultaController::class,'ConsultaClientes']);
    Route::get('/ConsultaClienteWhere/{idCliente}',[ConsultaController::class,'ConsultaClienteWhere']);
    Route::post('/insertCliente', [authController::class, 'insertClientes']);
    Route::get('/ConsultaClientesAbono/',[ConsultaController::class,'ConsultaClientesAbono']);
    Route::put('/Abono/{idCliente}/{NuevoSaldo}', [authController::class,'Abono']);
    Route::put('/ModificarCliente/{idArticulo}/{nombre}/{apellido}/{telefono}/{domicilio}/{diaCobro}/{saldo}/{ruta}', [authController::class,'ModificarCliente']);
    Route::get('/ConsultaDiaCobroClientes/',[ConsultaController::class,'ConsultaDiaCobroClientes']);
    Route::get('/ConsultaRutasClientes/',[ConsultaController::class,'ConsultaRutasClientes']);
    Route::get('/ConsultaClientesFlitros/{accion}/{dato}',[ConsultaController::class,'ConsultaClientesFlitros']);
    
    Route::get('/loginClientes/{apellido}/{telefono}',[UserController::class,'loginClientes']);
    
    // Articulos
    Route::get('/ConsultaArticulos/',[ConsultaController::class,'ConsultaArticulos']);
    Route::get('/ConsultaArticulosWhere/{idArticulo}',[ConsultaController::class,'ConsultaArticulosWhere']);
    Route::put('/ModificarArticulo/{idArticulo}/{descripcion}/{codigo_barras}/{precio}/{existencias}/{nombre}/{apellido}/{empresa}', [authController::class,'ModificarArticulo']);
    Route::get('/ConsultaCategorias/',[ConsultaController::class,'ConsultaCategorias']);
    Route::get('/ConsultaArticulosCategorias/{categoria}',[ConsultaController::class,'ConsultaArticulosCategorias']);
    Route::get('/ConsultaArticulosProveedor/{proveedor}',[ConsultaController::class,'ConsultaArticulosProveedor']);
    
    //PROVEEDORES
    Route::get('/ConsultaProveedores/',[ConsultaController::class,'ConsultaProveedores']);
    
    
    //TICKETS
    Route::post('/insert-ticket/{vendedor}/{cliente}', [authController::class, 'insertTicket']);
    Route::get('/Ticket/{idTicket}',[ConsultaController::class,'Ticket']);
    
    // DETALLES_TICKETS
    Route::post('/insertDetalleTicket/{cantidad}/{ARTICULOS_id}/{TICKETS_id}', [authController::class, 'insertDetalleTicket']);
    Route::get('/detallesTicket/{idTicket}',[ConsultaController::class,'detallesTicket']);
    Route::delete('/deleteDetalleTicket/{id}',[authController::class,'deleteDetalleTicket']);
    Route::get('/detallesTicketWhatssapp/{idTicket}',[ConsultaController::class,'detallesTicketWhatssapp']);
    Route::delete('/deleteTodosDetalleTicket/{id}',[authController::class,'deleteTodosDetalleTicket']);
    
    // abono a cuenta
    Route::get('/consultaDetallesTicket/{idTicket}',[ConsultaController::class,'consultaDetallesTicket']);
    Route::get('/consultaImporteTicket/{idTicket}',[ConsultaController::class,'consultaImporteTicket']);
    
    
    //TICKETS
    Route::get('/totalPagar/{idTicket}',[ConsultaController::class,'totalPagar']);
    Route::delete('/deleteTicket/{id}',[authController::class,'deleteTicket']);
    
    //FORMASPAGO
    Route::get('/formasPago/',[ConsultaController::class,'formas_pago']);
    
    //PAGOS
    Route::post('/insertPago/{FORMAS_PAGO_id}/{TICKETS_id}/{importe}', [authController::class, 'insertPago']);
    Route::delete('/deletePagos/{id}',[authController::class,'deletePagos']);
    Route::put('/actualizarPagosAbono/{idPago}/{nuevoImporte}', [authController::class,'actualizarPagosAbono']);
    
    //abono cuenta
    Route::get('/consultaPagosMenorTotalPagarDeTicket/{idCliente}',[ConsultaController::class,'consultaPagosMenorTotalPagarDeTicket']);
    Route::get('/consultaTodosPagosCliente/{idCliente}',[ConsultaController::class,'consultaTodosPagosCliente']);
    
    //CLIENTES
    Route::delete('/deleteClientes/{idCliente}',[authController::class,'deleteClientes']);
    Route::get('/telefonoCliente/{idTicket}',[ConsultaController::class,'telefonoCliente']);
    
    //CATEGORIAS
   // Route::get('/categorias/',[ConsultaController::class,'categorias']);
    
    //HISTORIAL
    Route::get('/historial/',[ConsultaController::class,'historial']);
    
    //DEVOLUCIONES
    Route::post('/devoluciones/{TICKET_id}/{DETALLES_TICKETS_id}', [authController::class, 'devoluciones']);
    
    Route::post('/crearImg/{USER_id}', [ConsultaController::class, 'crearImg']);

    Route::get('/getImg/{USER_id}', [ConsultaController::class, 'getImg']);
    
});