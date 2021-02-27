<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Tiendas;

class TiendasController extends Controller
{
    public function pruebas (Request $request){
        return "hola";
    }
    public function __construct()

    {

        $this->middleware('api.auth', ['except' => ['index', 'show','getEstados','getTiendasbyEstados']]);
    }

    public function index()
    {
        $Tiendas = Tiendas::all();
        return  response()->json([
            'code' => 200,
            'status' => 'success',
            'Tiendas' => $Tiendas
        ]);
    }


    public function show($id)
    {
        $Tiendas = Tiendas::where('id',$id)->get();
        if (is_object($Tiendas)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'Tiendas' => $Tiendas
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

    public function getTiendasbyEstados($id)
    {
        $Tiendas = Tiendas::where('estado',$id)->get();

        return  response()->json([
            'code' => 200,
            'status' => 'success',
            'Tiendas' => $Tiendas
        ]);
    }
    public function getEstados()
    {
        $Tiendas = DB::table('Tiendas')->distinct()->select('estado')->orderBy('estado')->get();

        return  response()->json([
            'code' => 200,
            'status' => 'success',
            'Tiendas' => $Tiendas
        ]);
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
            $validate = \Validator::make($params_array, [
                'name' => 'required',
                'image' => 'required',
                'estado'=>'required'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'No se creo cateogoria',
                    'error' => $validate->errors()
                );
            } else {


                //crear
                $Tiendas = new Tiendas();
                $Tiendas->name = $params_array['name'];
                $Tiendas->description = $params_array['description'];
                $Tiendas->estado = $params_array['estado'];
                $Tiendas->image = $params_array['image'];
                $Tiendas->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'categoria' => $Tiendas,

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
                'name' => 'required',
                'image' => 'required'

            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'No se actualizo Tiendas',
                    'error' => $validate->errors()
                );
            } else {

                //quitar lo que no quiero actualizar

                unset($params_array['created_at']);
                date_default_timezone_set('America/Mexico_City');
                $params_array['updated_at']= date("Y-m-d H:i:s");
                unset($params_array['id']);


                //actualizar
                $Tiendas = Tiendas::where('id', $id)->update($params_array); //->first(); updateOrCreate()


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
        $Tiendas = Tiendas::find($id);


        if (!empty($Tiendas)) {
            //borrarlo
            $Tiendas->delete();

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
                'message' => 'Tienda no existe',

            );
        }
        return response()->json($data, $data['code']);
    }


}
