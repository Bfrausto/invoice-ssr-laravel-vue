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
            'subtotal' => (float) $this->subtotal,
            'total_taxes' => (float) $this->total_taxes,
            'total' => (float) $this->total,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'client' => new ClientResource($this->whenLoaded('client')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'tax' => new TaxResource($this->whenLoaded('tax')),
        ];
    }
}
