<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = array_keys(config('locales.available'));

        $locale = $request->user()?->locale ?? $request->session()->get('locale');

        App::setLocale(in_array($locale, $available, true) ? $locale : config('locales.default'));

        return $next($request);
    }
}
