<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;

class LogRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try{
            $log = [
                'uri' => $request->getUri(),
                'method' => $request->getMethod(),
                'request_headers' => json_encode($request->headers->all()),
                'request_body' => json_encode($request->all()),
                'response' => $response->getContent(),
                'request_ip' => $this->getIp(),
                'server_ip' => $request->ip(),
                'device' => $request->header('User-Agent'),
            ];
            // return response($log);
           ApiLog::create($log);
        }catch(\Exception $e){
            return response()->json([
                'line'=>$e->getLine(),
                'file'=>$e->getFile(),
                'message'=>$e->getMessage(),
            ]);
        }

        return $response;
    }

    public function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return server ip when no client ip found
    }
}
