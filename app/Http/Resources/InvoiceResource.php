<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'folio' => $this->series . '-' . $this->folio,
            'notes' => $this->notes,
            'company_id' => $this->company_id,
            'client_id' => $this->client_id,
            'tax_id' => $this->tax_id,
            'issue_date' => $this->issue_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('Y-m-d'),
            'subtotal' => (float) $this->subtotal,
            'total_taxes' => (float) $this->total_taxes,
            'total' => (float) $this->total,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'client' => new ClientResource($this->whenLoaded('client')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'tax' => new TaxResource($this->whenLoaded('tax')),
            'currency' => $this->currency,
            'status' => $this->status,
            'global_discount' => (float) $this->global_discount,
        ];
    }
}
