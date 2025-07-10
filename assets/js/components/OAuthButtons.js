// OAuthButtons.js

const {OAuthService} = require("../services/OAuthService");
const {MessageDisplay} = require("./MessageDisplay");

const OAuthButtons = {
    providers: [],
    isLoading: false,
    isEnabled: false,

    oninit: function() {
        // Load OAuth providers on component initialization
        this.loadProviders();
    },

    loadProviders: function() {
        OAuthButtons.isLoading = true;
        
        OAuthService.getProviders()
            .then(function(providers) {
                OAuthButtons.providers = providers || [];
                OAuthButtons.isEnabled = providers && providers.length > 0;
                OAuthButtons.isLoading = false;
                m.redraw();
            })
            .catch(function(error) {
                console.error("Failed to load OAuth providers:", error);
                OAuthButtons.isEnabled = false;
                OAuthButtons.isLoading = false;
                m.redraw();
            });
    },

    handleOAuthLogin: function(provider) {
        MessageDisplay.setMessage('Redirecting to ' + provider + '...', 'info');
        
        OAuthService.initiateLogin(provider)
            .catch(function(error) {
                MessageDisplay.setMessage('Failed to initiate OAuth login: ' + error.message, 'error');
                console.error("OAuth login failed:", error);
            });
    },

    view: function() {
        if (!OAuthButtons.isEnabled || OAuthButtons.isLoading) {
            return null;
        }

        return m('div.oauth-buttons', [
            m('div.oauth-divider', [
                m('hr.oauth-line'),
                m('span.oauth-text', 'or continue with'),
                m('hr.oauth-line')
            ]),
            
            m('div.oauth-providers', 
                OAuthButtons.providers.map(function(provider) {
                    const config = OAuthService.getProviderConfig(provider.name);
                    
                    return m('button.oauth-button', {
                        key: provider.name,
                        onclick: function() {
                            OAuthButtons.handleOAuthLogin(provider.name);
                        },
                        style: {
                            backgroundColor: config.color,
                            color: config.textColor,
                            border: 'none',
                            padding: '12px 20px',
                            margin: '8px 0',
                            borderRadius: '6px',
                            cursor: 'pointer',
                            width: '100%',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            fontSize: '14px',
                            fontWeight: '500',
                            transition: 'all 0.2s ease',
                            textDecoration: 'none'
                        },
                        onmouseover: function(e) {
                            e.target.style.opacity = '0.9';
                            e.target.style.transform = 'translateY(-1px)';
                        },
                        onmouseout: function(e) {
                            e.target.style.opacity = '1';
                            e.target.style.transform = 'translateY(0)';
                        }
                    }, [
                        m('i', {
                            class: config.icon,
                            style: {
                                marginRight: '10px',
                                fontSize: '16px'
                            }
                        }),
                        'Continue with ' + config.name
                    ]);
                })
            )
        ]);
    }
};

// OAuth Callback Handler Component
const OAuthCallback = {
    isProcessing: false,
    
    oninit: function(vnode) {
        // Extract parameters from URL
        const urlParams = new URLSearchParams(window.location.search);
        const provider = vnode.attrs.provider;
        const code = urlParams.get('code');
        const state = urlParams.get('state');
        const error = urlParams.get('error');

        if (error) {
            MessageDisplay.setMessage('OAuth error: ' + error, 'error');
            setTimeout(() => {
                m.route.set('/login');
            }, 2000);
            return;
        }

        if (!provider || !code || !state) {
            MessageDisplay.setMessage('Invalid OAuth callback parameters', 'error');
            setTimeout(() => {
                m.route.set('/login');
            }, 2000);
            return;
        }

        this.handleCallback(provider, code, state);
    },

    handleCallback: function(provider, code, state) {
        OAuthCallback.isProcessing = true;
        
        OAuthService.handleCallback(provider, code, state)
            .then(function(response) {
                MessageDisplay.setMessage(response.message || 'OAuth login successful!', 'success');
                setTimeout(() => {
                    m.route.set('/');
                    m.redraw();
                }, 1500);
            })
            .catch(function(error) {
                MessageDisplay.setMessage('OAuth login failed: ' + error.message, 'error');
                console.error("OAuth callback failed:", error);
                setTimeout(() => {
                    m.route.set('/login');
                }, 2000);
            })
            .finally(function() {
                OAuthCallback.isProcessing = false;
                m.redraw();
            });
    },

    view: function() {
        return m('div.oauth-callback', [
            m('div.loading-spinner', [
                m('div.spinner'),
                m('p', OAuthCallback.isProcessing ? 'Processing OAuth login...' : 'Redirecting...')
            ])
        ]);
    }
};

// OAuth Account Management Component (for user profile/settings)
const OAuthAccountManager = {
    providers: [],
    userProviders: [],
    isLoading: false,

    oninit: function() {
        this.loadData();
    },

    loadData: function() {
        OAuthAccountManager.isLoading = true;
        
        Promise.all([
            OAuthService.getProviders(),
            this.getUserOAuthProviders()
        ]).then(function([providers, userProviders]) {
            OAuthAccountManager.providers = providers || [];
            OAuthAccountManager.userProviders = userProviders || [];
            OAuthAccountManager.isLoading = false;
            m.redraw();
        }).catch(function(error) {
            console.error("Failed to load OAuth data:", error);
            OAuthAccountManager.isLoading = false;
            m.redraw();
        });
    },

    getUserOAuthProviders: function() {
        // This would typically come from user profile data
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        return Promise.resolve(user.oauth_provider ? [user.oauth_provider] : []);
    },

    unlinkProvider: function(provider) {
        if (!confirm('Are you sure you want to unlink ' + provider + '?')) {
            return;
        }

        OAuthService.unlinkProvider(provider)
            .then(function(response) {
                MessageDisplay.setMessage(response.message || 'Provider unlinked successfully', 'success');
                OAuthAccountManager.loadData();
            })
            .catch(function(error) {
                MessageDisplay.setMessage('Failed to unlink provider: ' + error.message, 'error');
                console.error("OAuth unlink failed:", error);
            });
    },

    view: function() {
        if (OAuthAccountManager.isLoading) {
            return m('div.loading', 'Loading OAuth settings...');
        }

        return m('div.oauth-account-manager', [
            m('h3', 'Connected Accounts'),
            m('p.text-muted', 'Manage your OAuth provider connections'),
            
            m('div.oauth-providers-list',
                OAuthAccountManager.providers.map(function(provider) {
                    const config = OAuthService.getProviderConfig(provider.name);
                    const isConnected = OAuthAccountManager.userProviders.includes(provider.name);
                    
                    return m('div.oauth-provider-item', {
                        key: provider.name,
                        style: {
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'space-between',
                            padding: '15px',
                            border: '1px solid #e0e0e0',
                            borderRadius: '8px',
                            marginBottom: '10px'
                        }
                    }, [
                        m('div.provider-info', {
                            style: {
                                display: 'flex',
                                alignItems: 'center'
                            }
                        }, [
                            m('i', {
                                class: config.icon,
                                style: {
                                    fontSize: '20px',
                                    marginRight: '15px',
                                    color: config.color
                                }
                            }),
                            m('span', config.name)
                        ]),
                        
                        m('div.provider-actions', [
                            isConnected 
                                ? m('button.btn.btn-outline-danger.btn-sm', {
                                    onclick: function() {
                                        OAuthAccountManager.unlinkProvider(provider.name);
                                    }
                                }, 'Disconnect')
                                : m('button.btn.btn-outline-primary.btn-sm', {
                                    onclick: function() {
                                        OAuthButtons.handleOAuthLogin(provider.name);
                                    }
                                }, 'Connect')
                        ])
                    ]);
                })
            )
        ]);
    }
};

// Export components
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { 
        OAuthButtons,
        OAuthCallback,
        OAuthAccountManager
    };
} else if (typeof window !== 'undefined') {
    window.OAuthButtons = OAuthButtons;
    window.OAuthCallback = OAuthCallback;
    window.OAuthAccountManager = OAuthAccountManager;
}