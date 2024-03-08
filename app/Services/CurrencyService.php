<?php

namespace App\Services;

use App\Integration\CBR;
use App\Models\Currency;
use Exception;

class CurrencyService
{

    /**
     * @throws Exception
     */
    public function prepareCurrency(): array
    {
        $currencyLast = Currency::query()->where(['status' => Currency::STATUS_SUCCESS])->today()->orderBy('created_at', 'DESC')->first();

        if (!$currencyLast) {
            $currencyLast = $this->loadCurrency();
        }

        return $this->parseXML($currencyLast->response_xml);
    }

    /**
     * @throws Exception
     */
    public function loadCurrency(): ?Currency
    {
        $cbrCurrency = new CBR(CBR::createHttpClient());

        $currency = $cbrCurrency->load();

        if (!$currency) {
            throw new Exception('Не удалось загрузить курсы валют.');
        }

        return $currency;

    }

    private function parseXML($xml): array
    {

        $xml = simplexml_load_string(mb_convert_encoding($xml, "windows-1251", "utf-8"));

        $currencies = [];
        foreach ($xml->children() as $valute) {

            $currencies[] = array_merge(
                ['id' => $this->getIDFromAttributes($valute)],
                $this->getAllFields($valute)
            );
        }

        return $currencies;
    }

    private function getIDFromAttributes($xmlElement): ?string
    {
        $id = null;
        foreach ($xmlElement->attributes() as $key => $value) {
            if ($key == 'ID') {
                $id = (string)$value;
            }
        }

        return $id;
    }

    private function getAllFields($xmlElement): array
    {
        $element = [];
        foreach ($xmlElement as $key => $value) {
            $element[$key] = (string)$value;
        }
        return $element;
    }
}
