<?php
namespace IamLab\Service\SEO;

use IamLab\Model\Project;
use IamLab\Model\Post;
use IamLab\Model\Package;
use League\Flysystem\Filesystem;
use DateTime;

class SitemapService
{
    private Filesystem $filesystem;
    private string $baseUrl;
    private const ALWAYS = 'always';
    private const HOURLY = 'hourly';
    private const DAILY = 'daily';
    private const WEEKLY = 'weekly';
    private const MONTHLY = 'monthly';
    private const YEARLY = 'yearly';
    private const NEVER = 'never';

    public function __construct(
        Filesystem $filesystem,
        string $baseUrl = 'https://iamlab.com'
    ) {
        $this->filesystem = $filesystem;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function generate(): bool
    {
        try {
            $content = $this->generateSitemapContent();
            $publicDir = ROOT_PATH . '/public';
            $this->filesystem->write($publicDir . '/sitemap.xml', $content);

            return true;
        } catch (\Exception $e) {
            error_log("Error generating sitemap.xml: " . $e->getMessage());
            throw new \RuntimeException("Failed to generate sitemap.xml: " . $e->getMessage());
        }
    }

    private function generateSitemapContent(): string
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Add static pages
        $content .= $this->addStaticPages();

        // Add dynamic content
        $content .= $this->addProjects();
        $content .= $this->addPosts();
        $content .= $this->addPackages();

        $content .= '</urlset>';

        return $content;
    }

    private function addStaticPages(): string
    {
        $staticPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => self::DAILY],
            ['loc' => '/about', 'priority' => '0.8', 'changefreq' => self::MONTHLY],
            ['loc' => '/contact', 'priority' => '0.8', 'changefreq' => self::MONTHLY],
            ['loc' => '/docs', 'priority' => '0.9', 'changefreq' => self::WEEKLY],
            ['loc' => '/privacy', 'priority' => '0.3', 'changefreq' => self::YEARLY],
            ['loc' => '/terms', 'priority' => '0.3', 'changefreq' => self::YEARLY],
        ];

        $content = '';
        foreach ($staticPages as $page) {
            $content .= $this->createUrlEntry(
                $this->baseUrl . $page['loc'],
                $page['priority'],
                $page['changefreq']
            );
        }

        return $content;
    }

    private function addProjects(): string
    {
        $content = '';
        $projects = Project::find();

        foreach ($projects as $project) {
            $content .= $this->createUrlEntry(
                $this->baseUrl . '/project/' . $project->getId(),
                '0.8',
                self::WEEKLY,
                $project->getUpdatedAt()
            );
        }

        return $content;
    }

    private function addPosts(): string
    {
        $content = '';
        $posts = Post::find();

        foreach ($posts as $post) {
            $content .= $this->createUrlEntry(
                $this->baseUrl . '/post/' . $post->getId(),
                '0.7',
                self::MONTHLY,
                $post->getUpdatedAt()
            );
        }

        return $content;
    }

    private function addPackages(): string
    {
        $content = '';
        $packages = Package::find();

        foreach ($packages as $package) {
            $content .= $this->createUrlEntry(
                $this->baseUrl . '/package/' . $package->getId(),
                '0.6',
                self::WEEKLY,
                $package->getUpdatedAt()
            );
        }

        return $content;
    }

    private function createUrlEntry(
        string $loc,
        string $priority,
        string $changefreq,
        ?DateTime $lastmod = null
    ): string {
        $entry = "  <url>\n";
        $entry .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";

        if ($lastmod) {
            $entry .= "    <lastmod>" . $lastmod->format('Y-m-d\TH:i:sP') . "</lastmod>\n";
        }

        $entry .= "    <changefreq>{$changefreq}</changefreq>\n";
        $entry .= "    <priority>{$priority}</priority>\n";
        $entry .= "  </url>\n";

        return $entry;
    }
}