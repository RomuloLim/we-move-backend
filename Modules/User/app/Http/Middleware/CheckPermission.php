<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\User\Enums\Permission;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if user has a specific permission.
 * Usage: ->middleware('permission:view-users')
 */
class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Não autenticado.',
            ], 401);
        }

        // Check if user has any of the specified permissions
        foreach ($permissions as $permission) {
            $permissionEnum = Permission::tryFrom($permission);

            if ($permissionEnum && $user->hasPermission($permissionEnum)) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Acesso negado. Você não tem permissão para realizar esta ação.',
        ], 403);
    }
}
