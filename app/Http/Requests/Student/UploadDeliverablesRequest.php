<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UploadDeliverablesRequest extends FormRequest
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
        return [
            'deliverables' => ['required', 'array', 'min:1', 'max:10'],
            'deliverables.*' => ['required', 'file', 'max:51200'], // 50MB max per file
            'delivery_note' => ['nullable', 'string', 'max:1000'],
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
            'deliverables.required' => 'Please upload at least one deliverable file.',
            'deliverables.min' => 'Please upload at least one deliverable file.',
            'deliverables.max' => 'You can upload a maximum of 10 files.',
            'deliverables.*.required' => 'Each deliverable file is required.',
            'deliverables.*.file' => 'Each deliverable must be a valid file.',
            'deliverables.*.max' => 'Each file may not be larger than 50MB.',
            'delivery_note.max' => 'The delivery note may not be greater than 1000 characters.',
        ];
    }
}
