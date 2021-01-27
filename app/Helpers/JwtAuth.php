<?php

namespace App\Helpers;

use Firebase\JWT\JWT;

use Illuminate\Support\Facades\DB;

use App\User;
use PhpParser\Node\Stmt\Catch_;

class JwtAuth
{
    public $key;
    public function __construct()
    {
        $this->key = '5995';
    }
    public function signup($email, $password, $getToken = null)
    {
        //busca el usuario
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();
        //validacion si lo encontro
        $signup = false;
        if (is_object($user)) {
            $signup = true;
        }

        //si si lo encontro obtener datos
        if ($signup) {
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'image' => $user->image,
                'ine1' => $user->ine1,
                'ine2' => $user->ine2,
                'surname' => $user->surname,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)

            );
            //los datos codificarlo en jwt
            $jwt = JWT::encode($token, $this->key, 'HS256');
            //decodificar los datos que estan jwt
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            //si desea obtener los datos se enviar los datos decodificados
            if (is_null($getToken)) {
                $data = $jwt;

            } else {
                $data = $decoded;
            }
        }
        //en caso de no existir el email y pass
        else {
            $data = array(
                'status' => 'error',
                'message' => ' Login incorrecto',


            );
        }

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;
        try {
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }


        if ($getIdentity) {
            return $decoded;
        }
        return $auth;
    }
}
