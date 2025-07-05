<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class CampaignRequest extends FormRequest
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
                Rule::unique('campaigns', 'title')
                    /** @phpstan-ignore-next-line */
                    ->ignore($this->campaign?->id)
                    ->whereNull('deleted_at'),
            ],
            'description' => 'sometimes|string|max:255',
            'status' => 'required|string|in:'.implode(',', \App\Enums\CampaignStatus::values()),
            'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date|after:start_date',
            'prospect_filter' => 'sometimes|array',
        ];
    }
}
