<?php

declare(strict_types=1);

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'student';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic Info
            'bio' => ['nullable', 'string', 'max:1000'],
            'tagline' => ['nullable', 'string', 'max:100'],
            
            // University Info
            'university' => ['required', 'string', 'max:255'],
            'field_of_study' => ['required', 'string', 'max:255'],
            'year_of_study' => ['required', 'string', 'max:50'],
            
            // Skills
            'skills' => ['nullable', 'array', 'max:20'],
            'skills.*' => ['string', 'max:50'],
            
            // Languages
            'languages' => ['nullable', 'array', 'max:10'],
            'languages.*' => ['string', 'max:50'],
            
            // Social Links
            'github_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'behance_url' => ['nullable', 'url', 'max:255'],
            
            // Profile Picture
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB
            
            // Portfolio Files
            'portfolio_files' => ['nullable', 'array', 'max:10'],
            'portfolio_files.*' => ['file', 'mimes:jpeg,png,jpg,gif,pdf,doc,docx,mp4,mov,avi', 'max:51200'], // 50MB
            
            // Portfolio File Descriptions
            'portfolio_descriptions' => ['nullable', 'array'],
            'portfolio_descriptions.*' => ['nullable', 'string', 'max:255'],
            
            // Availability
            'available_for_work' => ['boolean'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
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
            'bio' => 'biography',
            'tagline' => 'professional tagline',
            'field_of_study' => 'field of study',
            'year_of_study' => 'year of study',
            'skills.*' => 'skill',
            'languages.*' => 'language',
            'github_url' => 'GitHub URL',
            'linkedin_url' => 'LinkedIn URL',
            'portfolio_url' => 'portfolio URL',
            'behance_url' => 'Behance URL',
            'profile_picture' => 'profile picture',
            'portfolio_files.*' => 'portfolio file',
            'portfolio_descriptions.*' => 'portfolio description',
            'available_for_work' => 'availability status',
            'hourly_rate' => 'hourly rate',
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
            'skills.max' => 'You can add a maximum of 20 skills.',
            'languages.max' => 'You can add a maximum of 10 languages.',
            'portfolio_files.max' => 'You can upload a maximum of 10 portfolio files.',
            'profile_picture.max' => 'Profile picture must not exceed 5MB.',
            'portfolio_files.*.max' => 'Each portfolio file must not exceed 50MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert skills and languages from comma-separated strings to arrays if needed
        if ($this->has('skills') && is_string($this->skills)) {
            $this->merge([
                'skills' => array_filter(array_map('trim', explode(',', $this->skills))),
            ]);
        }

        if ($this->has('languages') && is_string($this->languages)) {
            $this->merge([
                'languages' => array_filter(array_map('trim', explode(',', $this->languages))),
            ]);
        }

        // Ensure available_for_work is boolean
        if ($this->has('available_for_work')) {
            $this->merge([
                'available_for_work' => filter_var($this->available_for_work, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
