<?php

namespace App\Models;

use App\Traits\HasCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Setting extends Model
{
    use HasCache;

    public const FIELD_STRING = 'string';
    public const FIELD_NUMBER = 'integer';
    public const FIELD_BOOLEAN = 'boolean';

    protected $fillable = [
        'key', 'value', 'type',
        'description',
        'active',
    ];

    public static function getFields(): Collection
    {
        return new Collection([
            ['id' => static::FIELD_STRING, 'name' => 'Строка'],
            ['id' => static::FIELD_NUMBER, 'name' => 'Число'],
            ['id' => static::FIELD_BOOLEAN, 'name' => 'Boolean'],
        ]);
    }

    protected function casts(): array
    {
        return [
            'active' => 'bool',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getValueAttribute($value = null)
    {
        if (is_null($value)) {
            return $value;
        }

        if (is_null($this->type)) {
            return $value;
        }

        return cast_value($this->type, $value);
    }

    public static function getCached(): Collection
    {
        $key = 'settings';

        $date = now()->addHour();

        return static::cache()->remember($key, $date, function () {
            return static::query()->active()->get();
        });
    }

    public static function findValue(string $key, $default = null)
    {
        $settings = static::getCached();

        $setting = $settings->firstWhere('key', $key);

        $value = optional($setting)->value;

        return is_null($value) ? $default : $value;
    }
}
