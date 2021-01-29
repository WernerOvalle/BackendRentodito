<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

use Illuminate\Http\Response;

class UserController extends Controller
{
    public function pruebas(Request $request)
    {
        return "hola";
    }


    public function register(Request $request)
    {
        /*  PARAMETROS EJEMPLO

        $nombre= $request->input('name');
        $surname= $request->input('surname');

        return "accion de registro de usuario:". $nombre.$surname;*/


        //RECOGER DATOS

        $json = $request->input("json", null);
        //var_dump($json);
        //die();
        $params = json_decode($json); //objecto
        $params_array = json_decode($json, true); //array
        //var_dump($params_array);
        //die();

        //VALIDACION
        if (!empty($params) && !empty($params_array)) {
            $params_array = array_map('trim', $params_array);
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users', //no duplicidad
                'password' => 'required',
                'telefono' => 'required|numeric',
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'error' => $validate->errors()
                );
            } else {

                //CIFRAR
                //$pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);
                $pwd = hash('sha256', $params->password);

                //crear
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';
                $user->image = $params_array['image'];
                $user->telefono = $params_array['telefono'];
                $user->ine2 = $params_array['ine2'];
                $user->ine1 = $params_array['ine1'];
                /*  var_dump($user);
                 die();*/
                $user->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Usuario creado, ahora inicia sesión',

                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Datos mal enviados',

            );
        }
        //json example :{"name":"werner","surname":" OVALLE ","email":"Wernerovalle1995@hotmail.com","password":"123"}
        return response()->json($data, $data['code']);
    }


    public function login(Request $request)
    { //llamado jwtauth
        $jwtAuth = new \JwtAuth();
        //parametros en json
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true); //en array
        //validar
        $validate = \Validator::make($params_array, [

            'email' => 'required|email',
            'password' => 'required'
        ]);
        //si esta mal
        if ($validate->fails()) {
            $singup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Error en los datos ingresados',
                'error' => $validate->errors()
            );
        } else {
            //cifrar
            $pwd = hash('sha256', $params->password);
            //obtener token
            $singup = $jwtAuth->signup($params->email, $pwd);
            /*     $email = "wernerovalle1995@hotmail.com";
                $password = "123";
                $pwd = hash('sha256', $password);

                return response()->json($jwtAuth->signup($email, $pwd, true));*/
            //si desea obtener datos del usuario
            if (!empty($params->gettoken)) {
                $singup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }
        //retorna respuesta en json
        return response()->json($singup, 200);
    }


    public function update(Request $request)
    { //obtengo token en cabecer
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        //verifica token
        $checkToken = $jwtAuth->checkToken($token);

        //obtengo json de peticion put
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //si el token es valido y hay parametros
        if ($checkToken && !empty($params_array)) {
            //obtengo  datos de usuario
            $user = $jwtAuth->checkToken($token, true);

            //valido email y nombres
            $validate = \Validator::make($params_array, [

                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'telefono' => 'required|numeric',
                'email' => 'required|email|unique:users' . $user->sub
            ]);
            //codificio la contraseña y la actulizo en el array
            $pwd = hash('sha256', $params->password);
            $params_array['password'] = $pwd;
            /*  var_dump( $params_array['password']);
            die();*/

            //quito los valores que no deseo actualizar
            unset($params_array['id']);
            unset($params_array['role']);

            unset($params_array['created_At']);
            unset($params_array['remeber_token']);
            //Actualizo
            $usert_update = User::where('id', $user->sub)->update($params_array);
            //mensaje de exito
            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' =>'datos actualizados',
                'user' => $user,
                'changes' => $params_array
            );
        } else {
            //mensaje de error
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no esta identificado'
            );
        }
        //devuelvo datos
        return response()->json($data, $data['code']);
    }
    public function upload1(Request $request)
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
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );
        }


        //   return response($data, $data['code'])->header('Content-Type', 'text/plain');
        return response()->json($data, $data['code']);
    }

    public function upload2(Request $request)
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
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );
        }


        //   return response($data, $data['code'])->header('Content-Type', 'text/plain');
        return response()->json($data, $data['code']);
    }
    public function upload3(Request $request)
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
            \Storage::disk('users')->put($image_name, \File::get($image));

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
        $isset = \Storage::disk('users')->exists($filename);
        if ($isset) {
            $file =  \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        } else {
            $data = array(
                'code' => 404,
                'status' => 'errro',
                'message' => 'imagen no existe'
            );
            return response()->json($data, $data['code']);
        }
    }


    public function detail($id)
    { //comprobar si existe imagen
        $user = User::find($id);
        if (is_object($user)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );

        } else {
            $data = array(
                'code' => 404,
                'status' => 'Error',
                'message' => 'no existe usuario'
            );
        }
        return response()->json($data, $data['code']);
    }
}
