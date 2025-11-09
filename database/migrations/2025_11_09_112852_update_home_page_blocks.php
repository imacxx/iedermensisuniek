<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $homePage = \App\Models\Page::where('slug', 'home')->first();

        if ($homePage) {
            $homePage->update([
                'blocks' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'title' => 'Welcome to Our Website',
                            'subtitle' => 'Built with Laravel 12 and FilamentPHP'
                        ]
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<p>This is a sample page with block-based content. You can edit this content using our API or admin panel.</p>'
                        ]
                    ],
                    [
                        'type' => 'features',
                        'data' => [
                            'title' => 'Features',
                            'items' => [
                                ['title' => 'Laravel 12', 'description' => 'Latest Laravel framework with modern features.'],
                                ['title' => 'FilamentPHP', 'description' => 'Beautiful admin panel for content management.'],
                                ['title' => 'Tailwind CSS', 'description' => 'Modern utility-first CSS framework.']
                            ]
                        ]
                    ],
                    [
                        'type' => 'two_column',
                        'data' => [
                            'left_content' => '<h3>Why Choose Us?</h3><p>We provide modern, scalable solutions built with the latest technologies. Our team of experts ensures your project is delivered on time and exceeds expectations.</p><ul><li>Expert Development Team</li><li>Modern Technology Stack</li><li>Reliable Support</li></ul>',
                            'right_content' => '<h3>Our Services</h3><p>From web development to mobile apps, we offer comprehensive digital solutions tailored to your business needs.</p><div class="bg-primary text-white p-4 rounded-lg mt-4"><strong>Ready to start your project?</strong><br>Contact us today for a free consultation.</div>',
                            'layout' => '50-50'
                        ]
                    ],
                    [
                        'type' => 'cta',
                        'data' => [
                            'title' => 'Ready to Get Started?',
                            'subtitle' => 'Join thousands of satisfied customers and transform your business today.',
                            'button_text' => 'Start Your Project',
                            'button_url' => '/contact',
                            'background_color' => 'primary'
                        ]
                    ]
                ]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            //
        });
    }
};
