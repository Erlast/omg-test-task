<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;

class CurrencyController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->apiResponse(function () {

            $currencyService = new CurrencyService();

            return $currencyService->prepareCurrency();

        }, false);
    }
}
