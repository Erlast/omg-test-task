<?php

namespace App\Integration;

use App\Models\Currency;
use GuzzleHttp\Client;

class CBR
{

    protected Client $guzzle;
    public int $tries = 3;

    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function load()
    {
        for ($i = 0; $i <= $this->tries - 1; $i++) {
            try {
                $res = $this->guzzle->get('');

                if ($res->getStatusCode() === 200) {
                    $content = $res->getBody()->getContents();
                    $d = new \SimpleXMLElement($content);

                    $currency = new Currency();
                    $currency->response_xml = mb_convert_encoding($d->asXML(), "utf-8", "windows-1251");
                    $currency->status = Currency::STATUS_SUCCESS;
                    $currency->save();

                    return $currency;
                }
                exit();
            } catch (\Exception $e) {

                $currency = new Currency();
                $currency->response_xml = $e->getMessage();
                $currency->status = Currency::STATUS_ERROR;
                $currency->save();

            }
        }
    }


    public static function createHttpClient()
    {
        return new Client([
            'base_uri' => config('app.currency_service'),
            'defaults' => [
                'timeout' => 3
            ]
        ]);
    }
}
