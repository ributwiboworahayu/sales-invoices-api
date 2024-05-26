<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreInvoiceRequest extends FormRequestResponse
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_type' => 'required|in:SO,SI',
            'customer_id' => 'required|exists:customers,id',
            'sales_person' => 'required|string',
            'invoice_date' => 'required|date',
            'warehouse_name' => 'required|string',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|int',
            'terms' => 'required|int',
            'is_return' => 'required|boolean',
            'discount' => 'required|numeric',
            'remarks' => 'nullable|string',
        ];
    }
}
