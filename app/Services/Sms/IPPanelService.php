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
                'https://ippanel.com/class/sms/wsdlservice/server.php?wsdl',
                [
                    'connection_timeout' => 5,
                    'cache_wsdl' => WSDL_CACHE_MEMORY,
                ]
            );
            $response = $client->sendPatternSms(
                config('services.ippanel.from'),
                $mobile,
                config('services.ippanel.username'),
                config('services.ippanel.password'),
                config('services.ippanel.pattern'),
                [
                    'code' => $code,
                ]
            );
            logger()->info('IPPANEL RESPONSE', [
                'response' => $response
            ]);
            return $response > 0;
        } catch (Throwable $e) {
            report($e);
            return false;
        }
    }
}