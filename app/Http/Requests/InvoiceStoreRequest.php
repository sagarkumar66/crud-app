<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'address' => 'required|string',
            'payment_status' => 'required|in:0,1',
            'payment_date' => 'required|date',
            'item_name' => 'required|array',
            'item_name.*' => 'required|string',
            'item_quantity' => 'required|array',
            'item_quantity.*' => 'required|integer|min:1',
            'item_amount' => 'required|array',
            'item_amount.*' => 'required|numeric|min:0',
        ];
    }
}
