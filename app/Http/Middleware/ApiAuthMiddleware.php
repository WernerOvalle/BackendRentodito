<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    { //obtengo token en cabecer
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        //verifica token
        $checkToken = $jwtAuth->checkToken($token);

        if ($checkToken ) {
        return $next($request);
    }else{
        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'El usuario no esta identificado'
        );
        return response()->json($data, $data['code']);
    }
}}
