<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Service\Auth\AuthService;
use IamLab\Service\Auth\GoogleOAuthService;
use IamLab\Service\Auth\GitHubOAuthService;
use IamLab\Service\Auth\FacebookOAuthService;
use IamLab\Service\Auth\GenericOAuthService;
use function App\Core\Helpers\config;

class OAuth extends aAPI
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Get available OAuth providers
     */
    public function providersAction(): void
    {
        try {
            $oauthConfig = config('oauth');
            $providers = [];

            if (!$oauthConfig['enabled']) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'OAuth is not enabled'
                ]);
                return;
            }

            // Check each provider
            $providerClasses = [
                'google' => GoogleOAuthService::class,
                'github' => GitHubOAuthService::class,
                'facebook' => FacebookOAuthService::class,
                'generic' => GenericOAuthService::class
            ];

            foreach ($providerClasses as $provider => $class) {
                // Check if provider is enabled AND properly configured
                if (!empty($oauthConfig[$provider]['enabled']) && $oauthConfig[$provider]['enabled']) {
                    // Validate that required configuration is present
                    $isConfigured = !empty($oauthConfig[$provider]['client_id']) && 
                                   !empty($oauthConfig[$provider]['client_secret']);

                    // For generic provider, also check for required URLs
                    if ($provider === 'generic') {
                        $isConfigured = $isConfigured && 
                                       !empty($oauthConfig[$provider]['authorization_url']) &&
                                       !empty($oauthConfig[$provider]['token_url']) &&
                                       !empty($oauthConfig[$provider]['user_info_url']);
                    }

                    // Only include provider if it's fully configured
                    if ($isConfigured) {
                        $providers[] = [
                            'name' => $provider,
                            'display_name' => ucfirst($provider),
                            'auth_url' => "/auth/oauth/{$provider}"
                        ];
                    }
                }
            }

            $this->dispatch([
                'success' => true,
                'providers' => $providers
            ]);
        } catch (Exception $e) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to get OAuth providers: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Redirect to OAuth provider
     */
    public function redirectAction(): void
    {
        try {
            $provider = $this->request->get('provider');

            if (empty($provider)) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Provider parameter is required'
                ]);
                return;
            }

            $oauthService = $this->getOAuthService($provider);
            $state = bin2hex(random_bytes(16));

            // Store state in session for security
            $this->session->set('oauth_state', $state);
            $this->session->set('oauth_provider', $provider);

            $authUrl = $oauthService->getAuthorizationUrl($state);

            $this->dispatch([
                'success' => true,
                'auth_url' => $authUrl,
                'state' => $state
            ]);
        } catch (Exception $e) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to get authorization URL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle OAuth callback
     */
    public function callbackAction(): void
    {
        try {
            $provider = $this->request->get('provider');
            $code = $this->request->get('code');
            $state = $this->request->get('state');
            $error = $this->request->get('error');

            // Check for OAuth errors
            if (!empty($error)) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'OAuth error: ' . $error
                ], 400);
                return;
            }

            // Validate required parameters
            if (empty($provider) || empty($code) || empty($state)) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Missing required OAuth parameters'
                ], 400);
                return;
            }

            // Validate state parameter
            $sessionState = $this->session->get('oauth_state');
            $sessionProvider = $this->session->get('oauth_provider');

            if ($state !== $sessionState || $provider !== $sessionProvider) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Invalid OAuth state parameter'
                ], 400);
                return;
            }

            // Clear session state
            $this->session->remove('oauth_state');
            $this->session->remove('oauth_provider');

            $oauthService = $this->getOAuthService($provider);

            // Exchange code for access token
            $tokenData = $oauthService->getAccessToken($code, $state);

            // Get user information
            $oauthUser = $oauthService->getUserInfo($tokenData['access_token']);

            // Create or update user
            $user = $oauthService->createOrUpdateUser($oauthUser);

            // Authenticate user
            $this->authService->authenticate($user, 'oauth_' . $provider);

            // Generate auth data
            $authData = $this->authService->generateAuthData($user);

            $this->dispatch([
                'success' => true,
                'message' => 'OAuth authentication successful',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'avatar' => $user->avatar
                ],
                'auth' => $authData
            ]);
        } catch (Exception $e) {
            $this->dispatch([
                'success' => false,
                'message' => 'OAuth callback failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unlink OAuth provider from user account
     */
    public function unlinkAction(): void
    {
        try {
            $user = $this->authService->getUser();

            if (!$user) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
                return;
            }

            $provider = $this->request->get('provider');

            if (empty($provider)) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Provider parameter is required'
                ], 400);
                return;
            }

            // Check if user has a password set (for security)
            if (empty($user->password) && $user->oauth_provider === $provider) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Cannot unlink OAuth provider without setting a password first'
                ], 400);
                return;
            }

            // Unlink OAuth provider
            if ($user->oauth_provider === $provider) {
                $user->oauth_provider = null;
                $user->oauth_id = null;
                $user->updated_at = date('Y-m-d H:i:s');

                if (!$user->save()) {
                    throw new Exception('Failed to unlink OAuth provider');
                }
            }

            $this->dispatch([
                'success' => true,
                'message' => 'OAuth provider unlinked successfully'
            ]);
        } catch (Exception $e) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to unlink OAuth provider: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get OAuth service instance
     */
    private function getOAuthService(string $provider)
    {
        switch ($provider) {
            case 'google':
                return new GoogleOAuthService();
            case 'github':
                return new GitHubOAuthService();
            case 'facebook':
                return new FacebookOAuthService();
            case 'generic':
                return new GenericOAuthService();
            default:
                throw new Exception("Unsupported OAuth provider: {$provider}");
        }
    }
}
