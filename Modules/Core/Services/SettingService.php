<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Setting;

class SettingService
{
    public function get($key, $default = null)
    {
        return Setting::where('key', $key)->value('value') ?? $default;
    }

    public function set($key, $value, $group = null)
    {
        return Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }
}
