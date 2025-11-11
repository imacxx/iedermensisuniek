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

        // Build modern homepage content blocks
        $homeBlocks = [
            [
                'type' => 'hero',
                'data' => [
                    'title' => 'Praktijk voor klassieke homeopathie in Woudenberg',
                    'subtitle' => 'Persoonlijke homeopathie en EMDR-begeleiding voor volwassenen en kinderen.',
                    'background_image' => '/images/hero-pattern.svg',
                    'eyebrow' => 'Homeopathie & EMDR',
                    'button_text' => 'Maak een afspraak',
                    'button_url' => '/contact',
                ],
            ],
            [
                'type' => 'two_column',
                'data' => [
                    'layout' => '50-50',
                    'eyebrow' => 'Over de praktijk',
                    'left_content' => <<<'HTML'
<h2>Waarom ieder mens uniek is</h2>
<p>De praktijk voor klassieke homeopathie 'Ieder mens is uniek' bestaat sinds 2006 en verhuisde in 2019 van Hoevelaken / Vathorst naar Woudenberg.</p>
<p>We zijn geopend van maandag tot en met vrijdag, met op verzoek consulten in de avond of op zaterdag. De praktijk is volledig rolstoeltoegankelijk en er is de mogelijkheid om een telefonisch (beeld) consult te plannen via een veilige verbinding.</p>
<p>Je maakt altijd telefonisch een afspraak voor een consult. Laat gerust een bericht achter met naam en telefoonnummer wanneer ik even niet kan opnemen; ik bel je zo snel mogelijk terug.</p>
HTML,
                    'right_content' => <<<'HTML'
<p><strong>Telefoon:</strong> <a href="tel:0636176672">06-36176672</a><br>
<strong>E-mail:</strong> <a href="mailto:info@iedermensisuniek.nl">info@iedermensisuniek.nl</a></p>
<p>Tijdens weekenden en vakanties wordt de praktijk waargenomen door een geregistreerd NVKH-collega klassiek homeopaat. Je wordt hierover ge&iuml;nformeerd via het antwoordapparaat, tijdens een consult of via de website.</p>
HTML,
                    'right_variant' => 'card',
                    'right_title' => 'Direct contact',
                    'right_button_text' => 'Plan een consult',
                    'right_button_link' => '/contact',
                ],
            ],
            [
                'type' => 'text',
                'data' => [
                    'background_style' => 'bg-gradient-to-r from-neutral-100 via-white to-primary/15',
                    'max_width' => 'max-w-4xl',
                    'padding_class' => 'px-4 py-16 sm:px-6 lg:px-10',
                    'eyebrow' => 'Vergoeding',
                    'content' => <<<'HTML'
<h2>Zorgverzekering</h2>
<p>Consulten bij 'Ieder mens is uniek' worden geheel of gedeeltelijk vergoed door de aanvullende verzekering, omdat de praktijk is aangesloten bij de NVKH, de Nederlandse Vereniging van Klassiek Homeopaten. Als erkend register homeopaat (R-Hom&reg;) mogen deze consulten gedeclareerd worden.</p>
<ul class="space-y-2">
    <li>Klassieke homeopathie valt onder alternatieve geneeswijzen binnen het aanvullende pakket.</li>
    <li>Het eigen risico geldt voor de basisverzekering en heeft geen invloed op de vergoeding van een homeopathisch consult.</li>
    <li>De kosten van het consult worden geheel of gedeeltelijk vergoed vanuit de aanvullende verzekering.</li>
</ul>
HTML,
                ],
            ],
            [
                'type' => 'two_column',
                'data' => [
                    'layout' => '67-33',
                    'eyebrow' => 'Over mij',
                    'left_content' => <<<'HTML'
<h2>Even voorstellen</h2>
<p>Welkom op mijn website. Ik ben Yvonne Arnaud de Calavon en werk sinds 2000 als klassiek homeopaat. Na jarenlange ervaring in Hoevelaken en Amersfoort (Oranjekliniek Vathorst) is de praktijk sinds 2019 gevestigd in het mooie Woudenberg.</p>
<p>Ik behandel zowel volwassenen als kinderen met acute en/of chronische klachten. Iedere behandeling is afgestemd op de unieke mens: lichaam en psyche worden als geheel benaderd. Dat is de kern waar de praktijk voor staat.</p>
<p>Mijn belangstelling ontstond door positieve ervaringen met homeopathie bij mijn dochter en bij mezelf. Dit motiveerde mij om de zesjarige opleiding tot klassiek homeopaat te volgen. Daarnaast studeerde ik orthomoleculaire geneeskunde, volgde ik de opleiding 'Homeopathie en verloskunde' en behaalde ik in 2016 het diploma kindercoach. Naast mijn homeopathische werk ben ik ook gediplomeerd EMDR-practitioner.</p>
<p>Zoek je een klassiek homeopaat of EMDR-therapie in de regio Woudenberg, Maarn, Doorn, Scherpenzeel, Amersfoort Vathorst of Hoevelaken-Nijkerk? Je bent van harte welkom.</p>
HTML,
                    'right_content' => <<<'HTML'
<p>Yvonne begeleidt zowel volwassenen als kinderen met aandacht voor lichaam en geest.</p>
HTML,
                    'right_variant' => 'profile',
                    'right_title' => 'Yvonne Arnaud de Calavon',
                    'right_subtitle' => 'Klassiek homeopaat &amp; EMDR-practitioner',
                    'right_image' => '/images/yvonne.png',
                    'right_image_alt' => 'Yvonne Arnaud de Calavon',
                ],
            ],
            [
                'type' => 'features',
                'data' => [
                    'title' => 'Waarmee ik je kan helpen',
                    'eyebrow' => 'Aanbod',
                    'items' => [
                        [
                            'title' => 'Tarieven homeopathie',
                            'description' => 'Consulten worden geheel of gedeeltelijk vergoed via de aanvullende verzekering omdat de praktijk is aangesloten bij de NVKH. <a href="/tarieven" class="inline-flex items-center font-medium text-secondary hover:text-neutral-900 mt-3">Lees verder &rarr;</a>',
                        ],
                        [
                            'title' => 'Wanneer homeopathie',
                            'description' => 'Van baby tot volwassene: behandelingen bij o.a. verkoudheid, oorontstekingen, blaasontsteking en chronische klachten. <a href="/klassieke-homeopathie" class="inline-flex items-center font-medium text-secondary hover:text-neutral-900 mt-3">Lees verder &rarr;</a>',
                        ],
                        [
                            'title' => 'EMDR therapie',
                            'description' => 'Zachte traumaverwerking voor kinderen en volwassenen met ruimte voor maatwerk en veiligheid. <a href="/emdr" class="inline-flex items-center font-medium text-secondary hover:text-neutral-900 mt-3">Ontdek EMDR &rarr;</a>',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'cta',
                'data' => [
                    'title' => 'Plan een vrijblijvend kennismakingsgesprek',
                    'subtitle' => 'Samen zoeken we naar een behandeling die bij jou past.',
                    'button_text' => 'Neem contact op',
                    'button_url' => '/contact',
                    'background_color' => 'primary',
                ],
            ],
        ];

        Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'blocks' => $homeBlocks,
                'meta_title' => 'Praktijk voor klassieke homeopathie in Woudenberg',
                'meta_description' => 'Ieder mens is uniek: homeopathie en EMDR-praktijk in Woudenberg voor volwassenen en kinderen.',
                'meta_keywords' => 'homeopathie, EMDR, Woudenberg, klassiek homeopaat',
                'is_published' => true,
            ]
        );

        // Create placeholder pages for remaining navigation items
        $placeholderPages = [
            ['title' => 'Tarieven', 'slug' => 'tarieven'],
            ['title' => 'Klassieke homeopathie', 'slug' => 'klassieke-homeopathie'],
            ['title' => 'Cursus eerste hulp', 'slug' => 'cursus-eerste-hulp'],
            ['title' => 'EMDR', 'slug' => 'emdr'],
            ['title' => 'Contact', 'slug' => 'contact'],
            ['title' => 'Privacy', 'slug' => 'privacy'],
            ['title' => 'Handige links', 'slug' => 'handige-links'],
        ];

        foreach ($placeholderPages as $page) {
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
