<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $keys = [
            'company_name',
            'company_address',
            'company_email',
            'company_phone',
            'company_website',
            'company_tax_id',
            'company_gstin',
            'company_logo',
            'invoice_prefix',
            'invoice_number_length',
            'invoice_next_number',
            'invoice_header',
            'invoice_footer',
        ];

        $settings = Setting::getMany($keys);

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:255'],
            'company_website' => ['nullable', 'string', 'max:255'],
            'company_tax_id' => ['nullable', 'string', 'max:255'],
            'company_gstin' => ['nullable', 'string', 'max:30'],
            'company_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'invoice_prefix' => ['nullable', 'string', 'max:50'],
            'invoice_number_length' => ['nullable', 'integer', 'min:3', 'max:12'],
            'invoice_next_number' => ['nullable', 'integer', 'min:1'],
            'invoice_header' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'invoice_footer' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $fileKeys = ['company_logo', 'invoice_header', 'invoice_footer'];

        foreach ($data as $key => $value) {
            if (in_array($key, $fileKeys, true)) {
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $oldPath = Setting::get($key);
                    if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                    Setting::set($key, $value->store('settings', 'public'));
                }
            } else {
                Setting::set($key, $value);
            }
        }

        // Handle explicit removals
        $removals = [
            'remove_company_logo' => 'company_logo',
            'remove_invoice_header' => 'invoice_header',
            'remove_invoice_footer' => 'invoice_footer',
        ];

        foreach ($removals as $flag => $settingKey) {
            if ($request->boolean($flag)) {
                $oldPath = Setting::get($settingKey);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                Setting::set($settingKey, null);
            }
        }

        return redirect()
            ->route('settings.index')
            ->with('status', 'Settings updated successfully.');
    }
}

