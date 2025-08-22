<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaxRequest;
use App\Http\Resources\TaxResource;
use App\Models\Tax;

class TaxController extends Controller
{
    public function store(StoreTaxRequest $request)
    {
        $tax = Tax::create($request->validated());
        return new TaxResource($tax);
    }
}
