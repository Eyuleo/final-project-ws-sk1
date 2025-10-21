<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class DisputeOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $order = $this->route('order');
        return $order->client_profile_id === $this->user()->clientProfile->id
            && $order->status === 'completed'
            && $order->revision_count >= $order->max_revisions;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => 'required|string|min:50|max:2000',
            'evidence_files' => 'nullable|array|max:5',
            'evidence_files.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
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
            'reason.required' => 'Please provide a reason for the dispute.',
            'reason.min' => 'Please provide a detailed explanation (at least 50 characters).',
            'reason.max' => 'Dispute reason cannot exceed 2000 characters.',
            'evidence_files.max' => 'You can upload up to 5 evidence files.',
            'evidence_files.*.mimes' => 'Evidence files must be jpg, jpeg, png, or pdf.',
            'evidence_files.*.max' => 'Each evidence file must not exceed 5MB.',
        ];
    }
}
