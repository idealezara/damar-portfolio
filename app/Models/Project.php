<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'category_id', 'title', 'slug', 'short_description', 'description',
        'thumbnail', 'technologies', 'project_url', 'github_url', 'is_published',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** "Laravel, PHP, MySQL" => ['Laravel', 'PHP', 'MySQL'] */
    public function getTechnologyListAttribute(): array
    {
        return collect(explode(',', (string) $this->technologies))
            ->map(fn ($tech) => trim($tech))
            ->filter()
            ->values()
            ->all();
    }

    public function getThumbnailUrlAttribute(): string
    {
        return Media::url($this->thumbnail, 'images/placeholder.svg');
    }

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'project';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
