<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User must be a client
        if ($this->user()->role !== 'client') {
            return false;
        }

        // Get the order
        $orderId = $this->input('order_id');
        $order = \App\Models\Order::find($orderId);

        if (!$order) {
            return false;
        }

        // Order must belong to the client
        if ($order->client_profile_id !== $this->user()->clientProfile?->id) {
            return false;
        }

        // Order must be approved
        if ($order->status !== 'approved') {
            return false;
        }

        // Order must not already have a review
        if ($order->review()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|min:20|max:1000',
            'tags' => 'nullable|array|max:5',
            'tags.*' => 'string|in:professional,responsive,quality,communication,timely,creative,exceeded_expectations',
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
            'rating.required' => 'Please select a rating.',
            'rating.min' => 'Rating must be between 1 and 5 stars.',
            'rating.max' => 'Rating must be between 1 and 5 stars.',
            'review_text.min' => 'Review must be at least 20 characters if provided.',
            'review_text.max' => 'Review cannot exceed 1000 characters.',
            'tags.max' => 'You can select up to 5 tags.',
            'tags.*.in' => 'Invalid tag selected.',
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
            'order_id' => 'order',
            'review_text' => 'review',
        ];
    }
}
