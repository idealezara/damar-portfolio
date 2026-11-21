<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProfileSetting extends Model
{
    /** Used by the Hero only when hero_description is empty (never falls back to the biography). */
    public const DEFAULT_HERO_DESCRIPTION = 'I build modern and dynamic web applications using Laravel and PHP.';

    protected $fillable = [
        'full_name', 'title', 'hero_description', 'biography', 'motivation', 'email',
        'github_url', 'instagram_url', 'profile_image',
        'availability_enabled', 'availability_text',
    ];

    protected function casts(): array
    {
        return ['availability_enabled' => 'boolean'];
    }

    /** Initial content. Everything here can be edited in Admin > Profile. */
    public static function defaults(): array
    {
        return [
            'full_name' => 'Damar Ernest',
            'title' => 'Coding',
            'hero_description' => self::DEFAULT_HERO_DESCRIPTION,
            'biography' => 'I build modern and dynamic web applications using Laravel and PHP.',
            'motivation' => 'just persevere',
            'email' => 'damarernest5009@gmail.com',
            'github_url' => 'https://github.com/idealezara',
            'instagram_url' => 'https://www.instagram.com/dmrenst',
            'availability_enabled' => true,
            'availability_text' => 'Available for projects',
        ];
    }

    /** The single profile row (created on first use). */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], static::defaults());
    }

    /** Text for the Hero section. The About section uses "biography" instead. */
    public function getHeroTextAttribute(): string
    {
        return filled($this->hero_description) ? $this->hero_description : self::DEFAULT_HERO_DESCRIPTION;
    }

    public function getFirstNameAttribute(): string
    {
        return Str::of(trim($this->full_name))->before(' ')->toString();
    }

    public function getLastNameAttribute(): string
    {
        $name = trim($this->full_name);

        return str_contains($name, ' ') ? trim(Str::after($name, ' ')) : '';
    }

    public function getInitialsAttribute(): string
    {
        return collect(preg_split('/\s+/', trim($this->full_name)))
            ->filter()
            ->take(2)
            ->map(fn ($word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');
    }

    public function getPortraitUrlAttribute(): string
    {
        return Media::url($this->profile_image, 'images/portrait-default.png');
    }
}
