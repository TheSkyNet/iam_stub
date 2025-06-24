<?php

namespace IamLab\Migrations\Seeders;
use IamLab\Model\Post;
use Phalcon\Db\Exception;
use Phalcon\Migrations\Mvc\Model\Migration;


class PostSeeder extends Migration
{
    /**
     * Run the seeder
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Gaming Screenshots Gallery',
                'img' => '08-gameing-shots.png',
                'body' => 'Collection of gaming screenshots showcasing various gameplay moments.',
                'type' => 'gallery'
            ],
            [
                'title' => 'Email System Overview',
                'img' => 'Approval Email.jpg',
                'body' => 'Email approval system interface demonstration.',
                'type' => 'interface'
            ],
            [
                'title' => 'Benito Project',
                'img' => 'benito-logo.png',
                'body' => 'Benito project branding and logo design showcase.',
                'type' => 'branding'
            ],
            [
                'title' => 'Benito Preview',
                'img' => 'benito-pre.png',
                'body' => 'Preview of the Benito project interface and design.',
                'type' => 'preview'
            ],
            [
                'title' => 'Dashboard Interface',
                'img' => 'Dashboard.jpg',
                'body' => 'Administrative dashboard interface design.',
                'type' => 'interface'
            ],
            [
                'title' => 'Email Management System',
                'img' => 'Draft Emaiis.jpg',
                'body' => 'Email management system with draft functionality.',
                'type' => 'system'
            ],
            [
                'title' => 'Email Details Interface',
                'img' => 'Email Details.jpg',
                'body' => 'Detailed view of email management interface.',
                'type' => 'interface'
            ],
            [
                'title' => 'Engine Room Sports Platform',
                'img' => 'engineroomsports.jpg',
                'body' => 'Sports management platform interface and features.',
                'type' => 'platform'
            ],
            [
                'title' => 'Game Yetu Project',
                'img' => 'Game-yetu.fw.png',
                'body' => 'Game Yetu project showcase and features.',
                'type' => 'project'
            ],
            [
                'title' => 'Hallmark Brand Preview',
                'img' => 'hallmaerk1.png',
                'body' => 'Hallmark branding and design showcase.',
                'type' => 'branding'
            ],
            [
                'title' => 'IAM Lab Identity',
                'img' => 'IAMLablogo.png',
                'body' => 'IAM Lab corporate identity and branding.',
                'type' => 'branding'
            ],
            [
                'title' => 'Indigo Brand',
                'img' => 'indigologo.png',
                'body' => 'Indigo brand identity and logo showcase.',
                'type' => 'branding'
            ],
            [
                'title' => 'JJ Project',
                'img' => 'jj-logo.png',
                'body' => 'JJ project branding and identity design.',
                'type' => 'branding'
            ],
            [
                'title' => 'Moonworks Platform',
                'img' => 'moonwork-logo.png',
                'body' => 'Moonworks platform branding and interface.',
                'type' => 'platform'
            ],
            [
                'title' => 'Moonworks Search',
                'img' => 'moonworkes-serch.PNG',
                'body' => 'Search functionality interface for Moonworks platform.',
                'type' => 'interface'
            ],
            [
                'title' => 'Sahara London Brand',
                'img' => 'saharalondon.png',
                'body' => 'Sahara London brand identity and showcase.',
                'type' => 'branding'
            ],
            [
                'title' => 'Slimmables Platform',
                'img' => 'slimmables.fw.png',
                'body' => 'Slimmables fitness platform overview.',
                'type' => 'platform'
            ],
            [
                'title' => 'Slimmables Fitbook',
                'img' => 'Slimmables-Fitbook.png',
                'body' => 'Fitness tracking and workout planning interface.',
                'type' => 'interface'
            ],
            [
                'title' => 'Slimmables Home',
                'img' => 'Slimmables-Home.png',
                'body' => 'Main dashboard of the Slimmables platform.',
                'type' => 'interface'
            ],
            [
                'title' => 'Slimmables Workouts',
                'img' => 'Slimmables-Workouts.png',
                'body' => 'Workout management and tracking interface.',
                'type' => 'interface'
            ]
        ];

        foreach ($posts as $postData) {
            $post = new Post();
            $post->setTitle($postData['title']);
            $post->setImg($postData['img']);
            $post->setBody($postData['body']);
            $post->setType($postData['type']);

            if (!$post->save()) {
                echo "Failed to save post: " . $post->getTitle() . "\n";
                foreach ($post->getMessages() as $message) {
                    echo $message->getMessage() . "\n";
                }
            } else {
                echo "Successfully created post: " . $post->getTitle() . "\n";
            }
        }
    }
}