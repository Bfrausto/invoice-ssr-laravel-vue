<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'client_id' => 'required|exists:clients,id',
            'due_date' => 'required|date',
            'tax_id' => 'nullable|exists:taxes,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0.01',
            'currency' => 'required|string|in:MXN,USD',
            'global_discount' => 'nullable|numeric|min:0|max:100',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
