<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        "http://64b1-139-193-219-63.ngrok.io/*",
        "http://ocr-laravel.com/*",
        "https://ocr-laravel.com/*"
    ];
}
