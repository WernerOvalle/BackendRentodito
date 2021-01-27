<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Categorias;

class CategoriasController extends Controller
{
    /*  public function pruebas (Request $request){
        return "hola";
    }*/

//middleware para todos y excepciones
    public function __construct()

    {

        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }
    public function index()
    {
        $categories = Categorias::all();
        return  response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        ]);
    }
    public function show($id)
    {
        $categories = Categorias::find($id);
        if (is_object($categories)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'categories' => $categories
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
            $validate = \Validator::make($params_array, [
                'name' => 'required'
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
                $Categorias = new Categorias();
                $Categorias->name = $params_array['name'];

                $Categorias->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'categoria' => $Categorias,

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
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'No se actualizo cateogoria',
                    'error' => $validate->errors()
                );
            } else {

                //quitar lo que no quiero actualizar
                unset($params_array['id']);
                unset($params_array['created_at']);

                //actualizar
                $Categorias = Categorias::where('id',$id)->update($params_array);


                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'categoria' => $params_array,

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
}