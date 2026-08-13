<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * The default settings used when a setting has not been configured yet.
     *
     * @var array<string, array{key: string, value: mixed, group: string, label: string, type: string, options?: array<string, string>}>
     */
    public const DEFAULTS = [
        'general.site_name' => ['key' => 'general.site_name', 'value' => null, 'group' => 'general', 'label' => 'Site name', 'type' => 'text'],
        'general.tagline' => ['key' => 'general.tagline', 'value' => null, 'group' => 'general', 'label' => 'Tagline', 'type' => 'text'],
        'general.description' => ['key' => 'general.description', 'value' => null, 'group' => 'general', 'label' => 'Description', 'type' => 'textarea'],
        'general.email' => ['key' => 'general.email', 'value' => null, 'group' => 'general', 'label' => 'Email', 'type' => 'email'],
        'general.phone' => ['key' => 'general.phone', 'value' => null, 'group' => 'general', 'label' => 'Phone', 'type' => 'text'],
        'general.address' => ['key' => 'general.address', 'value' => null, 'group' => 'general', 'label' => 'Address', 'type' => 'textarea'],

        'branding.logo' => ['key' => 'branding.logo', 'value' => null, 'group' => 'branding', 'label' => 'Logo', 'type' => 'image'],
        'branding.favicon' => ['key' => 'branding.favicon', 'value' => null, 'group' => 'branding', 'label' => 'Favicon', 'type' => 'image'],
        'branding.footer_text' => ['key' => 'branding.footer_text', 'value' => null, 'group' => 'branding', 'label' => 'Footer text', 'type' => 'textarea'],

        'social.twitter' => ['key' => 'social.twitter', 'value' => null, 'group' => 'social', 'label' => 'Twitter / X', 'type' => 'url'],
        'social.facebook' => ['key' => 'social.facebook', 'value' => null, 'group' => 'social', 'label' => 'Facebook', 'type' => 'url'],
        'social.instagram' => ['key' => 'social.instagram', 'value' => null, 'group' => 'social', 'label' => 'Instagram', 'type' => 'url'],
        'social.linkedin' => ['key' => 'social.linkedin', 'value' => null, 'group' => 'social', 'label' => 'LinkedIn', 'type' => 'url'],
        'social.youtube' => ['key' => 'social.youtube', 'value' => null, 'group' => 'social', 'label' => 'YouTube', 'type' => 'url'],

        'seo.meta_title' => ['key' => 'seo.meta_title', 'value' => null, 'group' => 'seo', 'label' => 'Default meta title', 'type' => 'text'],
        'seo.meta_description' => ['key' => 'seo.meta_description', 'value' => null, 'group' => 'seo', 'label' => 'Default meta description', 'type' => 'textarea'],
        'seo.meta_keywords' => ['key' => 'seo.meta_keywords', 'value' => null, 'group' => 'seo', 'label' => 'Default meta keywords', 'type' => 'text'],

        'analytics.tracking_id' => ['key' => 'analytics.tracking_id', 'value' => null, 'group' => 'analytics', 'label' => 'Analytics tracking ID', 'type' => 'text'],
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'string',
        ];
    }
}
