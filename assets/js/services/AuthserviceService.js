const AuthService = {
    baseUrl: '/auth',

    // Authentication state
    user: null,
    accessToken: null,
    refreshToken: null,
    isAuthenticated: false,

    /**
     * Initialize the auth service - check for existing tokens on page load
     */
    init: function() {
        this.loadTokensFromStorage();
        if (this.accessToken) {
            this.validateCurrentUser();
        }
    },

    /**
     * Login with email and password
     */
    login: function(email, password, rememberMe = false) {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/login`,
            body: { email, password, remember_me: rememberMe }
        }).then((response) => {
            if (response.success && response.data) {
                this.setAuthData(response.data);
                return response;
            }
            throw new Error(response.message || 'Login failed');
        });
    },

    /**
     * Register new user
     */
    register: function(name, email, password) {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/register`,
            body: { name, email, password }
        }).then((response) => {
            if (response.success && response.data) {
                this.setAuthData(response.data);
                return response;
            }
            throw new Error(response.message || 'Registration failed');
        });
    },

    /**
     * Logout user
     */
    logout: function() {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/logout`,
            headers: this.getAuthHeaders()
        }).then((response) => {
            this.clearAuthData();
            return response;
        }).catch((error) => {
            // Clear auth data even if logout request fails
            this.clearAuthData();
            throw error;
        });
    },

    /**
     * Refresh access token
     */
    refreshAccessToken: function() {
        if (!this.refreshToken) {
            return Promise.reject(new Error('No refresh token available'));
        }

        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/refresh-token`,
            body: { refresh_token: this.refreshToken }
        }).then((response) => {
            if (response.success && response.data) {
                this.setAuthData(response.data);
                return response;
            }
            throw new Error(response.message || 'Token refresh failed');
        }).catch((error) => {
            this.clearAuthData();
            throw error;
        });
    },

    /**
     * Get user profile
     */
    getProfile: function() {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/profile`,
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Update user profile
     */
    updateProfile: function(data) {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/update-profile`,
            body: data,
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Generate API key
     */
    generateApiKey: function() {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/generate-api-key`,
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Get current user info
     */
    getCurrentUser: function() {
        return m.request({
            method: 'GET',
            url: `${this.baseUrl}/user`,
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Validate current user and refresh token if needed
     */
    validateCurrentUser: function() {
        return this.getCurrentUser()
            .then((response) => {
                if (response.success !== false) {
                    this.user = response;
                    this.isAuthenticated = true;
                }
                return response;
            })
            .catch((error) => {
                // Try to refresh token if current token is invalid
                if (this.refreshToken) {
                    return this.refreshAccessToken()
                        .then(() => this.getCurrentUser())
                        .then((response) => {
                            this.user = response;
                            this.isAuthenticated = true;
                            return response;
                        });
                }
                this.clearAuthData();
                throw error;
            });
    },

    /**
     * Set authentication data
     */
    setAuthData: function(data) {
        this.user = data.user || null;
        this.accessToken = data.access_token || null;
        this.refreshToken = data.refresh_token || null;
        this.isAuthenticated = !!this.accessToken;

        // Save to localStorage
        if (this.accessToken) {
            localStorage.setItem('access_token', this.accessToken);
        }
        if (this.refreshToken) {
            localStorage.setItem('refresh_token', this.refreshToken);
        }
        if (this.user) {
            localStorage.setItem('user', JSON.stringify(this.user));
        }
    },

    /**
     * Clear authentication data
     */
    clearAuthData: function() {
        this.user = null;
        this.accessToken = null;
        this.refreshToken = null;
        this.isAuthenticated = false;

        // Clear from localStorage
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
        localStorage.removeItem('user');
    },

    /**
     * Load tokens from localStorage
     */
    loadTokensFromStorage: function() {
        this.accessToken = localStorage.getItem('access_token');
        this.refreshToken = localStorage.getItem('refresh_token');
        const userStr = localStorage.getItem('user');
        if (userStr) {
            try {
                this.user = JSON.parse(userStr);
            } catch (e) {
                console.error('Failed to parse user data from localStorage');
            }
        }
        this.isAuthenticated = !!this.accessToken;
    },

    /**
     * Get authorization headers
     */
    getAuthHeaders: function() {
        if (this.accessToken) {
            return {
                'Authorization': `Bearer ${this.accessToken}`
            };
        }
        return {};
    },

    /**
     * Check if user is authenticated
     */
    isLoggedIn: function() {
        return this.isAuthenticated && !!this.accessToken;
    },

    /**
     * Get current user
     */
    getUser: function() {
        return this.user;
    },

    /**
     * Generate QR code for login
     */
    generateQRCode: function() {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/generate-qr-code`
        });
    },

    /**
     * Check QR code authentication status
     */
    checkQRStatus: function(sessionToken) {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/check-qr-status`,
            body: { session_token: sessionToken }
        });
    },

    /**
     * Authenticate QR code session (called from mobile)
     */
    authenticateQR: function(sessionToken) {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/authenticate-qr`,
            headers: this.getAuthHeaders(),
            body: { session_token: sessionToken }
        });
    },

    /**
     * Start QR login polling
     */
    startQRPolling: function(sessionToken, onSuccess, onError, onExpired) {
        const pollInterval = 2000; // Poll every 2 seconds
        const maxAttempts = 150; // 5 minutes (150 * 2 seconds)
        let attempts = 0;

        const poll = () => {
            if (attempts >= maxAttempts) {
                onExpired && onExpired();
                return;
            }

            this.checkQRStatus(sessionToken)
                .then((response) => {
                    if (response.success && response.status === 'authenticated') {
                        // Set auth data and call success callback
                        this.setAuthData(response.data);
                        onSuccess && onSuccess(response);
                    } else if (response.success && response.status === 'pending') {
                        // Continue polling
                        attempts++;
                        setTimeout(poll, pollInterval);
                    } else if (response.status === 'expired') {
                        onExpired && onExpired();
                    } else {
                        onError && onError(response);
                    }
                })
                .catch((error) => {
                    onError && onError(error);
                });
        };

        // Start polling
        poll();
    },

    /**
     * Generate QR code for mobile login (reverse flow)
     * Mobile device generates QR code for desktop to scan
     */
    generateMobileQRCode: function() {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/generate-mobile-qr-code`,
            headers: this.getAuthHeaders()
        });
    },

    /**
     * Check mobile QR code authentication status
     * Mobile device polls this to check if desktop has authenticated
     */
    checkMobileQRStatus: function(sessionToken) {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/check-mobile-qr-status`,
            body: { session_token: sessionToken }
        });
    },

    /**
     * Authenticate mobile QR code session (called from desktop)
     * Desktop scans mobile QR code and authenticates the mobile session
     */
    authenticateMobileQR: function(sessionToken) {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/authenticate-mobile-qr`,
            headers: this.getAuthHeaders(),
            body: { session_token: sessionToken }
        });
    },

    /**
     * Start mobile QR login polling (mobile polls for desktop authentication)
     */
    startMobileQRPolling: function(sessionToken, onSuccess, onError, onExpired) {
        const pollInterval = 2000; // Poll every 2 seconds
        const maxAttempts = 150; // 5 minutes (150 * 2 seconds)
        let attempts = 0;

        const poll = () => {
            if (attempts >= maxAttempts) {
                onExpired && onExpired();
                return;
            }

            this.checkMobileQRStatus(sessionToken)
                .then((response) => {
                    if (response.success && response.status === 'authenticated') {
                        // Set auth data and call success callback
                        this.setAuthData(response.data);
                        onSuccess && onSuccess(response);
                    } else if (response.success && response.status === 'pending') {
                        // Continue polling
                        attempts++;
                        setTimeout(poll, pollInterval);
                    } else if (response.status === 'expired') {
                        onExpired && onExpired();
                    } else {
                        onError && onError(response);
                    }
                })
                .catch((error) => {
                    onError && onError(error);
                });
        };

        // Start polling
        poll();
    }
};

export {AuthService};
