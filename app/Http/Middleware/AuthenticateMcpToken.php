<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the /mcp endpoint with a static bearer token from .env (MCP_TOKEN).
 * An empty token disables the endpoint entirely.
 */
class AuthenticateMcpToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = (string) config('services.mcp.token');

        if ($token === '') {
            abort(503, 'MCP endpoint is disabled (MCP_TOKEN is not set).');
        }

        if (! hash_equals($token, (string) $request->bearerToken())) {
            return response()->json(['error' => 'Unauthorized'], 401, ['WWW-Authenticate' => 'Bearer']);
        }

        return $next($request);
    }
}
