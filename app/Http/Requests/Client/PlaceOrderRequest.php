<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ServiceListing;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'client';
    }

    public function rules(): array
    {
        return [
            'service_listing_id' => 'required|exists:service_listings,id',
            'requirements' => 'required|string|min:50|max:5000',
            'quantity' => 'required|integer|min:1|max:100',
            'deadline' => 'required|date|after:now|before:+1 year',
            'attachment_files' => 'nullable|array|max:5',
            'attachment_files.*' => 'file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'requirements.min' => 'Please provide detailed requirements (at least 50 characters).',
            'deadline.after' => 'Deadline must be in the future.',
            'deadline.before' => 'Deadline cannot be more than 1 year from now.',
            'quantity.max' => 'Maximum quantity is 100.',
            'attachment_files.max' => 'You can upload up to 5 attachment files.',
            'attachment_files.*.max' => 'Each attachment must not exceed 5MB.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $service = ServiceListing::find($this->service_listing_id);
            
            if ($service && $service->status !== 'active') {
                $validator->errors()->add('service_listing_id', 'This service is not currently available.');
            }
            
            if ($service && $service->pricing_model === 'fixed' && $this->quantity != 1) {
                $validator->errors()->add('quantity', 'Quantity must be 1 for fixed-price services.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'service_listing_id' => 'service',
            'requirements' => 'project requirements',
            'attachment_files' => 'attachment files',
        ];
    }
}
