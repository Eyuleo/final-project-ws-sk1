<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResolveDisputeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resolution' => 'required|in:release_to_student,refund_to_client,split',
            'student_amount' => 'required_if:resolution,split|numeric|min:0',
            'client_amount' => 'required_if:resolution,split|numeric|min:0',
            'admin_notes' => 'required|string|min:50|max:2000',
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
            'resolution.required' => 'Please select a resolution option.',
            'resolution.in' => 'Invalid resolution option selected.',
            'student_amount.required_if' => 'Student amount is required for split resolution.',
            'student_amount.numeric' => 'Student amount must be a number.',
            'student_amount.min' => 'Student amount cannot be negative.',
            'client_amount.required_if' => 'Client amount is required for split resolution.',
            'client_amount.numeric' => 'Client amount must be a number.',
            'client_amount.min' => 'Client amount cannot be negative.',
            'admin_notes.required' => 'Admin notes are required.',
            'admin_notes.min' => 'Please provide detailed reasoning (at least 50 characters).',
            'admin_notes.max' => 'Admin notes cannot exceed 2000 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->resolution === 'split') {
                $order = $this->route('order');
                $total = $this->student_amount + $this->client_amount;
                
                if (abs($total - $order->total_amount) > 0.01) {
                    $validator->errors()->add('student_amount', 'Split amounts must equal order total (' . number_format($order->total_amount, 2) . ').');
                }
            }
        });
    }
}
