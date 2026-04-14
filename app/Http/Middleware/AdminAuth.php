<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-Admin-Key')
            ?? $request->query('key')
            ?? $request->input('admin_key')
            ?? session('admin_key');

        if (!$key || $key !== config('app.admin_secret_key')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Forbidden: Invalid admin key.'], 403);
            }
            return redirect()->route('admin.login');
        }

        session(['admin_key' => $key]);
        return $next($request);
    }
}
