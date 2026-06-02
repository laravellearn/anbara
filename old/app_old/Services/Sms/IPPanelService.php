<?php

namespace App\Services\Sms;

use SoapClient;
use Throwable;

class IPPanelService
{
    public function sendOtp(
        string $mobile,
        string $code
    ): bool {

        try {

            $client = new SoapClient(
                'http://ippanel.com/class/sms/wsdlservice/server.php?wsdl'
            );
            $response = $client->sendPatternSms(
                '+989998764947',
                $mobile,
                config('services.ippanel.username'),
                config('services.ippanel.password'),
                'gbfxp7oo8h2jmsg',
                [
                    'code' => $code,
                ]
            );
            return $response > 0;
        } catch (Throwable $e) {
            report($e);
            return false;
        }
    }
}