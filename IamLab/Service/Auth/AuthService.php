<?php

/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 22/10/2015
 * Time: 19:05
 */

namespace IamLab\Service\Auth;

use IamLab\Core\API\aAPI;
use IamLab\Model\User;
use Phalcon\Mvc\User\Component;
use function App\Core\Helpers\dd;

class AuthService extends aAPI
{
    public $isAuthenticated;
    public $state;

    public function isAuthenticated()
    {
        return (bool)$this->getIdentity();
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        $identity = $this->getIdentity();
        return User::findFirstById($identity['id']);
    }

    public function authenticate(User $user, $authMethod = "post")
    {

        if ($authMethod == "post") {

            return $this->authenticatePost($user);
        }

        return false;
    }

    private function authenticatePost(User $user)
    {

        $password = $user->getPassword();

        $user = User::findFirst("email='{$user->getEmail()}'");

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->getPassword())) {
            $this->setIdentity($user);

            return $user;
        }
        return false;
    }

    private function setIdentity($user)
    {

        $this->session->set('auth-identity', ['id' => $user->id, 'name' => $user->name, 'email' => $user->email,]);
    }

    public function getIdentity()
    {

        return $this->session->get('auth-identity');
    }


    /**
     * @param User $user
     *
     * @return bool
     */
    public function register(User $user): bool
    {
        $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

        if (!$user->validation()) {
            return false;
        }

        $userFind = User::findFirstByEmail($user->getEmail());

        if (!empty($userFind->id)) {
            return false;
        }

        if ($user->save() == false) {
            dump($user->getMessages());
            return false;

        }

        $this->setIdentity($user);

        return true;
    }
    /**
     * Deauthenticates the current user by destroying their session and clearing identity
     *
     * @return bool Returns true if deauthentication was successful
     * @throws Exception If session destruction fails
     */
    public function deauthenticate(): bool
    {
        try {
            // Clear the auth identity first
            $this->session->remove('auth-identity');

            // Reset session state
            $this->isAuthenticated = false;
            $this->state = null;

            // Regenerate the session ID to prevent session fixation attacks
            $this->session->regenerateId(true);

            // Destroy the session
            $this->session->destroy();

            return true;

        } catch (Exception $e) {
            // Log the error if you have a logger configured
            if (isset($this->logger)) {
                $this->logger->error('Deauthentication failed: ' . $e->getMessage());
            }
            throw $e;
        }
    }

}