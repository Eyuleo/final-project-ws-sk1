<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Graphic Design',
                'slug' => 'graphic-design',
                'description' => 'Logo design, branding, illustrations, and visual content creation',
                'icon' => 'palette',
                'sort_order' => 1,
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Website development, web applications, and frontend/backend coding',
                'icon' => 'code',
                'sort_order' => 2,
            ],
            [
                'name' => 'Mobile App Development',
                'slug' => 'mobile-app-development',
                'description' => 'iOS and Android app development, cross-platform solutions',
                'icon' => 'smartphone',
                'sort_order' => 3,
            ],
            [
                'name' => 'Content Writing',
                'slug' => 'content-writing',
                'description' => 'Blog posts, articles, copywriting, and creative writing',
                'icon' => 'pen-tool',
                'sort_order' => 4,
            ],
            [
                'name' => 'Translation',
                'slug' => 'translation',
                'description' => 'Document translation, localization, and language services',
                'icon' => 'languages',
                'sort_order' => 5,
            ],
            [
                'name' => 'Video Editing',
                'slug' => 'video-editing',
                'description' => 'Video production, editing, animation, and post-production',
                'icon' => 'video',
                'sort_order' => 6,
            ],
            [
                'name' => 'Digital Marketing',
                'slug' => 'digital-marketing',
                'description' => 'Social media marketing, SEO, email campaigns, and online advertising',
                'icon' => 'trending-up',
                'sort_order' => 7,
            ],
            [
                'name' => 'Data Entry',
                'slug' => 'data-entry',
                'description' => 'Data processing, spreadsheet work, and administrative tasks',
                'icon' => 'database',
                'sort_order' => 8,
            ],
            [
                'name' => 'Tutoring',
                'slug' => 'tutoring',
                'description' => 'Academic tutoring, test preparation, and online teaching',
                'icon' => 'book-open',
                'sort_order' => 9,
            ],
            [
                'name' => 'UI/UX Design',
                'slug' => 'ui-ux-design',
                'description' => 'User interface design, user experience research, and prototyping',
                'icon' => 'layout',
                'sort_order' => 10,
            ],
            [
                'name' => 'Photography',
                'slug' => 'photography',
                'description' => 'Photo shoots, image editing, and visual storytelling',
                'icon' => 'camera',
                'sort_order' => 11,
            ],
            [
                'name' => 'Virtual Assistant',
                'slug' => 'virtual-assistant',
                'description' => 'Administrative support, scheduling, email management, and organization',
                'icon' => 'user-check',
                'sort_order' => 12,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}
