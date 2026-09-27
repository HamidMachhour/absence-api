<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CheckForInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lockFile = storage_path('app/installed.lock');

        // Check if the installation lock file exists
        if (!File::exists($lockFile)) {
            // --- Installation Logic ---

            // 1. Generate Application Key (Essential if .env is copied)
            // Checks if key is missing and generates it, which is safer than running the command every time.
            if (empty(config('app.key')) || Str::startsWith(config('app.key'), 'base64:')) {
                // Warning: Artisan::call('key:generate') modifies the .env file.
                // Ensure your web user has write permissions on the .env file.
                Artisan::call('key:generate', ['--force' => true]);
            }
            
            // 2. Clear Database (migrate:fresh will clear, migrate will run only new ones)
            // Running 'migrate:fresh' is suitable for a first-time setup
            Artisan::call('migrate:fresh', ['--force' => true]);

            // 3. Run Seeders
            // Assumes 'DatabaseSeeder' calls all other necessary seeders
            Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
            
            // 4. Create the lock file
            File::put($lockFile, now()->toDateTimeString());
            
            // It is often best to redirect after a critical setup step to ensure 
            // the new configuration takes effect on the next request.
            return redirect($request->fullUrl()); 
        }

        return $next($request);
    }
}