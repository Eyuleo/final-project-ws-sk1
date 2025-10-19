<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by MessagePolicy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'exists:orders,id'],
            'message' => ['required', 'string', 'min:1', 'max:2000'],
            'attachment_files' => ['nullable', 'array', 'max:5'],
            'attachment_files.*' => ['file', 'max:10240'], // 10MB max per file
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'order_id.required' => 'Order ID is required.',
            'order_id.exists' => 'The specified order does not exist.',
            'message.required' => 'Message content is required.',
            'message.min' => 'Message must be at least 1 character.',
            'message.max' => 'Message may not be greater than 2000 characters.',
            'attachment_files.max' => 'You can upload a maximum of 5 files.',
            'attachment_files.*.file' => 'Each attachment must be a valid file.',
            'attachment_files.*.max' => 'Each file may not be larger than 10MB.',
        ];
    }
}
