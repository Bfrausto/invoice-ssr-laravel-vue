<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;

class CompanyController extends Controller
{
    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->validated());
        return new CompanyResource($company);
    }
}
