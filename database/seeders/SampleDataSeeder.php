<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample categories
        $webDevelopment = Category::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'description' => 'Articles about web development technologies and best practices.',
        ]);

        $laravel = Category::create([
            'name' => 'Laravel',
            'slug' => 'laravel',
            'description' => 'Laravel framework tutorials and tips.',
        ]);

        // Create sample blog post
        $blog = Blog::create([
            'title' => 'Getting Started with Laravel 12',
            'slug' => 'getting-started-with-laravel-12',
            'excerpt' => 'Learn about the new features and improvements in Laravel 12.',
            'body' => '<p>Laravel 12 brings exciting new features to the framework...</p>',
            'published_at' => now(),
            'is_published' => true,
            'seo_title' => 'Getting Started with Laravel 12',
            'seo_description' => 'Learn about the new features and improvements in Laravel 12.',
            'meta_keywords' => 'laravel, framework, php, tutorial',
        ]);

        // Attach categories to the blog
        $blog->categories()->attach([$laravel->id, $webDevelopment->id]);

        // Create global settings
        Setting::updateOrCreate(
            ['key' => 'global_settings'],
            ['value' => [
                'site_title' => 'Mijn Geweldige Website',
                'logo_image' => '/images/logo.png',
                'favicon' => null,
                'primary_navigation' => [
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Tarieven', 'url' => '/tarieven'],
                    ['label' => 'Klassieke homeopathie', 'url' => '/klassieke-homeopathie'],
                    ['label' => 'Cursus eerste hulp', 'url' => '/cursus-eerste-hulp'],
                    ['label' => 'EMDR', 'url' => '/emdr'],
                    ['label' => 'Contact', 'url' => '/contact'],
                ],
                'secondary_navigation' => [
                    ['label' => 'Privacybeleid', 'url' => '/privacy'],
                    ['label' => 'Algemene Voorwaarden', 'url' => '/terms'],
                    ['label' => 'Handige links', 'url' => '/handige-links'],
                ],
                'footer_text' => '© 2024 Mijn Geweldige Website. Alle rechten voorbehouden.',
                'footer_links' => [],
            ]]
        );

        // Keep existing settings for backwards compatibility
        Setting::updateOrCreate(
            ['key' => 'site_name'],
            ['value' => ['name' => 'Mijn Geweldige Website']]
        );

        Setting::updateOrCreate(
            ['key' => 'site_description'],
            ['value' => ['description' => 'Een modern CMS gebouwd met Laravel en FilamentPHP']]
        );

        Setting::updateOrCreate(
            ['key' => 'seo_defaults'],
            ['value' => [
                'title' => 'Mijn Geweldige Website',
                'description' => 'Een modern CMS gebouwd met Laravel en FilamentPHP',
                'keywords' => 'laravel, filament, cms, website'
            ]]
        );

        // Create placeholder pages for primary navigation
        $pages = [
            ['title' => 'Home', 'slug' => 'home'],
            ['title' => 'Tarieven', 'slug' => 'tarieven'],
            ['title' => 'Klassieke homeopathie', 'slug' => 'klassieke-homeopathie'],
            ['title' => 'Cursus eerste hulp', 'slug' => 'cursus-eerste-hulp'],
            ['title' => 'EMDR', 'slug' => 'emdr'],
            ['title' => 'Contact', 'slug' => 'contact'],
            ['title' => 'Privacy', 'slug' => 'privacy'],
            ['title' => 'Handige links', 'slug' => 'handige-links'],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'blocks' => [],
                    'meta_title' => $page['title'],
                    'meta_description' => null,
                    'meta_keywords' => null,
                    'is_published' => true,
                ]
            );
        }
    }
}
