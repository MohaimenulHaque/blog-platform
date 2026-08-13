<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-settings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'group' => ['required', 'string', 'in:general,branding,social,seo,analytics'],
            'settings' => ['nullable', 'array'],

            'settings.general.site_name' => ['nullable', 'string', 'max:255'],
            'settings.general.tagline' => ['nullable', 'string', 'max:255'],
            'settings.general.description' => ['nullable', 'string', 'max:1000'],
            'settings.general.email' => ['nullable', 'email', 'max:255'],
            'settings.general.phone' => ['nullable', 'string', 'max:50'],
            'settings.general.address' => ['nullable', 'string', 'max:500'],

            'settings.branding.logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp,svg', 'max:2048'],
            'settings.branding.favicon' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp,svg,ico', 'max:512'],
            'settings.branding.footer_text' => ['nullable', 'string', 'max:1000'],

            'settings.social.twitter' => ['nullable', 'url', 'max:255'],
            'settings.social.facebook' => ['nullable', 'url', 'max:255'],
            'settings.social.instagram' => ['nullable', 'url', 'max:255'],
            'settings.social.linkedin' => ['nullable', 'url', 'max:255'],
            'settings.social.youtube' => ['nullable', 'url', 'max:255'],

            'settings.seo.meta_title' => ['nullable', 'string', 'max:255'],
            'settings.seo.meta_description' => ['nullable', 'string', 'max:1000'],
            'settings.seo.meta_keywords' => ['nullable', 'string', 'max:500'],

            'settings.analytics.tracking_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
