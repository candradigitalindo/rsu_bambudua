<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log only authenticated users and only for web requests
        if (auth()->check()) {
            // Exclude some paths (assets, debugbar, health checks)
            $path = $request->path();
            $excluded = [
                'up',
                'debugbar',
                'storage',
                'images',
                'js',
                'css',
                'fonts',
                'vendor',
            ];
            foreach ($excluded as $ex) {
                if (str_starts_with($path, $ex)) {
                    return $response;
                }
            }

            // Sanitize payload to avoid storing secrets
            $input = $request->except(['password', 'password_confirmation', '_token']);

            // Prefer explicit subject/module/payload set by business code
            $explicitSubject = $request->attributes->get('activity_subject');
            $explicitModule  = $request->attributes->get('activity_module');
            $explicitPayload = $request->attributes->get('activity_payload');

            $method = strtoupper($request->getMethod());
            $routeName = optional($request->route())->getName();
            $firstSeg  = explode('/', trim($request->path(), '/'))[0] ?? null;

            // Only log when:
            // - there is an explicit subject from business logic, OR
            // - the HTTP method indicates a change (POST/PUT/PATCH/DELETE)
            $isMutating = in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true);
            if (!$explicitSubject && !$isMutating) {
                // Pure access (GET/HEAD/OPTIONS) without explicit subject -> do not log
                return $response;
            }

            // Build subject
            if ($explicitSubject) {
                $subject = $explicitSubject;
            } else {
                // Auto-subject based on method + route/path (Indonesian verbs)
                $verbMap = [
                    'POST'   => 'Menambahkan',
                    'PUT'    => 'Mengubah',
                    'PATCH'  => 'Mengubah',
                    'DELETE' => 'Menghapus',
                ];
                $label = $routeName ? str_replace(['.', '_'], ' ', $routeName) : trim($request->path(), '/');
                $subject = ($verbMap[$method] ?? $method) . ' ' . ucwords($label);
            }

            $module    = $explicitModule ?: $firstSeg;

            // Merge explicit payload into sanitized input (explicit takes precedence)
            $payload = $input;
            if (is_array($explicitPayload)) {
                $payload = array_merge($input, $explicitPayload);
            }

            ActivityLog::create([
                'user_id'    => auth()->id(),
                'subject'    => mb_strimwidth((string) $subject, 0, 200, '…'),
                'module'     => $module,
                'method'     => $method,
                'url'        => $request->fullUrl(),
                'route_name' => $routeName,
                'ip'         => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'status'     => $response->getStatusCode(),
                'payload'    => empty($payload) ? null : $payload,
            ]);
        }

        return $response;
    }
}