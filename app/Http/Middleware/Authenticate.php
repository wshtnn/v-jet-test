<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if ($request->header('api-token') && User::query()->where('api_token', $request->header('api-token'))->exists()) {
            try {
                return $next($request);
            } catch (QueryException $ex) {
                return response()->json(['success' => false, 'message' => $ex->getMessage()], 500);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }
    }
}
