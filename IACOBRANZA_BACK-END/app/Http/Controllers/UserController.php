<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// insertamos las librerias que usaremos
use Illuminate\Support\Facades\Hash;
use App\Models\User;
//libreria para usar la bd con consultas
use Illuminate\Database\Eloquent\Model;
//ocupamos esta libreria para poder generar una contraseña ramdom de tipo str
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;





class UserController extends Controller
{
   
    
    public function register(Request $request)
    {//obtener todos los datos del request en un json
        $data = $request->json()->all();
        // Comprobar que no este vacion
        $itExistsUserName=User::where('email',$data['email'])->first();

        if ($itExistsUserName==null) {
            $user = User::create(
                [
                    'name'=>$data['name'],
                    'email'=>$data['email'],
                    'password'=>Hash::make($data['password'])

                ]
            );
  //al usuario que creemos tenemos que asignarle un token
            $token = $user->createToken('web')->plainTextToken;


                return response()->json([
                    'data'=>$user,
                    'token'=> $token

                ],200);// tiempo de respuesta, si excede marca un error
        } else {
               return response()->json([
                'data'=>'User already exists!',
                'status'=> false
            ],200);
       }

   }

    //creacion del servicio de login, este metodo recibe un opjeto de tipo request que contiene los datos del input del front
    public function login(Request $request){

        //vamos a ocupar una condicion (laravel tiene un metodo llamado Auth (attemp, regresa un booleano) para autenticas datos, ver si si estan en la bd)
        if(!Auth::attempt($request->only('email','password')))
        {
            //si esto es falso va a retornar en un json
            return response()->json
            ([
                'message'=> 'Correo o contraseña incorrectos',// es lo que nos mostrara en la consola si no coinciden los datos
                'status'=> false
            ],400);//tiempo de espera de 400
        }
         //vamos a buscar al usuario en la bd
         $user = User::where('email',$request['email'])->firstOrFail();
         //tenemos que generarle un token de acceso que cambien en cada login
         $token = $user->createToken('web')->plainTextToken;
    
          //retornamos el data que es el usuario y tambien el token
         return response()->json
         ([
            'data'=> $user,
            'token'=>$token
         ]);
    
       }


    //creacion del servicio de logout, este metodo recibe un opjeto de tipo request que contiene los datos del input del front
   // Para salir tenemos que eliminar el token de log in
   public function logout(Request $request)
   {
    $request->user()->currentAccessToken()->delete();// metodo currentAccessToken() nos optiene el token y el delete lo borra
    // returnamos un valor para ver que hizo
    return response()->json
    ([
        'status'=> true,
    ]);

   }

//metodo para ver al usuario logeado con un servicio get
    public function showById($id)
    {
        //declaramos la variable que nos guarda el id del usuario con ese id
        $user = User::find($id);
        $newPassword = Str::random(6);
        //retornamos el response
        return response()->json(["data"=>$user]);
        //return response()->json(["data"=>$newPassword]);
    }


    public function updateRandomPassword($email)
    {
        //verificamos que el email si le corresponda a un usuario
        $user = User::where('email', $email)->first();

        //Si no encuentra ningun usuario con ese email nos dira que no existe
        if (!$user) 
        {
            return response()->json(['message' => 'El usuario no existe'], 200);
        }
        else
        {
            // Generar una contraseña aleatoria de 6 caracteres
        $newPassword = Str::random(6);
        
        // Actualizar el campo password de la tabla user
        $user->password = Hash::make($newPassword);
        //guarda los cambios en la bd
        $user->save();
        
        // Enviar respuesta un mensaje, la nueva contraseña y el usuario al que se le hizo el cambio
        return response()->json([
            'message' => 'Contraseña actualizada correctamente',
            'new_password' => $newPassword,
            'user' => $user,
            
        ], 200);
        }

        
    }       
    

    public function updateManualPassword($id,$newPassword)
    {
        //verificamos que el id si le corresponda a un usuario
        $user = User::findOrFail($id);
        
        // Actualizar el campo password del usuario
        $user->password = Hash::make($newPassword);
        //guarda los cambios en la bd
        $user->save();
        
         // Enviar respuesta un mensaje, la nueva contraseña y el usuario al que se le hizo el cambio
        return response()->json([
            'message' => 'Contraseña actualizada correctamente',
        ], 200);
    }
      
    //creacion del servicio de login, este metodo recibe un opjeto de tipo request que contiene los datos del input del front
    public function loginClientes($apellido,$telefono){

       
       
         $cliente = DB::table('clientes')
         ->select('id','nombre','apellido','telefono','domicilio','diaCobro','saldo')
         ->where('apellido', '=', $apellido)
         ->where("telefono",$telefono)
         ->get();

         if($cliente!="[]")
         {
            
         return response()->json([
             'CLIENTE'=> $cliente,
             'message' => 'Cliente encontrado en la base de datos',
         ],200);
         
        }
        else
        {
            return response()->json([
                'message' => 'Cliente no encontrado en la base de datos',
            ],200);
            
        }
    
       }

       

    
}
