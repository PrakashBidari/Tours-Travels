<?php

namespace App\Support;

use App\Models\PageSetting;
use Illuminate\Support\Facades\Schema;

/**
 * Company details shown across the site. Values an admin saved under Page Settings
 * win; anything left blank falls back to config/travel.php.
 */
class Site
{
    protected static ?array $resolved = null;

    /** Setting key => page_settings column that overrides it. */
    protected const OVERRIDES = [
        'name' => 'company_name',
        'email' => 'contact_email',
        'phone' => 'contact_phone',
        'mobile' => 'contact_mobile',
        'whatsapp' => 'whatsapp_number',
        'address' => 'contact_address',
        'office_hours' => 'office_hours',
        'map_embed' => 'map_embed_url',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all()[$key] ?? $default;
    }

    public static function all(): array
    {
        if (static::$resolved !== null) {
            return static::$resolved;
        }

        $values = config('travel.company');

        try {
            $settings = Schema::hasTable('page_settings') ? PageSetting::current() : null;
        } catch (\Throwable) {
            $settings = null;
        }

        foreach (static::OVERRIDES as $key => $column) {
            if ($settings && filled($settings->{$column} ?? null)) {
                $values[$key] = $settings->{$column};
            }
        }

        $values['whatsapp'] = preg_replace('/\D/', '', (string) $values['whatsapp']);
        $values['phone_href'] = 'tel:'.preg_replace('/[^\d+]/', '', (string) $values['phone']);
        $values['mobile_href'] = 'tel:'.preg_replace('/[^\d+]/', '', (string) $values['mobile']);

        return static::$resolved = $values;
    }

    /** Forget the per-request cache (after an admin saves settings). */
    public static function flush(): void
    {
        static::$resolved = null;
    }
}
