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
            'method' => 'required|in:bank_transfer,mobile_money',
            'account_number' => 'required_if:method,bank_transfer|string|max:50',
            'bank_name' => 'required_if:method,bank_transfer|string|max:100',
            'account_holder_name' => 'required_if:method,bank_transfer|string|max:255',
            'phone_number' => 'required_if:method,mobile_money|string|max:20',
            'mobile_provider' => 'required_if:method,mobile_money|in:telebirr,mpesa,other',
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
            'method.required' => 'Please select a withdrawal method.',
            'account_number.required_if' => 'Bank account number is required for bank transfers.',
            'bank_name.required_if' => 'Bank name is required for bank transfers.',
            'account_holder_name.required_if' => 'Account holder name is required for bank transfers.',
            'phone_number.required_if' => 'Phone number is required for mobile money withdrawals.',
            'mobile_provider.required_if' => 'Mobile provider is required for mobile money withdrawals.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'account_number' => 'account number',
            'bank_name' => 'bank name',
            'account_holder_name' => 'account holder name',
            'phone_number' => 'phone number',
            'mobile_provider' => 'mobile provider',
        ];
    }
}
