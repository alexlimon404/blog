<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);
        $isPublished = fake()->boolean(70);
        
        return [
            'title' => rtrim($title, '.'),
            'slug' => str($title)->slug(),
            'content' => fake()->paragraphs(5, true),
            'excerpt' => fake()->paragraph(2),
            'category_id' => Category::factory(),
            'author_id' => Author::factory(),
            'is_published' => $isPublished,
            'published_at' => $isPublished ? fake()->dateTimeBetween('-1 year', 'now') : null,
        ];
    }
}
