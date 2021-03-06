<?php

namespace App\Http\Middleware;

use Closure;

class Cross
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $origin = config('allow');
//        dd($origin);
        $response = $next($request);
//        echo $request->header('Origin');
//        dd($request->getSchemeAndHttpHost());
//        dd($origin);
        if (in_array($request->header('Origin'),$origin)){
//            dd($origin);
            if($request->getMethod() === 'OPTIONS'){
                $response->header('Access-Control-Allow-Origin', $request->header('Origin'));
//            $response->header('Access-Control-Allow-Origin', '*');
                $response->header('Access-Control-Allow-Headers', 'Origin, Access-Control-Request-Headers, SERVER_NAME, Access-Control-Allow-Headers, cache-control, token, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie, X-XSRF-TOKEN');
                $response->header('Access-Control-Allow-Methods', 'OPTIONS,GET, POST, PATCH, PUT, DELETE');
                $response->header('Access-Control-Allow-Credentials', 'true');
            }else{
//            $response->header('Access-Control-Allow-Origin', '*');
                $response->header('Access-Control-Allow-Origin', $request->header('Origin'));
                $response->header('Access-Control-Allow-Headers', 'Origin, Access-Control-Request-Headers, SERVER_NAME, Access-Control-Allow-Headers, cache-control, token, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie, X-XSRF-TOKEN');
                $response->header('Access-Control-Allow-Methods', 'OPTIONS,GET, POST, PATCH, PUT, DELETE');
                $response->header('Access-Control-Allow-Credentials', 'true');
            }
        }

        return $response;
    }
}
