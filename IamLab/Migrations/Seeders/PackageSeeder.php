<?php
namespace IamLab\Migrations\Seeders;
use Phalcon\Db\Exception;
use Phalcon\Migrations\Mvc\Model\Migration;
use IamLab\Model\Package;
use IamLab\Core\Enum\PackageType;

class PackageSeeder extends Migration
{
    const PACKAGIST_BASE_URL = 'https://packagist.org/packages/';

    /**
     * Run the seeder
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'batfish/core',
                'description' => 'batfish a high performance domain driven framework built for nosql data store',
                'slug' => 'batfish-core',
                'link' => self::PACKAGIST_BASE_URL . 'batfish/core',
                'lang' => PackageType::COMPOSER,
                'type' => 'framework'
            ],
            [
                'name' => 'iam-lab/login-as-customer',
                'description' => 'Login as customer functionality',
                'slug' => 'login-as-customer',
                'link' => self::PACKAGIST_BASE_URL . 'iam-lab/login-as-customer',
                'lang' => PackageType::COMPOSER,
                'type' => 'module'
            ],
            [
                'name' => 'iam-labs/iam-crawler',
                'description' => 'Web crawler implementation',
                'slug' => 'iam-crawler',
                'link' => self::PACKAGIST_BASE_URL . 'iam-labs/iam-crawler',
                'lang' => PackageType::COMPOSER,
                'type' => 'tool'
            ],
            [
                'name' => 'iamlab/google-sheets-db',
                'description' => 'A library to use Google Sheets as a basic Database',
                'slug' => 'google-sheets-db',
                'link' => self::PACKAGIST_BASE_URL . 'iamlab/google-sheets-db',
                'lang' => PackageType::COMPOSER,
                'type' => 'library'
            ],
            [
                'name' => 'txd-ltd/campaign-plus-send-mail',
                'description' => 'a simple package to implement the campaignPlusSendMail API',
                'slug' => 'campaign-plus-send-mail',
                'link' => self::PACKAGIST_BASE_URL . 'txd-ltd/campaign-plus-send-mail',
                'lang' => PackageType::COMPOSER,
                'type' => 'api-client'
            ]
        ];

        foreach ($packages as $packageData) {
            $package = new Package();
            $package->setName($packageData['name']);
            $package->setDescription($packageData['description']);
            $package->setSlug($packageData['slug']);
            $package->setLink($packageData['link']);
            $package->setLang($packageData['lang']);
            $package->setType($packageData['type']);

            if (!$package->save()) {
                echo "Failed to save package: " . $package->getName() . "\n";
                foreach ($package->getMessages() as $message) {
                    echo $message->getMessage() . "\n";
                }
            } else {
                echo "Successfully created package: " . $package->getName() . "\n";
            }
        }
    }
}