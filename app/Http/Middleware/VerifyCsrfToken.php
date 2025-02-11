<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{   
    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest($request);  

        return is_string($request->session()->token()) &&
               is_string($token) &&
               hash_equals($request->session()->token(), $token);
    }
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'invoice/paytm/*',
        'plan-get-paytm-status',
        'invoice/mollie/*',
        '/plan/iyzipay/*',
        '/invoice/iyzipay/*',
        '/plan/aamarpay/*',
        '/invoice/aamarpay/*',
        '/course/iyzipay/*',
        '*course/mercado*',
        '/course/aamarpay/*',
        '/course/paytm*',
        '/roombooking/iyzipay/*',
        '/roombooking/aamarpay/*',
        '/plan/paytab/*',
        'plan-get-phonepe-status/*',
        '/invoice/phonepe/*',
        'course/phonepe/*',
        '*/get-payment-status/*',
        '*/cinetpay/*',
        'plan-easebuzz-payment-notify*',
        '/invoice/easebuzz/*',
        '/course/easebuzz*',
        'plan-get-powertranz-status',
        '/invoice-powertranz-status/*',
        '/property-booking-pay-with-stripe/*',
        'job/*',
        '/assistants/create-thread',
        '/assistants/send-message',
        'assistants/run/*',
        'assistants/recover-thread/*/*',
        'assistants/get-response/*',
        'assistants/save-summary/*',
        'assistants/save-history',
    ];
}
