<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(protected SettingsService $settings)
    {
    }

    /**
     * Display the settings management screen.
     */
    public function index(): View
    {
        $this->authorize('manage-settings');

        $values = $this->settings->all();

        $groups = collect(Setting::DEFAULTS)
            ->groupBy('group')
            ->map(function ($settings, $group) use ($values) {
                return [
                    'label' => ucfirst($group),
                    'settings' => $settings->map(fn ($meta) => [
                        ...$meta,
                        'value' => $values[$meta['key']] ?? $meta['value'],
                    ]),
                ];
            });

        return view('admin.settings.index', ['groups' => $groups]);
    }

    /**
     * Update the site settings.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $this->authorize('manage-settings');

        $values = [];

        foreach (Arr::dot($request->input('settings', [])) as $key => $value) {
            if (! array_key_exists($key, Setting::DEFAULTS) || is_array($value)) {
                continue;
            }

            $values[$key] = $value;
        }

        foreach (['branding.logo', 'branding.favicon'] as $key) {
            if ($request->hasFile('settings.'.$key)) {
                $file = $request->file('settings.'.$key);

                $old = $this->settings->get($key);

                if ($old) {
                    Storage::disk('public')->delete($old);
                }

                $values[$key] = $file->store('settings', 'public');
            }
        }

        $this->settings->set($values, $request->input('group', 'general'));

        return Redirect::route('admin.settings.index', ['tab' => $request->input('group')])
            ->with('status', 'Settings saved.');
    }
}
