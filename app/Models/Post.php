<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $uuid
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string|null $excerpt
 * @property string|null $description
 * @property int $base_prompt_id
 * @property int|null $category_id
 * @property int|null $author_id
 * @property \Carbon\Carbon|null $published_at
 * @property string|null $driver
 * @property string|null $model
 *
 * @property-read Category|null $category
 * @property-read Author|null $author
 * @property-read BasePrompt $basePrompt
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 */
class Post extends Model
{
    use HasFactory, HasStatus;

    protected $fillable = [
        'uuid',
        'title', 'slug',
        'content', 'excerpt',
        'description',
        'base_prompt_id', 'category_id', 'author_id',
        'published_at',
        'driver', 'model',
        'status', 'status_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'status_at' => 'datetime',
    ];

    const STATUS_CREATED = 'created';
    const STATUS_COMPLETED = 'completed';
    const STATUS_GENERATE = 'generate';
    const STATUS_REGENERATE = 'regenerate';
    const STATUS_ERROR = 'error';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
                $post->uuid = Str::uuid();
            }
        });

        static::updating(function ($post) {
            if (empty($post->slug) || $post->isDirty('title')) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public static function getStatuses(): Collection
    {
        return new Collection([
            ['id' => static::STATUS_COMPLETED, 'name' => __('Завершено'), 'color' => 'success'],
            ['id' => static::STATUS_CREATED, 'name' => __('Создано'), 'color' => 'grey'],
            ['id' => static::STATUS_GENERATE, 'name' => __('Генерируется'), 'color' => 'warning'],
            ['id' => static::STATUS_REGENERATE, 'name' => __('Регенерируется'), 'color' => 'grey'],
            ['id' => static::STATUS_ERROR, 'name' => __('Ошибка'), 'color' => 'danger'],
        ]);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function basePrompt(): BelongsTo
    {
        return $this->belongsTo(BasePrompt::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->published_at && $this->published_at <= now();
    }
}
