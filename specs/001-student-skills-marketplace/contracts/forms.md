# Form Validation Contracts

**Date**: 2025-10-18  
**Feature**: Student Skills Marketplace

## Overview

This document defines all Form Request validation rules for the Student Skills Marketplace. All validation uses Laravel Form Request classes.

---

## Student Form Requests

### UpdateProfileRequest

**Route**: `PUT /student/profile`  
**Controller**: `Student\ProfileController@update`

```php
namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'student';
    }

    public function rules(): array
    {
        return [
            'university' => 'required|string|max:255',
            'student_id' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'skills' => 'nullable|array|max:10',
            'skills.*' => 'string|max:50',
            'hourly_rate_min' => 'nullable|numeric|min:5|max:1000',
            'hourly_rate_max' => 'nullable|numeric|min:5|max:1000|gte:hourly_rate_min',
            'portfolio_url' => 'nullable|url|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'hourly_rate_max.gte' => 'Maximum rate must be greater than or equal to minimum rate.',
            'skills.max' => 'You can add up to 10 skills.',
            'profile_picture.max' => 'Profile picture must not exceed 2MB.',
        ];
    }
}
```

---

### StoreServiceRequest

**Route**: `POST /student/services`  
**Controller**: `Student\ServiceController@store`

```php
namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'student' 
            && $this->user()->studentProfile()->exists();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:10|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:100|max:5000',
            'pricing_model' => 'required|in:fixed,hourly',
            'price' => 'required|numeric|min:5|max:10000',
            'delivery_days' => 'required|integer|min:1|max:90',
            'requirements' => 'nullable|string|max:2000',
            'portfolio_files' => 'nullable|array|max:5',
            'portfolio_files.*' => 'file|mimes:jpg,jpeg,png,pdf,docx|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'title.min' => 'Service title must be at least 10 characters.',
            'description.min' => 'Description must be at least 100 characters to help clients understand your service.',
            'price.min' => 'Minimum price is $5.',
            'price.max' => 'Maximum price is $10,000.',
            'delivery_days.max' => 'Maximum delivery time is 90 days.',
            'portfolio_files.max' => 'You can upload up to 5 portfolio files.',
            'portfolio_files.*.max' => 'Each portfolio file must not exceed 10MB.',
        ];
    }
}
```

---

### UpdateServiceRequest

**Route**: `PUT /student/services/{id}`  
**Controller**: `Student\ServiceController@update`

```php
namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $service = $this->route('service');
        return $this->user()->id === $service->studentProfile->user_id;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:10|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:100|max:5000',
            'pricing_model' => 'required|in:fixed,hourly',
            'price' => 'required|numeric|min:5|max:10000',
            'delivery_days' => 'required|integer|min:1|max:90',
            'requirements' => 'nullable|string|max:2000',
            'portfolio_files' => 'nullable|array|max:5',
            'portfolio_files.*' => 'file|mimes:jpg,jpeg,png,pdf,docx|max:10240',
            'remove_files' => 'nullable|array',
            'remove_files.*' => 'string',
        ];
    }
}
```

---

### DeclineOrderRequest

**Route**: `POST /student/orders/{id}/decline`  
**Controller**: `Student\OrderController@decline`

```php
namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class DeclineOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        return $order->student_profile_id === $this->user()->studentProfile->id
            && $order->status === 'pending';
    }

    public function rules(): array
    {
        return [
            'reason' => 'required|string|min:20|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.min' => 'Please provide a detailed reason (at least 20 characters).',
        ];
    }
}
```

---

### UploadDeliverablesRequest

**Route**: `POST /student/orders/{id}/deliverables`  
**Controller**: `Student\OrderController@uploadDeliverables`

```php
namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UploadDeliverablesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        return $order->student_profile_id === $this->user()->studentProfile->id
            && in_array($order->status, ['accepted', 'in_progress', 'revision_requested']);
    }

    public function rules(): array
    {
        return [
            'deliverable_files' => 'required|array|min:1|max:10',
            'deliverable_files.*' => 'file|mimes:jpg,jpeg,png,pdf,docx,zip|max:10240',
            'delivery_note' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'deliverable_files.required' => 'Please upload at least one deliverable file.',
            'deliverable_files.max' => 'You can upload up to 10 files.',
            'deliverable_files.*.max' => 'Each file must not exceed 10MB.',
        ];
    }
}
```

---

### WithdrawalRequest

**Route**: `POST /student/earnings/withdraw`  
**Controller**: `Student\EarningsController@storeWithdrawal`

```php
namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'student';
    }

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

    public function messages(): array
    {
        return [
            'amount.min' => 'Minimum withdrawal amount is $10.',
            'amount.max' => 'Withdrawal amount exceeds your available balance.',
            'account_number.required_if' => 'Bank account number is required for bank transfers.',
            'phone_number.required_if' => 'Phone number is required for mobile money withdrawals.',
        ];
    }
}
```

---

## Client Form Requests

### PlaceOrderRequest

**Route**: `POST /client/orders`  
**Controller**: `Client\OrderController@store`

```php
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
}
```

---

### RequestRevisionRequest

**Route**: `POST /client/orders/{id}/revision`  
**Controller**: `Client\OrderController@requestRevision`

```php
namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class RequestRevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        return $order->client_profile_id === $this->user()->clientProfile->id
            && $order->status === 'completed'
            && $order->revision_count < $order->max_revisions;
    }

    public function rules(): array
    {
        return [
            'revision_notes' => 'required|string|min:20|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'revision_notes.min' => 'Please provide detailed revision feedback (at least 20 characters).',
        ];
    }
}
```

---

### DisputeOrderRequest

**Route**: `POST /client/orders/{id}/dispute`  
**Controller**: `Client\OrderController@dispute`

```php
namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class DisputeOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        return $order->client_profile_id === $this->user()->clientProfile->id
            && $order->status === 'completed'
            && $order->revision_count >= $order->max_revisions;
    }

    public function rules(): array
    {
        return [
            'reason' => 'required|string|min:50|max:2000',
            'evidence_files' => 'nullable|array|max:5',
            'evidence_files.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.min' => 'Please provide a detailed explanation (at least 50 characters).',
            'evidence_files.max' => 'You can upload up to 5 evidence files.',
        ];
    }
}
```

---

### ReviewRequest

**Route**: `POST /client/reviews`  
**Controller**: `Client\ReviewController@store`

```php
namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        return $order->client_profile_id === $this->user()->clientProfile->id
            && $order->status === 'approved'
            && !$order->review()->exists();
    }

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

    public function messages(): array
    {
        return [
            'rating.required' => 'Please select a rating.',
            'rating.min' => 'Rating must be between 1 and 5 stars.',
            'review_text.min' => 'Review must be at least 20 characters if provided.',
            'tags.max' => 'You can select up to 5 tags.',
        ];
    }
}
```

---

## Messaging Form Requests

### MessageRequest

**Route**: `POST /messages`  
**Controller**: `MessageController@store`

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        $user = $this->user();
        
        return $order->client_profile_id === $user->clientProfile?->id
            || $order->student_profile_id === $user->studentProfile?->id;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'message' => 'required|string|min:1|max:5000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Message cannot be empty.',
            'message.max' => 'Message cannot exceed 5000 characters.',
            'attachment.max' => 'Attachment must not exceed 5MB.',
        ];
    }
}
```

---

## Admin Form Requests

### ResolveDisputeRequest

**Route**: `POST /admin/disputes/{id}/resolve`  
**Controller**: `Admin\DisputeController@resolve`

```php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResolveDisputeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'resolution' => 'required|in:release_to_student,refund_to_client,split',
            'student_amount' => 'required_if:resolution,split|numeric|min:0',
            'client_amount' => 'required_if:resolution,split|numeric|min:0',
            'admin_notes' => 'required|string|min:50|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'resolution.required' => 'Please select a resolution option.',
            'admin_notes.min' => 'Please provide detailed reasoning (at least 50 characters).',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->resolution === 'split') {
                $order = $this->route('order');
                $total = $this->student_amount + $this->client_amount;
                
                if ($total != $order->total_amount) {
                    $validator->errors()->add('student_amount', 'Split amounts must equal order total.');
                }
            }
        });
    }
}
```

---

## Common Validation Patterns

### File Upload Validation

```php
// Images only
'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'

// Documents
'document' => 'required|file|mimes:pdf,docx,doc|max:5120'

// Portfolio files (mixed)
'portfolio.*' => 'file|mimes:jpg,jpeg,png,pdf,docx|max:10240'

// Deliverables (including archives)
'deliverable.*' => 'file|mimes:jpg,jpeg,png,pdf,docx,zip|max:10240'
```

### Array Validation

```php
// Skills array
'skills' => 'nullable|array|max:10',
'skills.*' => 'string|max:50',

// Tags array
'tags' => 'nullable|array|max:5',
'tags.*' => 'string|in:professional,responsive,quality',

// Multiple file uploads
'files' => 'required|array|min:1|max:10',
'files.*' => 'file|mimes:jpg,png,pdf|max:5120',
```

### Conditional Validation

```php
// Required if another field has specific value
'account_number' => 'required_if:method,bank_transfer',
'phone_number' => 'required_if:method,mobile_money',

// Required unless another field has specific value
'price' => 'required_unless:pricing_model,negotiable',

// Greater than or equal to another field
'hourly_rate_max' => 'gte:hourly_rate_min',
```

---

## Custom Validation Rules

### Available Balance Validation

```php
use Illuminate\Contracts\Validation\Rule;

class AvailableBalance implements Rule
{
    public function passes($attribute, $value)
    {
        $studentProfile = auth()->user()->studentProfile;
        return $value <= $studentProfile->available_balance;
    }

    public function message()
    {
        return 'Withdrawal amount exceeds your available balance.';
    }
}

// Usage
'amount' => ['required', 'numeric', 'min:10', new AvailableBalance],
```

### Service Availability Validation

```php
use Illuminate\Contracts\Validation\Rule;

class ServiceIsActive implements Rule
{
    public function passes($attribute, $value)
    {
        $service = ServiceListing::find($value);
        return $service && $service->status === 'active';
    }

    public function message()
    {
        return 'This service is not currently available.';
    }
}

// Usage
'service_listing_id' => ['required', 'exists:service_listings,id', new ServiceIsActive],
```

---

## Error Message Customization

### Global Custom Messages

In `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Support\Facades\Validator;

public function boot()
{
    Validator::replacer('max', function ($message, $attribute, $rule, $parameters) {
        if ($attribute === 'profile_picture' || $attribute === 'attachment') {
            return str_replace(':max', $parameters[0] / 1024 . 'MB', $message);
        }
        return $message;
    });
}
```

### Attribute Name Customization

```php
public function attributes(): array
{
    return [
        'service_listing_id' => 'service',
        'requirements' => 'project requirements',
        'deliverable_files' => 'deliverable files',
        'hourly_rate_min' => 'minimum hourly rate',
        'hourly_rate_max' => 'maximum hourly rate',
    ];
}
```

---

## Form Request Best Practices

1. **Authorization First**: Always implement `authorize()` method to check permissions
2. **Specific Messages**: Provide user-friendly, actionable error messages
3. **Custom Validation**: Use `withValidator()` for complex cross-field validation
4. **Attribute Names**: Customize attribute names for better error messages
5. **File Size Display**: Convert bytes to MB in error messages for clarity
6. **Conditional Rules**: Use `required_if`, `required_unless` for conditional validation
7. **Array Validation**: Validate both array structure and individual items
8. **Database Checks**: Use `exists` rule to verify foreign key references
9. **Business Rules**: Implement custom validation rules for complex business logic
10. **Security**: Never trust client input, validate everything

---

**Next Steps**: Generate services.md with service layer contracts.
