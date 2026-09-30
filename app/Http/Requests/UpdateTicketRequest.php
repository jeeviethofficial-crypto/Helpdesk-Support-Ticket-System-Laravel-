<?php

namespace App\Http\Requests;

use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [];
        if ($this->has('status')) $rules['status'] = ['required', new Enum(TicketStatus::class)];
        if ($this->has('assigned_to')) $rules['assigned_to'] = ['nullable', 'exists:users,id'];
        return $rules;
    }
}
