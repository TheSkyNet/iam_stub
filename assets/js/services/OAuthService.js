// OAuthService.js

const OAuthService = {
    /**
     * Get available OAuth providers
     */
    getProviders: function() {
        return m.request({
            method: "GET",
            url: "/api/oauth/providers",
            headers: {
                "Content-Type": "application/json"
            }
        }).then(function(response) {
            if (response.success) {
                return response.providers;
            } else {
                throw new Error(response.message || 'Failed to get OAuth providers');
            }
        }).catch(function(error) {
            console.error("OAuth providers request failed:", error);
            throw error;
        });
    },

    /**
     * Initiate OAuth login with a provider
     */
    initiateLogin: function(provider) {
        return m.request({
            method: "GET",
            url: "/api/oauth/redirect",
            params: {
                provider: provider
            },
            headers: {
                "Content-Type": "application/json"
            }
        }).then(function(response) {
            if (response.success) {
                // Store state for security
                sessionStorage.setItem('oauth_state', response.state);
                sessionStorage.setItem('oauth_provider', provider);
                
                // Redirect to OAuth provider
                window.location.href = response.auth_url;
            } else {
                throw new Error(response.message || 'Failed to initiate OAuth login');
            }
        }).catch(function(error) {
            console.error("OAuth initiate login failed:", error);
            throw error;
        });
    },

    /**
     * Handle OAuth callback
     */
    handleCallback: function(provider, code, state) {
        // Verify state parameter
        const storedState = sessionStorage.getItem('oauth_state');
        const storedProvider = sessionStorage.getItem('oauth_provider');
        
        if (state !== storedState || provider !== storedProvider) {
            throw new Error('Invalid OAuth state parameter');
        }

        // Clear stored state
        sessionStorage.removeItem('oauth_state');
        sessionStorage.removeItem('oauth_provider');

        return m.request({
            method: "GET",
            url: "/api/oauth/callback",
            params: {
                provider: provider,
                code: code,
                state: state
            },
            headers: {
                "Content-Type": "application/json"
            }
        }).then(function(response) {
            if (response.success) {
                // Store authentication data
                if (response.auth && response.auth.access_token) {
                    localStorage.setItem('access_token', response.auth.access_token);
                    localStorage.setItem('refresh_token', response.auth.refresh_token);
                    localStorage.setItem('user', JSON.stringify(response.user));
                }
                return response;
            } else {
                throw new Error(response.message || 'OAuth callback failed');
            }
        }).catch(function(error) {
            console.error("OAuth callback failed:", error);
            throw error;
        });
    },

    /**
     * Unlink OAuth provider from user account
     */
    unlinkProvider: function(provider) {
        const token = localStorage.getItem('access_token');
        
        if (!token) {
            throw new Error('User not authenticated');
        }

        return m.request({
            method: "POST",
            url: "/api/oauth/unlink",
            params: {
                provider: provider
            },
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            }
        }).then(function(response) {
            if (response.success) {
                return response;
            } else {
                throw new Error(response.message || 'Failed to unlink OAuth provider');
            }
        }).catch(function(error) {
            console.error("OAuth unlink failed:", error);
            throw error;
        });
    },

    /**
     * Check if OAuth is enabled
     */
    isOAuthEnabled: function() {
        return this.getProviders()
            .then(function(providers) {
                return providers && providers.length > 0;
            })
            .catch(function() {
                return false;
            });
    },

    /**
     * Get OAuth button configuration for UI
     */
    getProviderConfig: function(provider) {
        const configs = {
            google: {
                name: 'Google',
                icon: 'fab fa-google',
                color: '#db4437',
                textColor: '#ffffff'
            },
            github: {
                name: 'GitHub',
                icon: 'fab fa-github',
                color: '#333333',
                textColor: '#ffffff'
            },
            facebook: {
                name: 'Facebook',
                icon: 'fab fa-facebook-f',
                color: '#4267B2',
                textColor: '#ffffff'
            },
            generic: {
                name: 'OAuth',
                icon: 'fas fa-sign-in-alt',
                color: '#6c757d',
                textColor: '#ffffff'
            }
        };

        return configs[provider] || configs.generic;
    }
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { OAuthService };
} else if (typeof window !== 'undefined') {
    window.OAuthService = OAuthService;
}