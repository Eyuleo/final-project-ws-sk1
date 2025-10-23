<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'student';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $studentProfile = $this->user()->studentProfile;
        
        return [
            'amount' => [
                'required',
                'numeric',
                'min:10',
                'max:' . $studentProfile->available_balance,
            ],
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Please enter a withdrawal amount.',
            'amount.min' => 'Minimum withdrawal amount is $10.',
            'amount.max' => 'Withdrawal amount exceeds your available balance.',
        ];
    }
}
