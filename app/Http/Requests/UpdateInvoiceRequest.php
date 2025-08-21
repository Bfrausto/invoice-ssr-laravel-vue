<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'due_date' => 'required|date',
            'tax_id' => 'nullable|exists:taxes,id',
            'notes' => 'nullable|string',
            'status' => 'sometimes|string|in:draft,saved',

            'items' => 'required|array|min:1',
            'items.*.description' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0.01',
        ];
    }
}
