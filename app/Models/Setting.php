<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Get a setting value by key from global settings
     */
    public static function get(string $key, $default = null)
    {
        $globalSettings = static::where('key', 'global_settings')->first();
        if (!$globalSettings) return $default;

        return $globalSettings->value[$key] ?? $default;
    }

    /**
     * Get the global settings record
     */
    public static function getGlobalSettings(): ?array
    {
        $setting = static::where('key', 'global_settings')->first();
        return $setting ? $setting->value : null;
    }

    /**
     * Set a global setting value
     */
    public static function setGlobalSetting(string $key, $value): void
    {
        $setting = static::firstOrNew(['key' => 'global_settings']);
        $currentValue = $setting->value ?? [];
        $currentValue[$key] = $value;
        $setting->value = $currentValue;
        $setting->save();
    }

    /**
     * Get site title
     */
    public static function getSiteTitle(): string
    {
        return static::get('site_title', 'My Website');
    }

    /**
     * Get logo image
     */
    public static function getLogoImage(): ?string
    {
        return static::get('logo_image');
    }

    /**
     * Get favicon
     */
    public static function getFavicon(): ?string
    {
        return static::get('favicon');
    }

    /**
     * Get primary navigation
     */
    public static function getPrimaryNavigation(): array
    {
        return static::get('primary_navigation', []);
    }

    /**
     * Get secondary navigation
     */
    public static function getSecondaryNavigation(): array
    {
        return static::get('secondary_navigation', []);
    }

    /**
     * Get footer text
     */
    public static function getFooterText(): string
    {
        return static::get('footer_text', '© 2024 All rights reserved.');
    }

    /**
     * Get footer links
     */
    public static function getFooterLinks(): array
    {
        return static::get('footer_links', []);
    }
}
