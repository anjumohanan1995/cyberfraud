<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IPBlocker
{
    public function handle(Request $request, Closure $next)
    {
        $systemIp = $this->getSystemIp($request);
        $cacheKey = 'password_attempts_' . $systemIp;
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= 3) {
            return redirect()->back()->withInput()->withErrors(['password' => 'Too many failed attempts. This system has been blocked for 1 hour.']);
        }

        return $next($request);
    }

    private function getSystemIp(Request $request)
    {
        $ip = null;

        // Check for IP address in the X-Forwarded-For header
        if ($request->header('X-Forwarded-For')) {
            $ipList = explode(',', $request->header('X-Forwarded-For'));
            $ip = trim(end($ipList));
        }

        // If not found in X-Forwarded-For, check other common proxy headers
        if (empty($ip)) {
            $headers = [
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR'
            ];

            foreach ($headers as $header) {
                if ($request->server($header)) {
                    $ip = trim($request->server($header));
                    break;
                }
            }
        }

        // If still not found, use the default method
        if (empty($ip)) {
            $ip = $request->ip();
        }

        // Validate the IP address
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            // If the IP is not valid or is a private/reserved IP, fall back to the server's remote address
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
}

