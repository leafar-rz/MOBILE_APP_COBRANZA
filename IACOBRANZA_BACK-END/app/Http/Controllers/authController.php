<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// insertamos las librerias que usaremos
use Illuminate\Support\Facades\Hash;
use App\Models\User;
//laravel nos da una libreria con el metodo auth para facilitarnos la vida en la autenticacion
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{//para asignrar una variable en php se usa el $
    

    public function insertTicket($VENDEDOR_id, $CLIENTE_id)
    {
        $new_id = DB::table('tickets')->insertGetId([
            'VENDEDORES_id' => $VENDEDOR_id,
            'CLIENTES_id' => $CLIENTE_id,
        ]);
        
        return response()->json([
            'message' => 'Ticket insertado correctamente',
            'new_id' => $new_id,
        ]);
    }

    

    public function insertTicket2($VENDEDOR_id, $CLIENTE_id)
    {
        $transaction = DB::beginTransaction(true); // Inicia la transacción y guarda su identificador

        try {
            $new_id = DB::table('tickets')->insertGetId([
                'VENDEDORES_id' => $VENDEDOR_id,
                'CLIENTES_id' => $CLIENTE_id,
            ]);

           // $this->otraOperacion($new_id); // Realiza otra operación en la base de datos

            return response()->json([
                'message' => 'Ticket insertado correctamente',
                'new_id' => $new_id,
            ]);
            DB::commit(); // Confirma la transacción
            
        } catch (\Throwable $e) {
            DB::rollBack($transaction); // Deshace la transacción en caso de error
            return response()->json([
                'message' => 'Error al insertar el ticket: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function confirmarTransaccion($transaction)
    {
        DB::commit($transaction); // Confirma la transacción utilizando el identificador
    }

    public function insertDetalleTicket($cantidad,$ARTICULOS_id,$TICKETS_id)
    {
        DB::table('detalles_tickets')->insert([
            'cantidad' => $cantidad,
            'ARTICULOS_id' => $ARTICULOS_id,
            'TICKETS_id' => $TICKETS_id,
        ]);
        
        return response()->json([
            'message' => 'Detalles de Ticket insertado correctamente',
        ]);
    }

    public function deleteDetalleTicket($idDetalle)
    {
        DB::table('detalles_tickets')
        ->where('id', $idDetalle)
        ->delete();
        return response()->json(['message' => 'Detalle eliminado correctamente'], 200);
    }

    public function insertPago($FORMA_PAGO_id, $TICKETS_id, $IMPORTE)
{
    DB::table('pagos')->insert([
        'FORMAS_PAGO_id' => $FORMA_PAGO_id,
        'TICKETS_id' => $TICKETS_id,
        'importe' => $IMPORTE,
    ]);
    
    return response()->json([
        'message' => 'Pago realizado correctamente',
    ]);
}

    public function deleteClientes($idCliente)
    {
        DB::table('clientes')
        ->where('id', $idCliente)
        ->delete();
        return response()->json(['message' => 'Cliente eliminado correctamente'], 200);
    }

    public function deleteTicket($idTicket)
    {
        DB::table('tickets')
        ->where('id', $idTicket)
        ->delete();
        return response()->json(['message' => 'Ticket eliminado correctamente'], 200);
    }

    public function deleteTodosDetalleTicket($idTicket)
    {
        DB::table('detalles_tickets')
        ->where('TICKETS_id', $idTicket)
        ->delete();
        return response()->json(['message' => 'Detalles eliminados correctamente'], 200);
    }

    public function deletePagos($idTicket)
    {
        DB::table('pagos')
        ->where('TICKETS_id', $idTicket)
        ->delete();
        return response()->json(['message' => 'Pago eliminado correctamente'], 200);
    }

    public function insertArticulo(Request $request)
    {
        //obtener todos los datos del request en un json
        $data = $request->json()->all();
        
        DB::table('articulos')->insert([
            'descripcion' =>$data['descripcion'],
            'codigo_barras' =>$data['codigo_barras'],
            'precio' =>$data['precio'],
            'existencias' =>$data['existencias'],
            'CATEGORIAS_id' =>$data['CATEGORIAS_id']
        ]);
        
        return response()->json([
            'message' => 'Articulo insertado correctamente',
        ]);
    }

    public function insertClientes(Request $request)
    {
        //obtener todos los datos del request en un json
        $data = $request->json()->all();
        
        DB::table('clientes')->insert([
            'nombre' =>$data['nombre'],
            'apellido' =>$data['apellido'],
            'telefono' =>$data['telefono'],
            'domicilio' =>$data['domicilio'],
            'diaCobro' =>$data['diaCobro']
        ]);
        
        return response()->json([
            'message' => 'Cliente agregado correctamente',
        ]);
    }

    public function Abono($idCliente,$NuevoSaldo)
    {
    
        $cliente = DB::table('clientes')->where('id', $idCliente)->update(['saldo' => $NuevoSaldo]);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
    
        // Enviar respuesta un mensaje, la nueva contraseña y el usuario al que se le hizo el cambio
        return response()->json([
            'message' => 'Saldo actualizado correctamente',            
        ], 200);
    }      

    public function devoluciones($TICKETS_id,$DETALLES_TICKETS_id)
    {
        
        DB::table('devoluciones')->insert([
            'TICKETS_id' =>$TICKETS_id,
            'DETALLES_TICKETS_id' =>$DETALLES_TICKETS_id
        ]);
        
        return response()->json([
            'message' => 'Devolucion realizada correctamente',
        ]);
    }

    public function ModificarArticulo($idArticulo, $descripcion, $codigo_barras, $precio, $existencias,$nombre,$apellidos,$empresa)
{
    $articulo = DB::table('articulos')
        ->where('id', $idArticulo)
        ->update([
            'descripcion' => $descripcion,
            'codigo_barras' => $codigo_barras,
            'precio' => $precio,
            'existencias' => $existencias,
            'PROVEEDOR_nombre' =>$nombre,
            'PROVEEDOR_apellidos' =>$apellidos,
            'PROVEEDOR_empresa' =>$empresa,
        ]);

    if (!$articulo) {
        return response()->json(['message' => 'Articulo no encontrado'], 404);
    }

    // Enviar respuesta un mensaje, la nueva contraseña y el usuario al que se le hizo el cambio
    return response()->json([
        'message' => 'Articulo actualizado correctamente',            
    ], 200);
}


public function ModificarCliente($idCliente,$nombre,$apellido,$telefono,$domicilio,$diaCobro,$saldo,$ruta)
{
    $articulo = DB::table('clientes')
        ->where('id', $idCliente)
        ->update([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'telefono' => $telefono,
            'domicilio' => $domicilio,
            'diaCobro' => $diaCobro,
            'saldo' => $saldo,
            'ruta' => $ruta,
        ]);

    if (!$articulo) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    // Enviar respuesta un mensaje, la nueva contraseña y el usuario al que se le hizo el cambio
    return response()->json([
        'message' => 'Cliente actualizado correctamente',            
    ], 200);
}


        public function actualizarPagosAbono($idPago,$nuevoImporte)
        {
            $pago = DB::table('pagos')->find($idPago);

            if (!$pago) 
            {
                return response()->json([
                    'message' => 'El pago con el ID ' . $idPago . ' no existe.',
                ], 200);
            }
            else
            {
            DB::table('pagos')
            ->where('id', $idPago)
            ->update(['importe' => $nuevoImporte]);
            
            // Enviar respuesta un mensaje, la nueva contraseña y el usuario al que se le hizo el cambio
            return response()->json([
                'message' => 'Importe de pagos actualizado correctamente',
            ], 200);
            }
           
        }
  

    
}

