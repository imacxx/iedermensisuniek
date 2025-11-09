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
        Setting::create([
            'key' => 'global_settings',
            'value' => [
                'site_title' => 'My Awesome Site',
                'logo_image' => null,
                'favicon' => null,
                'primary_navigation' => [
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'About', 'url' => '/about'],
                    ['label' => 'Services', 'url' => '/services'],
                    ['label' => 'Contact', 'url' => '/contact'],
                ],
                'secondary_navigation' => [
                    ['label' => 'Privacy Policy', 'url' => '/privacy'],
                    ['label' => 'Terms of Service', 'url' => '/terms'],
                ],
                'footer_text' => '© 2024 My Awesome Site. All rights reserved.',
                'footer_links' => [
                    ['label' => 'Facebook', 'url' => 'https://facebook.com'],
                    ['label' => 'Twitter', 'url' => 'https://twitter.com'],
                    ['label' => 'LinkedIn', 'url' => 'https://linkedin.com'],
                ],
            ]
        ]);

        // Keep existing settings for backwards compatibility
        Setting::create([
            'key' => 'site_name',
            'value' => ['name' => 'My Awesome Site']
        ]);

        Setting::create([
            'key' => 'site_description',
            'value' => ['description' => 'A modern CMS built with Laravel and FilamentPHP']
        ]);

        Setting::create([
            'key' => 'seo_defaults',
            'value' => [
                'title' => 'My Awesome Site',
                'description' => 'A modern CMS built with Laravel and FilamentPHP',
                'keywords' => 'laravel, filament, cms, website'
            ]
        ]);

        // Create sample Home page with blocks
        Page::create([
            'title' => 'Welcome to Our Site',
            'slug' => 'home',
            'blocks' => [
                [
                    'type' => 'hero',
                    'data' => [
                        'title' => 'Welcome to Our Website',
                        'subtitle' => 'Built with Laravel 12 and FilamentPHP',
                        'background_image' => null,
                    ],
                ],
                [
                    'type' => 'text',
                    'data' => [
                        'content' => '<p>This is a sample page with block-based content. You can edit this content using our API or admin panel.</p>',
                    ],
                ],
                [
                    'type' => 'features',
                    'data' => [
                        'title' => 'Features',
                        'items' => [
                            [
                                'title' => 'Laravel 12',
                                'description' => 'Latest Laravel framework with modern features.',
                            ],
                            [
                                'title' => 'FilamentPHP',
                                'description' => 'Beautiful admin panel for content management.',
                            ],
                            [
                                'title' => 'Tailwind CSS',
                                'description' => 'Modern utility-first CSS framework.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'two_column',
                'data' => [
                    'left_content' => '<h3>Why Choose Us?</h3><p>We provide modern, scalable solutions built with the latest technologies. Our team of experts ensures your project is delivered on time and exceeds expectations.</p><ul><li>Expert Development Team</li><li>Modern Technology Stack</li><li>Reliable Support</li></ul>',
                    'right_content' => '<h3>Our Services</h3><p>From web development to mobile apps, we offer comprehensive digital solutions tailored to your business needs.</p><div class="bg-primary text-white p-4 rounded-lg mt-4"><strong>Ready to start your project?</strong><br>Contact us today for a free consultation.</div>',
                    'layout' => '50-50',
                ],
            ],
            [
                'type' => 'cta',
                'data' => [
                    'title' => 'Ready to Get Started?',
                    'subtitle' => 'Join thousands of satisfied customers and transform your business today.',
                    'button_text' => 'Start Your Project',
                    'button_url' => '/contact',
                    'background_color' => 'primary',
                ],
            ],
            'meta_title' => 'Home - Our Website',
            'meta_description' => 'Welcome to our website built with Laravel 12 and FilamentPHP.',
            'meta_keywords' => 'laravel, filament, cms, website',
        ]);
    }
}
