<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class LandingpageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('landingpages', 'title')
                    /** @phpstan-ignore-next-line */
                    ->ignore($this->landingpage?->id)
                    ->whereNull('deleted_at'),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('landingpages', 'slug')
                    /** @phpstan-ignore-next-line */
                    ->ignore($this->landingpage?->id)
                    ->whereNull('deleted_at'),
            ],
            'headline' => 'required|string|max:255',
            'subline' => 'sometimes|nullable|string|max:255',
            'campaign_id' => 'sometimes|nullable|exists:campaigns,id',
            'sections' => 'required|array',
            // 'form_fields' => 'required|array',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $title = is_string($this->title) ? $this->title : '';
        $this->merge([
            'slug' => Str::slug($title),
        ]);
    }
}
