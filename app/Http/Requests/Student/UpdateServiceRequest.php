<?php

declare(strict_types=1);

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $service = $this->route('service');
        return $this->user() 
            && $this->user()->role === 'student' 
            && $this->user()->studentProfile 
            && $service->student_profile_id === $this->user()->studentProfile->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:5', 'max:999999.99'],
            'delivery_time' => ['required', 'integer', 'min:1', 'max:365'],
            'revisions' => ['required', 'integer', 'min:0', 'max:10'],
            'requirements' => ['nullable', 'string', 'max:2000'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:50'],
            'portfolio_samples' => ['nullable', 'array', 'max:5'],
            'portfolio_samples.*' => ['file', 'mimes:jpeg,png,jpg,gif,pdf,doc,docx,mp4,mov,avi', 'max:51200'], // 50MB
            'sample_descriptions' => ['nullable', 'array'],
            'sample_descriptions.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'service title',
            'category' => 'service category',
            'description' => 'service description',
            'price' => 'service price',
            'delivery_time' => 'delivery time',
            'revisions' => 'number of revisions',
            'requirements' => 'service requirements',
            'tags.*' => 'tag',
            'portfolio_samples.*' => 'portfolio sample',
            'sample_descriptions.*' => 'sample description',
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
            'price.min' => 'The minimum service price is $5.',
            'delivery_time.min' => 'Delivery time must be at least 1 day.',
            'delivery_time.max' => 'Delivery time cannot exceed 365 days.',
            'revisions.max' => 'You can offer a maximum of 10 revisions.',
            'tags.max' => 'You can add a maximum of 10 tags.',
            'portfolio_samples.max' => 'You can upload a maximum of 5 portfolio samples.',
            'portfolio_samples.*.max' => 'Each portfolio sample must not exceed 50MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert tags from comma-separated string to array if needed
        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_filter(array_map('trim', explode(',', $this->tags))),
            ]);
        }
    }
}
