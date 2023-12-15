<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use App\Models\Imagen;

use Illuminate\Support\Facades\Storage;

class ConsultaController extends Controller
{
    public function guardar($id)
    {
        $descripcion = DB::table('articulos')
        ->select('descripcion')
        ->where('id', $id)
        ->get();
        return response()->json([
            'Descripcion'=> $descripcion
        ],200);

    }

    /*public function ImporteTicket($TICKETS_id,$FORMAS_PAGO_id)
    {
        $descripcion = DB::table('pagos')
        ->select('importe, FORMAS_PAGO_id ')
        ->where('TICKETS_id', '=',$TICKETS_id )
        ->where('FORMAS_PAGO_id', '=',$FORMAS_PAGO_id )
        ->get();
        return response()->json([
            'Descripcion'=> $descripcion
        ],200);

    }*/

    public function ImporteTicket($idTicket,$idFormaPago)
    {
        $descripcion = DB::table('pagos')
        ->select('importe','FORMAS_PAGO_id')
        //->join('Formas_pago', 'Formas_pago.id', '=', 'pagos.FORMAS_PAGO_id')
        ->where('TICKETS_id', $idTicket)
        ->where('FORMAS_PAGO_id', $idFormaPago )
        ->get();
        return response()->json([
            'Descripcion'=> $descripcion
        ],200);
    }

    public function ConsultaClientes()
    {
        $clientes = DB::table('clientes')
        ->select('id','nombre','apellido','telefono','domicilio','diaCobro','saldo')
        ->get();
        return response()->json([
            'CLIENTES'=> $clientes
        ],200);
    }

    public function ConsultaArticulos()
    {
        $articulos = DB::table('articulos')
        ->select('id','descripcion','precio','existencias')
        ->get();
        return response()->json([
            'ARTICULOS'=> $articulos
        ],200);
    }

    public function totalPagar($idTicket)
    {
        $totalPagar = DB::table('tickets')
        ->select('total_pagar')
        ->where('id', $idTicket)
        ->get();
        return response()->json([
            'TotalPagar'=> $totalPagar
        ],200);
    }

    public function detallesTicket($idTicket)
    {
        $detalles = DB::table('detalles_tickets')
            ->select('detalles_tickets.id', 'articulos.descripcion', 'detalles_tickets.cantidad', 'detalles_tickets.importe')
            ->join('articulos', 'articulos.id', '=', 'detalles_tickets.ARTICULOS_id')
            ->where('detalles_tickets.TICKETS_id', $idTicket)
            ->get();
            return response()->json([
                'DETALLES'=> $detalles
            ],200);
    }

    public function detallesTicketWhatssapp($idTicket)
    {
        
    $details = DB::table('articulos as a')
                    ->select('a.descripcion', 'a.precio', 'dt.cantidad', 'dt.importe')
                    ->join('detalles_tickets as dt', 'a.id', '=', 'dt.ARTICULOS_id')
                    ->join('tickets as t', 't.id', '=', 'dt.TICKETS_id')
                    ->where('dt.TICKETS_id', $idTicket)
                    ->get();

    $diaCobro = DB::table('tickets as t')
                    ->select('c.diaCobro')
                    ->join('clientes as c', 'c.id', '=', 't.CLIENTES_id')
                    ->where('t.id', $idTicket)
                    ->get();

    return response()->json([
                        'DETALLES'=> $details,
                        'DIACOBRO'=>$diaCobro
                    ],200);

    }

    public function formas_pago()
    {
        $FormasPago = DB::table('formas_pago')
        ->select('id','descripcion')
        ->get();
        return response()->json([
            'FormasPago'=> $FormasPago
        ],200);
    }

    public function telefonoCliente($idTicket)
    {

        $telefono = DB::table('detalles_tickets')
        ->join('tickets', 'detalles_tickets.TICKETS_id', '=', 'tickets.id')
        ->join('clientes', 'clientes.id', '=', 'tickets.CLIENTES_id')
        ->select('clientes.telefono')
        ->where('tickets.id', '=', $idTicket)
        ->get();
        
    return response()->json([
                        'TELEFONO'=> $telefono
                    ],200);

    }

    public function categorias()
    {
        $categorias = DB::table('categorias')
        ->select('id','descripcion')
        ->get();
        return response()->json([
            'CATEGORIAS'=> $categorias
        ],200);
    }

    public function ConsultaClientesAbono()
    {
        $clientes = DB::table('clientes')
        ->select('id','nombre','apellido','telefono','saldo')
        ->get();
        return response()->json([
            'CLIENTES'=> $clientes
        ],200);
    }
    
    public function Ticket($idTicket)
    {
        $ticket = DB::table('tickets')
            ->select('id')
            ->where('id', '=', $idTicket)
            ->get();

        if ($ticket=="[]") {
            return response()->json(['message' => 'Ticket no encontrado'], 404);
        }

        else
        {
        return response()->json([
            'message' => 'Ticket encontrado correctamente',
        ], 200);
        }
    }

    public function ConsultaArticulosWhere($idArticulo)
    {
        $articulo = DB::table('articulos')
        ->select('id','descripcion','codigo_barras','precio','existencias', 'PROVEEDOR_nombre', 'PROVEEDOR_apellidos', 'PROVEEDOR_empresa')
        ->where('id', '=', $idArticulo)
        ->get();
        return response()->json([
            'ARTICULO'=> $articulo
        ],200);
    }

    public function ConsultaClienteWhere($idCliente)
    {
        $cliente = DB::table('clientes')
        ->select('id','nombre','apellido','telefono','domicilio','diaCobro','saldo','ruta')
        ->where('id', '=', $idCliente)
        ->get();
        return response()->json([
            'CLIENTE'=> $cliente
        ],200);
    }

    public function historial()
    {
        $historial = DB::table('historial')
        ->select('fecha','hora','accion')
        ->get();
        return response()->json([
            'HISTORIAL'=> $historial
        ],200);
    }

    public function ConsultaDiaCobroClientes()
    {
        $dias = DB::table('clientes')
            ->select('diaCobro')
            ->groupBy('diaCobro')
            ->orderBy('diaCobro', 'asc')
            ->get();
        
        return response()->json([
            'DIAS' => $dias
        ], 200);
    }

    public function ConsultaRutasClientes()
    {
        $rutas = DB::table('clientes')
            ->select('ruta')
            ->groupBy('ruta')
            ->orderBy('ruta', 'asc')
            ->get();
        
        return response()->json([
            'RUTAS' => $rutas
        ], 200);
    }

    public function ConsultaClientesFlitros($accion,$dato)
    {
        $clientes = DB::table('clientes')
        ->select('id','nombre','apellido','telefono','domicilio','diaCobro','saldo','ruta')
        ->where($accion, '=', $dato)
        ->get();
        return response()->json([
            'CLIENTES'=> $clientes
        ],200);
    }

    public function ConsultaCategorias()
    {
        $categorias = DB::table('categorias')
            ->select('descripcion')
            ->groupBy('descripcion')
            ->orderBy('descripcion', 'asc')
            ->get();
        
        return response()->json([
            'CATEGORIAS' => $categorias
        ], 200);
    }

    
    public function ConsultaProveedores()
        {
            $proveedores = DB::table('proveedores')
                ->select('empresa')
                ->groupBy('empresa')
                ->orderBy('empresa', 'asc')
                ->get();

            return response()->json([
                'PROVEEDORES' => $proveedores
            ], 200);
        }

        public function ConsultaArticulosCategorias($categoria)
        {
            $articulos = DB::table('articulos')
                ->select('articulos.id', 'articulos.descripcion', 'articulos.codigo_barras', 'articulos.precio', 'articulos.existencias', 'articulos.PROVEEDOR_nombre', 'articulos.PROVEEDOR_apellidos', 'articulos.PROVEEDOR_empresa')
                ->join('categorias', 'articulos.CATEGORIAS_id', '=', 'categorias.id')
                ->where('categorias.descripcion', $categoria)
                ->get();
            
            return response()->json([
                'ARTICULOS_CATEGORIAS'=> $articulos
            ],200);
        }

        public function ConsultaArticulosProveedor($proveedor)
        {
            $articulos = DB::table('articulos')
                ->select('articulos.id', 'articulos.descripcion', 'articulos.codigo_barras', 'articulos.precio', 'articulos.existencias', 'articulos.PROVEEDOR_nombre', 'articulos.PROVEEDOR_apellidos', 'articulos.PROVEEDOR_empresa')
                ->join('proveedores', 'articulos.PROVEEDOR_id', '=', 'proveedores.id')
                ->where('proveedores.empresa', $proveedor)
                ->get();
            
            return response()->json([
                'ARTICULOS_PROVEEDORES'=> $articulos
            ],200);
        }


        //segmentado
        public function consultaPagosMenorTotalPagarDeTicket($idCliente)
        {
            $pagos = DB::table('pagos')
                ->select('pagos.id', 'pagos.TICKETS_id', 'pagos.importe', 'tickets.total_pagar', 'tickets.fecha_hora')
                ->join('tickets', 'pagos.TICKETS_id', '=', 'tickets.id')
                ->whereRaw('pagos.importe < tickets.total_pagar')
                ->where('tickets.CLIENTES_id', $idCliente)
                ->orderBy('tickets.fecha_hora', 'asc')
                //->limit(1)
                ->get();

            return response()->json([
                'PAGOS' => $pagos
            ], 200);
        }

            public function consultaDetallesTicket($idTicket)
            {
                $detalles = DB::table('detalles_tickets')
                    ->select('detalles_tickets.id', 'articulos.descripcion', 'detalles_tickets.cantidad', 'detalles_tickets.precio','detalles_tickets.importe')
                    ->join('articulos', 'detalles_tickets.ARTICULOS_id', '=', 'articulos.id')
                    ->where('detalles_tickets.TICKETS_id', $idTicket)
                    ->get();
    
                return response()->json([
                    'DETALLES' => $detalles
                ], 200);
            }

            public function consultaImporteTicket($idTicket)
            {
                $importe = DB::table('detalles_tickets')
                    ->select(DB::raw('SUM(detalles_tickets.importe) AS importe_total'))
                    ->where('detalles_tickets.TICKETS_id', $idTicket)
                    ->get();

                return response()->json([
                    'IMPORTE' => $importe
                ], 200);
            }


            public function consultaTodosPagosCliente($idCliente)
        {
            $pagos = DB::table('pagos')
                ->select('pagos.id', 'pagos.TICKETS_id', 'pagos.importe', 'tickets.total_pagar', 'tickets.fecha_hora')
                ->join('tickets', 'pagos.TICKETS_id', '=', 'tickets.id')
                ->where('tickets.CLIENTES_id', $idCliente)
                ->orderBy('tickets.fecha_hora', 'asc')
                //->limit(1)
                ->get();

            return response()->json([
                'PAGOS' => $pagos
            ], 200);
        }

        public function crearImg(Request $request, $USER_id)
        {
            
            /*$request->selectedImage
    
            if ($request->imagen["base64String"] !=null) {
                $imgUrl = $request->file('imagen');
                $imagen = base64_encode($request->imagen["base64String"]);
    */
                
        
                DB::table('imagenes')->insert([
                    'USER_id' => $USER_id,
                    'imagen' => $request->selectedImage,
                ]);
        
                return response()->json([
                    'message' => $request->selectedImage,
                ]);
    
            // } else {
            // 	return response()->json([
            // 		'message' => 'No se ha proporcionado una imagen válida',
            // 	], 400);
            // }
        }
    
    
    
    
        //GET DE IMG
        public function getImg($USER_id)
        {
            $imagenes = DB::table('imagenes')
            ->select('imagenes.id','imagenes.USER_id', 'imagenes.imagen')
            ->where('imagenes.USER_id', $USER_id)
            ->get();
            
    
        return response()->json
            ([
                'IMAGENES' => $imagenes
            ], 200);
        }
        
}

