<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;

class ClientController extends Controller
{
    public function store(StoreClientRequest $request)
    {
        $client = Client::create($request->validated());

        return new ClientResource($client);
    }
}
