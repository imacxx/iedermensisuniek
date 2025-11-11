<?php

namespace App\Services;

class BlockSchema
{
    /**
     * Get all available block types with their schemas
     */
    public static function getAvailableBlocks(): array
    {
        return [
            'hero' => [
                'name' => 'Hero Section',
                'description' => 'Large hero section with title and subtitle',
                'schema' => [
                    'title' => ['type' => 'string', 'required' => true, 'default' => 'Welcome'],
                    'subtitle' => ['type' => 'string', 'required' => false, 'default' => ''],
                    'background_image' => ['type' => 'string', 'required' => false, 'default' => null],
                    'eyebrow' => ['type' => 'string', 'required' => false, 'default' => ''],
                ],
                'template' => 'blocks.hero'
            ],
            'text' => [
                'name' => 'Text Content',
                'description' => 'Rich text content block',
                'schema' => [
                    'content' => ['type' => 'html', 'required' => true, 'default' => '<p>Your content here</p>'],
                    'eyebrow' => ['type' => 'string', 'required' => false, 'default' => ''],
                    'background_style' => ['type' => 'string', 'required' => false, 'default' => 'bg-white'],
                    'max_width' => ['type' => 'string', 'required' => false, 'default' => 'max-w-3xl'],
                    'padding_class' => ['type' => 'string', 'required' => false, 'default' => 'px-4 py-12 sm:px-6 lg:px-8'],
                ],
                'template' => 'blocks.text'
            ],
            'features' => [
                'name' => 'Features List',
                'description' => 'List of features with titles and descriptions',
                'schema' => [
                    'title' => ['type' => 'string', 'required' => false, 'default' => 'Features'],
                    'eyebrow' => ['type' => 'string', 'required' => false, 'default' => ''],
                    'items' => [
                        'type' => 'array',
                        'required' => false,
                        'default' => [],
                        'item_schema' => [
                            'title' => ['type' => 'string', 'required' => true, 'default' => 'Feature Title'],
                            'description' => ['type' => 'string', 'required' => false, 'default' => 'Feature description'],
                            'icon' => ['type' => 'string', 'required' => false, 'default' => null],
                        ]
                    ],
                ],
                'template' => 'blocks.features'
            ],
            'image' => [
                'name' => 'Image Block',
                'description' => 'Single image with optional caption',
                'schema' => [
                    'image_url' => ['type' => 'string', 'required' => true, 'default' => ''],
                    'alt_text' => ['type' => 'string', 'required' => false, 'default' => ''],
                    'caption' => ['type' => 'string', 'required' => false, 'default' => ''],
                ],
                'template' => 'blocks.image'
            ],
            'gallery' => [
                'name' => 'Image Gallery',
                'description' => 'Grid of images',
                'schema' => [
                    'images' => [
                        'type' => 'array',
                        'required' => false,
                        'default' => [],
                        'item_schema' => [
                            'url' => ['type' => 'string', 'required' => true, 'default' => ''],
                            'alt' => ['type' => 'string', 'required' => false, 'default' => ''],
                        ]
                    ],
                    'columns' => ['type' => 'integer', 'required' => false, 'default' => 3, 'min' => 1, 'max' => 6],
                ],
                'template' => 'blocks.gallery'
            ],
            'two_column' => [
                'name' => 'Two Column Layout',
                'description' => 'Content in two columns',
                'schema' => [
                    'left_content' => ['type' => 'html', 'required' => true, 'default' => '<p>Left column content</p>'],
                    'right_content' => ['type' => 'html', 'required' => false, 'default' => '<p>Right column content</p>'],
                    'eyebrow' => ['type' => 'string', 'required' => false, 'default' => ''],
                    'layout' => ['type' => 'string', 'required' => false, 'default' => '50-50', 'options' => ['50-50', '33-67', '67-33']],
                    'right_variant' => ['type' => 'string', 'required' => false, 'default' => 'plain', 'options' => ['plain', 'card', 'profile']],
                    'right_title' => ['type' => 'string', 'required' => false, 'default' => ''],
                    'right_subtitle' => ['type' => 'string', 'required' => false, 'default' => ''],
                    'right_button_text' => ['type' => 'string', 'required' => false, 'default' => ''],
                    'right_button_link' => ['type' => 'string', 'required' => false, 'default' => ''],
                    'right_image' => ['type' => 'string', 'required' => false, 'default' => ''],
                    'right_image_alt' => ['type' => 'string', 'required' => false, 'default' => ''],
                ],
                'template' => 'blocks.two_column'
            ],
            'cta' => [
                'name' => 'Call to Action',
                'description' => 'Call to action section with button',
                'schema' => [
                    'title' => ['type' => 'string', 'required' => true, 'default' => 'Ready to get started?'],
                    'subtitle' => ['type' => 'string', 'required' => false, 'default' => 'Join thousands of satisfied customers.'],
                    'button_text' => ['type' => 'string', 'required' => true, 'default' => 'Get Started'],
                    'button_url' => ['type' => 'string', 'required' => true, 'default' => '#'],
                    'background_color' => ['type' => 'string', 'required' => false, 'default' => 'primary'],
                ],
                'template' => 'blocks.cta'
            ],
        ];
    }

    /**
     * Get a specific block schema
     */
    public static function getBlockSchema(string $type): ?array
    {
        $blocks = self::getAvailableBlocks();
        return $blocks[$type] ?? null;
    }

    /**
     * Get default data for a block type
     */
    public static function getDefaultBlockData(string $type): array
    {
        $schema = self::getBlockSchema($type);
        if (!$schema) return [];

        $data = [];
        foreach ($schema['schema'] as $field => $config) {
            if (isset($config['default'])) {
                $data[$field] = $config['default'];
            } elseif ($config['type'] === 'array') {
                $data[$field] = [];
            } else {
                $data[$field] = '';
            }
        }

        return $data;
    }

    /**
     * Validate block data against schema
     */
    public static function validateBlockData(string $type, array $data): array
    {
        $schema = self::getBlockSchema($type);
        if (!$schema) return ['valid' => false, 'errors' => ['Unknown block type']];

        $errors = [];
        foreach ($schema['schema'] as $field => $config) {
            if ($config['required'] && (!isset($data[$field]) || empty($data[$field]))) {
                $errors[] = "Field '{$field}' is required";
            }

            if (isset($data[$field])) {
                $value = $data[$field];

                // Type validation
                switch ($config['type']) {
                    case 'string':
                        if (!is_string($value)) {
                            $errors[] = "Field '{$field}' must be a string";
                        }
                        break;
                    case 'integer':
                        if (!is_int($value)) {
                            $errors[] = "Field '{$field}' must be an integer";
                        }
                        if (isset($config['min']) && $value < $config['min']) {
                            $errors[] = "Field '{$field}' must be at least {$config['min']}";
                        }
                        if (isset($config['max']) && $value > $config['max']) {
                            $errors[] = "Field '{$field}' must be at most {$config['max']}";
                        }
                        break;
                    case 'array':
                        if (!is_array($value)) {
                            $errors[] = "Field '{$field}' must be an array";
                        }
                        // Validate array items if schema provided
                        if (isset($config['item_schema']) && is_array($value)) {
                            foreach ($value as $index => $item) {
                                if (is_array($item)) {
                                    foreach ($config['item_schema'] as $itemField => $itemConfig) {
                                        if ($itemConfig['required'] && (!isset($item[$itemField]) || empty($item[$itemField]))) {
                                            $errors[] = "Item {$index} field '{$itemField}' is required";
                                        }
                                    }
                                }
                            }
                        }
                        break;
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }

    /**
     * Get block template path
     */
    public static function getBlockTemplate(string $type): ?string
    {
        $schema = self::getBlockSchema($type);
        return $schema['template'] ?? null;
    }
}
