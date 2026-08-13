<?php

namespace Tests\Feature\Blog;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_access_the_settings_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Settings')
            ->assertSee('General')
            ->assertSee('Branding');
    }

    public function test_editors_cannot_access_the_settings_page(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.settings.index'))
            ->assertForbidden();
    }

    public function test_admins_can_update_text_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), [
                'group' => 'general',
                'settings' => [
                    'general' => [
                        'site_name' => 'The Daily Scroll',
                        'tagline' => 'Stories worth reading',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.settings.index', ['tab' => 'general']))
            ->assertSessionHas('status', 'Settings saved.');

        $this->assertDatabaseHas('settings', [
            'key' => 'general.site_name',
            'value' => 'The Daily Scroll',
        ]);

        $this->assertSame('The Daily Scroll', setting('general.site_name'));
    }

    public function test_admins_can_update_social_and_seo_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), [
                'group' => 'social',
                'settings' => [
                    'social' => [
                        'twitter' => 'https://twitter.com/blog',
                        'instagram' => 'https://instagram.com/blog',
                    ],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Settings saved.');

        $this->assertDatabaseHas('settings', [
            'key' => 'social.twitter',
            'value' => 'https://twitter.com/blog',
        ]);
        $this->assertSame('https://twitter.com/blog', setting('social.twitter'));
    }

    public function test_admins_can_upload_a_logo(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), [
                'group' => 'branding',
                'settings' => [
                    'branding' => [
                        'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
                        'footer_text' => 'Made with care',
                    ],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Settings saved.');

        $logo = Setting::where('key', 'branding.logo')->first();

        $this->assertNotNull($logo);
        $this->assertStringStartsWith('settings/', $logo->value);
        Storage::disk('public')->assertExists($logo->value);

        $this->assertSame('Made with care', setting('branding.footer_text'));
    }

    public function test_invalid_setting_keys_are_ignored(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), [
                'group' => 'general',
                'settings' => [
                    'general' => ['site_name' => 'Safe Value'],
                    'system' => ['secret' => 'should be ignored'],
                    'nested' => ['evil' => 'also ignored'],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('settings', ['key' => 'system.secret']);
        $this->assertDatabaseMissing('settings', ['key' => 'nested.evil']);
        $this->assertSame('Safe Value', setting('general.site_name'));
    }

    public function test_invalid_url_setting_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), [
                'group' => 'social',
                'settings' => [
                    'social' => ['twitter' => 'not-a-url'],
                ],
            ])
            ->assertSessionHasErrors('settings.social.twitter');

        $this->assertDatabaseMissing('settings', ['key' => 'social.twitter']);
    }

    public function test_site_name_from_settings_is_rendered_in_the_layout(): void
    {
        Setting::create(['key' => 'general.site_name', 'value' => 'Renamed Blog', 'group' => 'general']);
        $this->app->make(\App\Services\SettingsService::class)->flush();

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Renamed Blog');
    }
}
