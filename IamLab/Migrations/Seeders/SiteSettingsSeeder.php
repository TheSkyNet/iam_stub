<?php

namespace IamLab\Migrations\Seeders;

use IamLab\Model\SiteSetting;
use Phalcon\Migrations\Mvc\Model\Migration;

class SiteSettingsSeeder extends Migration
{
    public function run(): void
    {
        $settings = [
            // Basic Site Information
            [
                'key' => 'site_name',
                'value' => 'IAM Lab - Kevin Morton\'s Portfolio',
                'type' => SiteSetting::TYPE_STRING,
                'description' => 'The name of the website'
            ],
            [
                'key' => 'site_description',
                'value' => 'Innovative technology laboratory showcasing cutting-edge solutions in web development, project management, and digital transformation. Portfolio of Kevin Morton featuring real-world projects and open-source contributions.',
                'type' => SiteSetting::TYPE_STRING,
                'description' => 'Main site description'
            ],
            [
                'key' => 'contact_email',
                'value' => 'kev+jobs@kevs.biz',
                'type' => SiteSetting::TYPE_STRING,
                'description' => 'Primary contact email'
            ],

            // SEO Settings
            [
                'key' => 'seo_meta_tags',
                'value' => json_encode([
                    'keywords' => 'web development, PHP, Phalcon, Mithril.js, portfolio, technology, innovation, full-stack development, open source, project management',
                    'author' => 'Kevin Morton',
                    'robots' => 'index,follow',
                    'viewport' => 'width=device-width, initial-scale=1',
                    'revisit-after' => '7 days'
                ]),
                'type' => SiteSetting::TYPE_JSON,
                'description' => 'Default SEO meta tags'
            ],
            [
                'key' => 'og_image',
                'value' => '/img/iam-lab-og.jpg',
                'type' => SiteSetting::TYPE_STRING,
                'description' => 'Default Open Graph image for social sharing'
            ],
            [
                'key' => 'canonical_url',
                'value' => 'https://iamlab.tech',
                'type' => SiteSetting::TYPE_STRING,
                'description' => 'Canonical URL for the website'
            ],

            // Social Media Links
            [
                'key' => 'social_links',
                'value' => json_encode([
                    'github' => 'https://github.com/TheSkyNet',
                    'linkedin' => 'https://linkedin.com/in/kevmorton',
                    'twitter' => 'https://twitter.com/iamlab_tech',
                    'portfolio' => 'https://iamlab.tech'
                ]),
                'type' => SiteSetting::TYPE_JSON,
                'description' => 'Social media profile links'
            ],

            // Content Settings
            [
                'key' => 'posts_per_page',
                'value' => '12',
                'type' => SiteSetting::TYPE_INT,
                'description' => 'Number of posts to display per page'
            ],
            [
                'key' => 'projects_per_page',
                'value' => '9',
                'type' => SiteSetting::TYPE_INT,
                'description' => 'Number of projects to display per page'
            ],
            [
                'key' => 'enable_comments',
                'value' => '1',
                'type' => SiteSetting::TYPE_BOOL,
                'description' => 'Enable comments on posts and projects'
            ],

            // Theme and UI
            [
                'key' => 'theme_colors',
                'value' => json_encode([
                    'primary' => '#9BA5D0',    // Pastel blue
                    'secondary' => '#8E96C8',  // Medium pastel blue
                    'accent' => '#6B7399',     // Darker pastel blue
                    'background' => '#F8F9FE', // Light pastel blue
                    'text' => '#2A2F45'        // Dark blue-gray
                ]),
                'type' => SiteSetting::TYPE_JSON,
                'description' => 'Theme color palette'
            ],
            [
                'key' => 'font_family',
                'value' => 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                'type' => SiteSetting::TYPE_STRING,
                'description' => 'Default font family'
            ],

            // Site Features
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => SiteSetting::TYPE_BOOL,
                'description' => 'Enable maintenance mode'
            ],
            [
                'key' => 'enable_cache',
                'value' => '1',
                'type' => SiteSetting::TYPE_BOOL,
                'description' => 'Enable site-wide caching'
            ],
            [
                'key' => 'cache_duration',
                'value' => '3600',
                'type' => SiteSetting::TYPE_INT,
                'description' => 'Cache duration in seconds'
            ],

            // Analytics
            [
                'key' => 'analytics_enabled',
                'value' => '1',
                'type' => SiteSetting::TYPE_BOOL,
                'description' => 'Enable analytics tracking'
            ],
            [
                'key' => 'analytics_id',
                'value' => '',  // Add your Analytics ID here
                'type' => SiteSetting::TYPE_STRING,
                'description' => 'Analytics tracking ID'
            ],

            // API Settings
            [
                'key' => 'api_rate_limit',
                'value' => '100',
                'type' => SiteSetting::TYPE_INT,
                'description' => 'API requests per minute per IP'
            ],
            [
                'key' => 'api_version',
                'value' => 'v1',
                'type' => SiteSetting::TYPE_STRING,
                'description' => 'Current API version'
            ],

            // Footer Content
            [
                'key' => 'footer_text',
                'value' => '© ' . date('Y') . ' IAM Lab. All rights reserved.',
                'type' => SiteSetting::TYPE_STRING,
                'description' => 'Footer copyright text'
            ],
            [
                'key' => 'footer_links',
                'value' => json_encode([
                    'Privacy Policy' => '/privacy',
                    'Terms of Service' => '/terms',
                    'Contact' => '/contact'
                ]),
                'type' => SiteSetting::TYPE_JSON,
                'description' => 'Footer navigation links'
            ]
        ];

        foreach ($settings as $settingData) {
            $setting = new SiteSetting();
            $setting->setKey($settingData['key']);
            $setting->setType($settingData['type']);
            $setting->setValue($settingData['value']);
            $setting->setDescription($settingData['description']);

            if (!$setting->save()) {
                echo "Failed to save setting: " . $setting->getKey() . "\n";
                foreach ($setting->getMessages() as $message) {
                    echo $message->getMessage() . "\n";
                }
            } else {
                echo "Successfully created setting: " . $setting->getKey() . "\n";
            }
        }
    }
}