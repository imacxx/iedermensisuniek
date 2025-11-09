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
                'site_title' => 'Mijn Geweldige Website',
                'logo_image' => null,
                'favicon' => null,
                'primary_navigation' => [
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Over Ons', 'url' => '/about'],
                    ['label' => 'Diensten', 'url' => '/services'],
                    ['label' => 'Contact', 'url' => '/contact'],
                ],
                'secondary_navigation' => [
                    ['label' => 'Privacybeleid', 'url' => '/privacy'],
                    ['label' => 'Algemene Voorwaarden', 'url' => '/terms'],
                ],
                'footer_text' => '© 2024 Mijn Geweldige Website. Alle rechten voorbehouden.',
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
            'value' => ['name' => 'Mijn Geweldige Website']
        ]);

        Setting::create([
            'key' => 'site_description',
            'value' => ['description' => 'Een modern CMS gebouwd met Laravel en FilamentPHP']
        ]);

        Setting::create([
            'key' => 'seo_defaults',
            'value' => [
                'title' => 'Mijn Geweldige Website',
                'description' => 'Een modern CMS gebouwd met Laravel en FilamentPHP',
                'keywords' => 'laravel, filament, cms, website'
            ]
        ]);

        // Create sample Home page with blocks
        Page::create([
            'title' => 'Welkom bij Onze Website',
            'slug' => 'home',
            'blocks' => [
                [
                    'type' => 'hero',
                    'data' => [
                        'title' => 'Welkom bij Onze Website',
                        'subtitle' => 'Gebouwd met Laravel 12 en FilamentPHP',
                        'background_image' => null,
                    ],
                ],
                [
                    'type' => 'text',
                    'data' => [
                        'content' => '<p>Dit is een voorbeeldpagina met blok-gebaseerde inhoud. U kunt deze inhoud bewerken met onze API of het beheerpaneel.</p>',
                    ],
                ],
                [
                    'type' => 'features',
                    'data' => [
                        'title' => 'Kenmerken',
                        'items' => [
                            [
                                'title' => 'Laravel 12',
                                'description' => 'Nieuwste Laravel framework met moderne functies.',
                            ],
                            [
                                'title' => 'FilamentPHP',
                                'description' => 'Mooi beheerpaneel voor contentbeheer.',
                            ],
                            [
                                'title' => 'Tailwind CSS',
                                'description' => 'Modern utility-first CSS framework.',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'two_column',
                    'data' => [
                        'left_content' => '<h3>Waarom Voor Ons Kiezen?</h3><p>Wij bieden moderne, schaalbare oplossingen gebouwd met de nieuwste technologieën. Ons team van experts zorgt ervoor dat uw project op tijd wordt opgeleverd en de verwachtingen overtreft.</p><ul><li>Expert Ontwikkelingsteam</li><li>Moderne Technologie Stack</li><li>Betrouwbare Ondersteuning</li></ul>',
                        'right_content' => '<h3>Onze Diensten</h3><p>Van webontwikkeling tot mobiele apps, wij bieden uitgebreide digitale oplossingen op maat van uw bedrijfsbehoeften.</p><div class="bg-primary text-white p-4 rounded-lg mt-4"><strong>Klaar om uw project te starten?</strong><br>Neem vandaag nog contact met ons op voor een gratis consultatie.</div>',
                        'layout' => '50-50',
                    ],
                ],
                [
                    'type' => 'cta',
                    'data' => [
                        'title' => 'Klaar Om Te Beginnen?',
                        'subtitle' => 'Sluit je aan bij duizenden tevreden klanten en transformeer je bedrijf vandaag nog.',
                        'button_text' => 'Start Uw Project',
                        'button_url' => '/contact',
                        'background_color' => 'primary',
                    ],
                ],
            ],
            'meta_title' => 'Home - Onze Website',
            'meta_description' => 'Welkom bij onze website gebouwd met Laravel 12 en FilamentPHP.',
            'meta_keywords' => 'laravel, filament, cms, website',
            'is_published' => true,
        ]);
    }
}
