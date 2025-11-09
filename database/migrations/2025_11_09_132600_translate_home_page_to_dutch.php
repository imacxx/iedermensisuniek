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
                'title' => 'Welkom bij Onze Website',
                'blocks' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'title' => 'Welkom bij Onze Website',
                            'subtitle' => 'Gebouwd met Laravel 12 en FilamentPHP'
                        ]
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<p>Dit is een voorbeeldpagina met blok-gebaseerde inhoud. U kunt deze inhoud bewerken met onze API of het beheerpaneel.</p>'
                        ]
                    ],
                    [
                        'type' => 'features',
                        'data' => [
                            'title' => 'Kenmerken',
                            'items' => [
                                ['title' => 'Laravel 12', 'description' => 'Nieuwste Laravel framework met moderne functies.'],
                                ['title' => 'FilamentPHP', 'description' => 'Mooi beheerpaneel voor contentbeheer.'],
                                ['title' => 'Tailwind CSS', 'description' => 'Modern utility-first CSS framework.']
                            ]
                        ]
                    ],
                    [
                        'type' => 'two_column',
                        'data' => [
                            'left_content' => '<h3>Waarom Voor Ons Kiezen?</h3><p>Wij bieden moderne, schaalbare oplossingen gebouwd met de nieuwste technologieën. Ons team van experts zorgt ervoor dat uw project op tijd wordt opgeleverd en de verwachtingen overtreft.</p><ul><li>Expert Ontwikkelingsteam</li><li>Moderne Technologie Stack</li><li>Betrouwbare Ondersteuning</li></ul>',
                            'right_content' => '<h3>Onze Diensten</h3><p>Van webontwikkeling tot mobiele apps, wij bieden uitgebreide digitale oplossingen op maat van uw bedrijfsbehoeften.</p><div class="bg-primary text-white p-4 rounded-lg mt-4"><strong>Klaar om uw project te starten?</strong><br>Neem vandaag nog contact met ons op voor een gratis consultatie.</div>',
                            'layout' => '50-50'
                        ]
                    ],
                    [
                        'type' => 'cta',
                        'data' => [
                            'title' => 'Klaar Om Te Beginnen?',
                            'subtitle' => 'Sluit je aan bij duizenden tevreden klanten en transformeer je bedrijf vandaag nog.',
                            'button_text' => 'Start Uw Project',
                            'button_url' => '/contact',
                            'background_color' => 'primary'
                        ]
                    ]
                ],
                'meta_title' => 'Home - Onze Website',
                'meta_description' => 'Welkom bij onze website gebouwd met Laravel 12 en FilamentPHP.',
            ]);
        }
        
        // Update settings
        $globalSettings = \App\Models\Setting::where('key', 'global_settings')->first();
        if ($globalSettings) {
            $value = $globalSettings->value;
            $value['site_title'] = 'Mijn Geweldige Website';
            $value['primary_navigation'] = [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Over Ons', 'url' => '/about'],
                ['label' => 'Diensten', 'url' => '/services'],
                ['label' => 'Contact', 'url' => '/contact'],
            ];
            $value['secondary_navigation'] = [
                ['label' => 'Privacybeleid', 'url' => '/privacy'],
                ['label' => 'Algemene Voorwaarden', 'url' => '/terms'],
            ];
            $value['footer_text'] = '© 2024 Mijn Geweldige Website. Alle rechten voorbehouden.';
            $globalSettings->update(['value' => $value]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible
    }
};

