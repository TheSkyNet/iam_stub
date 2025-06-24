<?php
// app/services/SettingsService.php

namespace IamLab\Service;

use IamLab\Model\SiteSetting;

class SettingsService
{
    private $settings = [];
    private $formatted = [];

    public function initialize()
    {
        $this->loadSettings();
        $this->formatSettings();
    }

    private function loadSettings()
    {
        $this->settings = SiteSetting::find()->toArray();
    }

    private function formatSettings()
    {
        // Base settings format
        $this->formatted = [
            'meta' => [
                'title' => '',
                'description' => '',
                'keywords' => '',
                'author' => '',
                'robots' => 'index,follow',
                'viewport' => 'width=device-width, initial-scale=1',
            ],
            'og' => [
                'title' => '',
                'description' => '',
                'image' => '',
                'type' => 'website',
                'url' => '',
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => '',
                'description' => '',
                'image' => '',
            ],
            'theme' => [
                'colors' => [],
                'font' => '',
            ],
            'social' => [],
            'config' => [
                'maintenance' => false,
                'analytics' => [
                    'enabled' => false,
                    'script' => '',
                ],
                'postsPerPage' => 10,
            ],
            'structured_data' => [],
        ];

        foreach ($this->settings as $setting) {
            $value = $this->parseValue($setting['value'], $setting['type']);

            switch ($setting['key']) {
                case 'site_name':
                    $this->formatted['meta']['title'] = $value;
                    $this->formatted['og']['title'] = $value;
                    $this->formatted['twitter']['title'] = $value;
                    break;

                case 'site_description':
                    $this->formatted['meta']['description'] = $value;
                    $this->formatted['og']['description'] = $value;
                    $this->formatted['twitter']['description'] = $value;
                    break;

                case 'seo_meta_tags':
                    $this->formatted['meta'] = array_merge($this->formatted['meta'], $value);
                    break;

                case 'og_image':
                    $this->formatted['og']['image'] = $value;
                    $this->formatted['twitter']['image'] = $value;
                    break;

                case 'social_links':
                    $this->formatted['social'] = $value;
                    $this->formatted['structured_data']['sameAs'] = array_values($value);
                    break;

                case 'theme_colors':
                    $this->formatted['theme']['colors'] = $value;
                    break;

                case 'font_family':
                    $this->formatted['theme']['font'] = $value;
                    break;

                case 'maintenance_mode':
                    $this->formatted['config']['maintenance'] = (bool)$value;
                    if ($value) {
                        $this->formatted['meta']['robots'] = 'noindex,nofollow';
                    }
                    break;

                case 'analytics_enabled':
                    $this->formatted['config']['analytics']['enabled'] = (bool)$value;
                    break;

                case 'analytics_id':
                    $this->formatted['config']['analytics']['script'] = $value;
                    break;

                case 'posts_per_page':
                    $this->formatted['config']['postsPerPage'] = (int)$value;
                    break;
            }
        }

        // Build structured data
        $this->formatted['structured_data'] = array_merge([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $this->formatted['meta']['title'],
            'description' => $this->formatted['meta']['description'],
            'url' => $this->formatted['og']['url'],
        ], $this->formatted['structured_data']);
    }

    private function parseValue($value, $type)
    {
        switch ($type) {
            case SiteSetting::TYPE_JSON:
                return json_decode($value, true) ?? [];
            case SiteSetting::TYPE_BOOL:
                return (bool)$value;
            case SiteSetting::TYPE_INT:
                return (int)$value;
            default:
                return $value;
        }
    }

    public function getFormatted()
    {
        return $this->formatted;
    }

    public function getRaw()
    {
        return $this->settings;
    }
}