<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Articulos;
use App\Helpers\JwtAuth;

class ArticulosController extends Controller
{ //middleware para todos y excepciones
    public function __construct()

    {

        $this->middleware('api.auth', ['except' => ['index', 'show','getImage', 'getArticulosByCateogoria','getArticulosByUser','getArticulosPersona','getArticulosName','getArticulosByTienda']]);
    }
    public function index()
    {
        $Articulos = Articulos::all()->load('categorias')->load('tiendas')->load('user');
        return  response()->json([
            'code' => 200,
            'status' => 'success',
            'Articulos' => $Articulos
        ]);
    }

    public function show($id)
    {
        $Articulos = Articulos::find($id)->load('categorias', 'user2', 'tiendas');
        if (is_object($Articulos)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'categories' => $Articulos
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'no existe'
            ];
        }
        return  response()->json($data, $data['code']);
    }


    public function store(Request $request)
    {

        //recoger datos
        $json = $request->input("json", null);
        $params = json_decode($json); //objecto
        $params_array = json_decode($json, true); //array

        //VALIDACION
        if (!empty($params) && !empty($params_array)) {
            $params_array = array_map('trim', $params_array);
            //conserguir usuario identificado
            $jwtAuth = new \JwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtAuth->checkToken($token, true);
            //validar
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'categoria_id' => 'required',
                'image' => 'required',

            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'No se creo articulo',
                    'error' => $validate->errors()
                );
            } else {


                //crear
                $Articulos = new Articulos();
                $Articulos->user_id = $user->sub;
                $Articulos->categoria_id = $params->categoria_id;
                $Articulos->tienda_id = $params->tienda_id;
                $Articulos->title = $params->title;
                $Articulos->content = $params->content;
                $Articulos->image = $params->image;
                $Articulos->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'Articulos' => $Articulos,

                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Datos mal enviados',

            );
        }
        //mostrar
        return response()->json($data, $data['code']);
    }


    public function update($id, Request $request)
    {
        $json = $request->input("json", null);
        $params = json_decode($json); //objecto
        $params_array = json_decode($json, true); //array

        //VALIDACION
        if (!empty($params) && !empty($params_array)) {
            $params_array = array_map('trim', $params_array);
            //conserguir usuario identificado
            $jwtAuth = new \JwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtAuth->checkToken($token, true);
            //validar
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'categoria_id' => 'required',
                'image' => 'required'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'No se actualizo Articulos',
                    'error' => $validate->errors()
                );
            } else {

                //quitar lo que no quiero actualizar
                unset($params_array['user']);
                unset($params_array['user_id']);
                unset($params_array['created_at']);
                if($params_array['fecha_apartado']   == null) {
                    $params_array['fecha_apartado']=null;
                }
                date_default_timezone_set('America/Mexico_City');
                $params_array['updated_at']= date("Y-m-d H:i:s");
                unset($params_array['id']);


                //actualizar
                $Articulos = Articulos::where('id', $id)->update($params_array); //->first(); updateOrCreate()


                $data = array(
                    'status' => 'success',
                    'code' => 200,

                    'changes' => $params_array,

                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Datos mal enviados',

            );
        }
        //mostrar
        return response()->json($data, $data['code']);
    }


    public function destroy($id, Request $request)
    {
  /* en caso de querer borrar unicamente los arituclos de los usuarios logeados
         //conserguir usuario identificado


         $jwtAuth = new \JwtAuth();
         $token = $request->header('Authorization', null);
         $user = $jwtAuth->checkToken($token, true);

         $Articulos = Articulos::where('id',$id)->where('user_id', $user->sub)->first() ; */
        $Articulos = Articulos::find($id);


        if (!empty($Articulos)) {
            //borrarlo
            $Articulos->delete();

            //devolver

            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Datos eliminados',

            );
        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Articulo no existe',

            );
        }
        return response()->json($data, $data['code']);
    }

    public function upload(Request $request)
    { //Recoger datos de la peticion
        $image = $request->file('file0');

        //validacion de imagen

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        if (!$image || $validate->fails()) {
            //devolver  el resultado
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'error al subir imagen'
            );
        } else {
            //guardar imagen
            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('images')->put($image_name, \File::get($image));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );
        }


        //   return response($data, $data['code'])->header('Content-Type', 'text/plain');
        return response()->json($data, $data['code']);
    }

    public function getImage($filename)
    { //comprobar si existe imagen
        $isset = \Storage::disk('images')->exists($filename);
        if ($isset) {
            $file =  \Storage::disk('images')->get($filename);
            return new Response($file, 200);
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'imagen no existe'
            );
            return response()->json($data, $data['code']);
        }
    }


    public function getArticulosByTienda($id)
    {
        $Articulos = Articulos::where('tienda_id',$id)->get()->load('user', 'tiendas');

        return  response()->json([
            'status' => 'success',
            'articulos' =>   $Articulos

        ], 200);
    }
    public function getArticulosByCateogoria($id)
    {
        $Articulos = Articulos::where('categoria_id',$id)->get()->load('user', 'tiendas');

        return  response()->json([
            'status' => 'success',
            'articulos' =>   $Articulos

        ], 200);
    }
    public function getArticulosPersona()
    {

      /*  $Articulos = DB::table('Articulos')->join('tiendas', 'tiendas.id', '=', 'Articulos.tienda_id')
        ->join('users', function ($join) {
            $join->on('Articulos.user_id', '=', 'users.id')
                 ->where('users.role', '=', 'PARTICULAR-PROD');
        })
        ->get();*/

       // all()->with('user')->where('user.role', 'PARTICULAR-PROD')->load('categorias')->load('tiendas')->toArray();
       $Articulos = Articulos::with(array('user' => function($query)
{
     $query->where('users.role', '=', 'PARTICULAR-PROD');

}))

    ->get();

    $Articulos->load('categorias')->load('tiendas');


        return  response()->json([
            'code' => 200,
            'status' => 'success',
            'Articulos' => $Articulos
        ]);
    }
    public function getArticulosByUser($id)
    {
        $Articulos = Articulos::where('user_id',$id)->get();

        return  response()->json([
            'status' => 'success',
            'articulos' =>   $Articulos

        ], 200);
    }


    public function getArticulosName($name)
    {
        $Articulos = Articulos::Where('title', 'like', '%' . $name . '%')->get()->load('tiendas')->load('user');

        return  response()->json([
            'status' => 'success',
            'Articulos' =>   $Articulos

        ], 200);
    }
}
