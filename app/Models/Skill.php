<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    /**
     * Allowed Font Awesome (Free) class format: ONE style prefix + ONE icon name.
     * Examples: "fa-brands fa-php", "fa-solid fa-database", "fas fa-code".
     * Anything else (extra classes, quotes, tags, pro styles such as fa-light) is rejected.
     */
    public const ICON_PATTERN = '/^(?:fa-(?:solid|regular|brands)|fa[srb]) fa-[a-z0-9]+(?:-[a-z0-9]+)*$/D';

    public const FALLBACK_ICON = 'fa-solid fa-code';

    public const LEVELS = ['Beginner', 'Intermediate', 'Advanced', 'Expert'];

    /** Suggestions shown in the admin form. */
    public const ICON_SUGGESTIONS = [
        'fa-brands fa-php' => 'PHP',
        'fa-brands fa-laravel' => 'Laravel',
        'fa-brands fa-js' => 'JavaScript',
        'fa-brands fa-html5' => 'HTML5',
        'fa-brands fa-css3-alt' => 'CSS3',
        'fa-brands fa-git-alt' => 'Git',
        'fa-solid fa-database' => 'MySQL / database',
    ];

    // "icon" is the legacy column from the first version; it is kept so no data is lost.
    protected $fillable = ['name', 'icon', 'icon_class', 'category', 'level', 'sort_order'];

    /** Icon class that is safe to print: re-validated on output, with a fallback. */
    public function getSafeIconClassAttribute(): string
    {
        $class = (string) $this->icon_class;

        return preg_match(self::ICON_PATTERN, $class) === 1 ? $class : self::FALLBACK_ICON;
    }
}
