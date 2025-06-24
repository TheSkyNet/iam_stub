<?php
namespace IamLab\Service\SEO;

use IamLab\Model\Project;
use IamLab\Model\Post;
use IamLab\Model\Package;
use League\Flysystem\Filesystem;

class LLMsService
{
    private Filesystem $filesystem;
    private string $baseUrl;

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
            // Generate and write the concise version
            $content = $this->generateContent(false);
            $publicDir = ROOT_PATH . '/public';
            $this->filesystem->write($publicDir . '/llms.txt', $content);

            // Generate and write the full version
            $contentFull = $this->generateContent(true);
            $this->filesystem->write($publicDir . '/llms-full.txt', $contentFull);

            return true;
        } catch (\Exception $e) {
            error_log("Error generating llms files: " . $e->getMessage());
            throw new \RuntimeException("Failed to generate llms files: " . $e->getMessage());
        }
    }

    private function generateContent(bool $full = false): string
    {
        $projects = Project::find();
        $posts = Post::find();
        $packages = Package::find();
        $siteDetails = $this->getSiteDetails($full);

        $content = "# {$siteDetails['name']}\n\n";
        $content .= "> {$siteDetails['about']}\n\n";

        if ($full) {
            $content .= "## Key Features\n\n";
            foreach ($siteDetails['features'] as $feature) {
                $content .= "- **{$feature['title']}:** {$feature['description']}\n";
            }
            $content .= "\n";
        }

        // Projects Section
        $content .= "## Projects\n\n";
        foreach ($projects as $project) {
            $content .= "- [{$project->getTitle()}]({$this->baseUrl}/project/{$project->getId()}): "
                . $this->truncateBody($project->getBody(), $full ? 300 : 150) . "\n";
        }
        $content .= "\n";

        // Blog Posts Section
        $content .= "## Blog Posts\n\n";
        foreach ($posts as $post) {
            $content .= "- [{$post->getTitle()}]({$this->baseUrl}/post/{$post->getId()}): "
                . $this->truncateBody($post->getBody(), $full ? 300 : 150) . "\n";
        }
        $content .= "\n";

        // Packages Section with extended descriptions in full version
        $content .= "## Packages\n\n";
        foreach ($packages as $package) {
            if ($full) {
                $content .= "### {$package->getName()}\n";
                $content .= "{$package->getDescription()}\n";
                $content .= "- **Link:** [{$package->getLink()}]({$package->getLink()})\n\n";
            } else {
                $content .= "- [{$package->getName()}]({$package->getLink()}): {$package->getDescription()}\n";
            }
        }
        $content .= "\n";

        // Resources Section
        $content .= "## Resources\n\n";
        foreach ($siteDetails['resources'] as $resource) {
            if ($full && isset($resource['extended_description'])) {
                $content .= "### {$resource['title']}\n";
                $content .= "{$resource['extended_description']}\n";
                $content .= "- **Link:** [{$this->baseUrl}{$resource['link']}]({$this->baseUrl}{$resource['link']})\n\n";
            } else {
                $content .= "- [{$resource['title']}]({$this->baseUrl}{$resource['link']}): {$resource['description']}\n";
            }
        }
        $content .= "\n";

        // Legal Section
        $content .= "## Legal\n\n";
        foreach ($siteDetails['legal'] as $legal) {
            if ($full && isset($legal['description'])) {
                $content .= "### {$legal['title']}\n";
                $content .= "{$legal['description']}\n";
                $content .= "- **Link:** [{$this->baseUrl}{$legal['link']}]({$this->baseUrl}{$legal['link']})\n\n";
            } else {
                $content .= "- [{$legal['title']}]({$this->baseUrl}{$legal['link']})\n";
            }
        }

        if (!$full) {
            $content .= "\n---\n\n";
            $content .= "For more detailed information, see: [{$this->baseUrl}/llms-full.txt]({$this->baseUrl}/llms-full.txt)\n";
        }

        return $content;
    }

    private function getSiteDetails(bool $full = false): array
    {
        $details = [
            'name' => 'IAM Lab',
            'about' => 'IAM Lab is an innovative technology laboratory focused on developing cutting-edge solutions in web development, artificial intelligence, and software architecture.',
            'resources' => [
                [
                    'title' => 'Documentation',
                    'link' => '/docs',
                    'description' => 'Technical documentation and guides',
                    'extended_description' => 'Comprehensive documentation covering all aspects of our technologies, including setup guides, API references, and best practices for implementation.'
                ],
                [
                    'title' => 'API Reference',
                    'link' => '/api/docs',
                    'description' => 'API documentation',
                    'extended_description' => 'Detailed API documentation with examples, endpoint descriptions, and integration guides for all our services.'
                ],
                [
                    'title' => 'GitHub',
                    'link' => 'https://github.com/iam-lab',
                    'description' => 'Our open source projects',
                    'extended_description' => 'Access our open-source repositories, contribute to our projects, and collaborate with our development team.'
                ]
            ],
            'legal' => [
                [
                    'title' => 'Privacy Policy',
                    'link' => '/privacy',
                    'description' => 'Detailed information about how we handle and protect your data.'
                ],
                [
                    'title' => 'Terms of Service',
                    'link' => '/terms',
                    'description' => 'Our terms of service and usage guidelines.'
                ],
                [
                    'title' => 'AI Policy',
                    'link' => '/ai-policy',
                    'description' => 'Guidelines and principles for AI development and usage.'
                ]
            ]
        ];

        if ($full) {
            $details['features'] = [
                [
                    'title' => 'Innovation',
                    'description' => 'Cutting-edge solutions in AI and web development'
                ],
                [
                    'title' => 'Open Source',
                    'description' => 'Commitment to open-source development and community collaboration'
                ],
                [
                    'title' => 'Research',
                    'description' => 'Advanced research in artificial intelligence and software architecture'
                ]
            ];
        }

        return $details;
    }

    private function truncateBody(string $body, int $length = 150): string
    {
        $body = strip_tags($body);
        if (mb_strlen($body) <= $length) {
            return $body;
        }

        return mb_substr($body, 0, $length) . '...';
    }
}