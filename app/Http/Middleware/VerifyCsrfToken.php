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
        //
        'api/register',
        'api/Otpverify',
        'api/login',
        'api/resetpassword',
        'api/password/forgotpassword',
        'api/password/reset',
        '/api/userdetail',
        '/api/forgotOtpVerify'
        
    ];
}
