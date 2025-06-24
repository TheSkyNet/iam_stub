<?php

namespace IamLab\Service;

use IamLab\Core\API\aAPI;
use IamLab\Model\Code;
use IamLab\Model\Filepond;
use IamLab\Model\Package;
use IamLab\Model\Post;
use IamLab\Model\Project;
use IamLab\Model\SiteSetting;
use IamLab\Model\User;
use IamLab\Service\SEO\LLMsService;
use IamLab\Service\SEO\SitemapService;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use SodiumException;
use function App\Core\Helpers\dd;
use function App\Core\Helpers\decrypt;
use function App\Core\Helpers\moveTo;

/**
 * @property void isAuthenticated
 * @property Filesystem $file
 * @property Filesystem $fs
 */
class API extends aAPI
{
    /**
     * @return void
     */
    public function getAllAction()
    {
        $posts = Post::find();
        $packages = Package::find();
        $project = Project::find();
        $code = Code::find();

        $this->dispatch([$posts, $packages, $project, $code]);
    }

    public function getPostsAction()
    {
        $posts = Post::find();
        $this->dispatch($posts);
    }


    public function getPostsByIdAction(int $id)
    {
        $posts = Post::findFirstById($id);
        $this->dispatch($posts);
    }

    public function deleteProjectAction(int $id)
    {
        $project = Project::findFirstById($id);

        $this->delete($project);
    }

    public function getUsersAction()
    {
        $posts = User::find();
        $this->dispatch($posts);
    }


    public function getUsersByIdAction(int $id)
    {
        $posts = User::findFirstById($id);
        $this->dispatch($posts);
    }

    public function deleteUserAction(int $id)
    {
        $project = User::findFirstById($id);

        $this->delete($project);
    }

    public function newUserAction()
    {
      $this->isAuthenticated;
        $user = (new User())->setName(
            $this->getParam('name')
        )->setEmail(
            $this->getParam('email')
        )->setKey(
            uniqid('', true)
        )->setPassword(
            password_hash($this->getParam('password'), PASSWORD_DEFAULT)
        )->save();
        $this->dispatch($user);
    }


    public function getCodeAction()
    {
        $posts = Code::find();
        $this->dispatch($posts);
    }


    public function getCodeByIdAction(int $id)
    {
        $posts = Code::findFirstById($id);
        $this->dispatch($posts);
    }

    public function getCodeByIdHtmlAction(int $id)
    {
        /** @var Code $code */
        $code = Code::findFirstById($id);
        $this->response->setContentType('text/html', 'UTF-8');

        $this->response->setContent($code->getBody());
        $this->response->send();
    }

    public function deleteCodeAction(int $id)
    {
        $project = Code::findFirstById($id);

        $this->delete($project);
    }


    public function newCodeAction()
    {
        $this->isAuthenticated;
        $code = new Code();

        $code->setImg(  "/files/HORSE2.fw.png");
        $code->setTitle( "Demo");
        $code->setBody($this->getParam('body'));
        $code->setType('html');

        // The simple fix: Check if create() returns false.
        if ($code->create() === false) {
            $errors =[];
            foreach ($code->getMessages() as $message) {
               $errors[] = $message->getMessage();
            }
            $this->dispatch(
                [
                    'success' => false,
                    'errors' => $errors
                ]
            );

        }

      $this->dispatch(
            [
                'success' => true,
            ]
        );
    }
    public function updateCodeAction()
    {
       $this->isAuthenticated;
        $posts = (new Code())->setImg(
            $this->getParam('img')
        )->setTitle(
            $this->getParam('title')
        )->setBody(
            $this->getParam('body')
        )->save();
        $this->dispatch($posts);
    }

    public function getFilesAction()
    {
        $fp = FILE_PATH;
        $project = Filepond::find("filepath = '$fp'");


        $this->dispatch($project);
    }

    /**
     * @throws FilesystemException
     * @throws SodiumException
     */
    public function saveFilesAction()
    {
 
        try {
            $project = $this->file->listContents('', true);

            // Ensure the input is properly formatted
            $encryptedData = $this->getParam('file');
            if (empty($encryptedData)) {
                throw new \InvalidArgumentException('Empty file parameter');
            }

            // Safely decode and decrypt
            $decryptedData = decrypt($encryptedData);
            if ($decryptedData === false) {
                throw new \RuntimeException('Failed to decrypt file data');
            }

            $fileData = json_decode($decryptedData, true, 512, JSON_THROW_ON_ERROR);
            if (json_last_error() !== JSON_ERROR_NONE || !isset($fileData['id'])) {
                throw new \RuntimeException('Invalid file data format');
            }

            $file = Filepond::findFirst($fileData['id']);
            if (!$file) {
                throw new \RuntimeException('File not found');
            }

            // Move the file
            moveTo('fs', $file->filepath . '/' . $file->filename, FILE_PATH . '/' . $file->filename);

            // Update file path
            $file->filepath = FILE_PATH;
            $file->save();

            $this->dispatch($project);
        } catch (\Exception $e) {
            // Handle the error appropriately
            $this->dispatch([
                'error' => true,
                'message' => 'Failed to save file: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePostsAction(int $id)
    {
        $post = Post::findFirstById($id);

        $this->delete($post);
    }

    public function deletePackageAction(int $id)

    {
        $post = Post::findFirstById($id);

        $this->delete($post);
    }

    public function newPostAction()
    {
//       / $this->isAuthenticated;
        $posts = (new Post())->setImg(
            $this->getParam('img')
        )->setTitle(
            $this->getParam('title')
        )->setBody(
            $this->getParam('body')
        )->save();
        $this->dispatch($posts);
    }

    public function updatePostsAction($id)
    {
        //    $this->isAuthenticated;
        $posts = Post::findFirstById($id)->setImg(
            $this->getParam('img')
        )->setTitle(
            $this->getParam('title')
        )->setBody(
            $this->getParam('body')
        )->save();
        $this->dispatch($posts);
    }

    public function getPackageAction()
    {
        $posts = Package::find();
        $this->dispatch($posts);
    }

    public function getPackageByIdAction($id)
    {
        $posts = Package::findFirstById($id);
        $this->dispatch($posts);
    }

    public function newPackageAction()
    {
        //$this->isAuthenticated;
        $posts = (new Package())->setType(
            $this->getParam('type')
        )->setName(
            $this->getParam('name')
        )->setSlug(
            $this->getParam('name')
        )->setDescription(
            $this->getParam('description', 'description')
        )->setLink(
            $this->getParam('link')
        );
        $this->save($posts);
    }

    public function updatePackageAction($id)
    {
        // $this->isAuthenticated;
        $posts = (new Package())->setType(
            $this->getParam('type')
        )->setName(
            $this->getParam('name')
        )->setSlug(
            $this->getParam('name')
        )->setDescription(
            $this->getParam('description', '')
        )->setLink(
            $this->getParam('link')
        );

        $this->save($posts);
    }

    public function getProjectAction()
    {
        $posts = Project::find();
        $this->dispatch($posts);
    }

    public function getProjectByIdAction($id)
    {
        $posts = Project::findFirstById($id);
        $this->dispatch($posts);
    }

    public function newProjectAction()
    {   /// TODO: Authentication
        // $this->isAuthenticated;
        $posts = (new Project())->setImg(
            $this->getParam('img')
        )->setTitle(
            $this->getParam('title')
        )->setBody(
            $this->getParam('body')
        )->save();

        $this->dispatch($posts);
    }

    public function updateProjectAction($id)
    {
        $posts = Project::findFirstById($id)->setImg(
            $this->getParam('img')
        )->setTitle(
            $this->getParam('title')
        )->setBody(
            $this->getParam('body')
        )->save();
        $this->dispatch($posts);
    }
    /**
     * @param $type
     * @return void
     */
    public function generateSeoFileAction($type): void
    {
        $this->isAuthenticated;

        try {
            switch ($type) {
                case 'llms':
                    $service = new LLMsService($this->fs);
                    $result = $service->generate();
                    if (!$result) {
                        throw new \RuntimeException("Failed to write llms files");
                    }
                    break;
                case 'sitemap':
                    $service = new SitemapService($this->fs);
                    $result = $service->generate();
                    if (!$result) {
                        throw new \RuntimeException("Failed to generate sitemap.xml");
                    }
                    // Ping search engines about the update
                   // $this->pingSearchEnginesAction();
                    break;
                default:
                    throw new \InvalidArgumentException("Unsupported file type: $type");
            }

            $this->dispatch([
                'success' => true,
                'message' => "Successfully generated $type file"
            ]);

        } catch (\Exception $e) {
            $this->dispatch([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function pingSearchEnginesAction(): void
    {
        $urls = [
            "https://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($this->baseUrl . "/sitemap.xml"),
            "https://www.bing.com/ping?sitemap=" . urlencode($this->baseUrl . "/sitemap.xml")
        ];

        foreach ($urls as $url) {
            file_get_contents($url);
        }
    }


    /**
     * Get all site settings
     */
    public function getSettingsAction()
    {
        $settings = SiteSetting::find();
        $this->dispatch($settings);
    }

    /**
     * Get a specific setting by key
     * @param string $key
     */
    public function getSettingByKeyAction(string $key)
    {
        $setting = SiteSetting::findFirst([
            'conditions' => 'key = :key:',
            'bind' => ['key' => $key]
        ]);

        if (!$setting) {
            $this->dispatch(['error' => 'Setting not found'], 404);
            return;
        }

        $this->dispatch($setting);
    }

    /**
     * Create a new setting
     */
    public function newSettingAction()
    {
        $this->isAuthenticated;

        try {
            $setting = new SiteSetting();
            $setting->setKey($this->getParam('key'))
                ->setType($this->getParam('type'))
                ->setValue($this->getParam('value'))
                ->setDescription($this->getParam('description', ''));

            if (!$setting->save()) {
                $messages = [];
                foreach ($setting->getMessages() as $message) {
                    $messages[] = $message->getMessage();
                }
                throw new \Exception(implode(', ', $messages));
            }

            $this->dispatch($setting);
        } catch (\Exception $e) {
            $this->dispatch([
                'error' => true,
                'message' => 'Failed to create setting: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update an existing setting
     * @param string $key
     */
    public function updateSettingAction(string $key): void
    {
        $this->isAuthenticated;

        try {
            $setting = SiteSetting::findFirst([
                'conditions' => 'key = :key:',
                'bind' => ['key' => $key]
            ]);

            if (!$setting) {
                $this->dispatch(['error' => 'Setting not found'], 404);
                return;
            }

            if ($this->hasParam('value')) {
                $setting->setValue($this->getParam('value'));
            }

            if ($this->hasParam('type')) {
                $setting->setType($this->getParam('type'));
            }

            if ($this->hasParam('description')) {
                $setting->setDescription($this->getParam('description'));
            }

            if (!$setting->save()) {
                $messages = [];
                foreach ($setting->getMessages() as $message) {
                    $messages[] = $message->getMessage();
                }
                throw new \Exception(implode(', ', $messages));
            }

            $this->dispatch($setting);
        } catch (\Exception $e) {
            $this->dispatch([
                'error' => true,
                'message' => 'Failed to update setting: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete a setting
     * @param string $key
     */
    public function deleteSettingAction(string $key)
    {
        $this->isAuthenticated;

        $setting = SiteSetting::findFirst([
            'conditions' => 'key = :key:',
            'bind' => ['key' => $key]
        ]);

        if (!$setting) {
            $this->dispatch(['error' => 'Setting not found'], 404);
            return;
        }

        if (!$setting->delete()) {
            $this->dispatch([
                'error' => true,
                'message' => 'Failed to delete setting'
            ], 400);
            return;
        }

        $this->dispatch(['success' => true]);
    }




}