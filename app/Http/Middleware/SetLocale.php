<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = array_keys(config('locales.available'));

        $user = Auth::user();
        $locale = ($user instanceof User ? $user->locale : null) ?? $request->session()->get('locale');

        App::setLocale(in_array($locale, $available, true) ? $locale : config('locales.default'));

        return $next($request);
    }
}
