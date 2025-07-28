
const AuthService = {
    baseUrl: '/auth',

    // Authentication state
    user: null,
    accessToken: null,
    refreshToken: null,
    isAuthenticated: false,
    
    // Auto-logout configuration
    tokenCheckInterval: null,
    activityTimeout: null,
    lastActivity: Date.now(),
    inactivityTimeoutMs: 30 * 60 * 1000, // 30 minutes of inactivity
    tokenCheckIntervalMs: 5 * 60 * 1000, // Check token validity every 5 minutes

    /**
     * Initialize the auth service - check for existing tokens on page load
     */
    init: function() {
        this.loadTokensFromStorage();
        if (this.accessToken) {
            this.validateCurrentUser();
        }
        this.startAutoLogout();
        this.setupActivityTracking();
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
                this.startAutoLogout(); // Start auto-logout after successful login
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
                this.startAutoLogout(); // Start auto-logout after successful registration
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
                    // Ensure consistent user data structure
                    // Backend returns identity directly, so we use it as the user object
                    this.user = response;
                    this.isAuthenticated = true;
                    
                    // Update localStorage with the validated user data
                    if (this.user) {
                        localStorage.setItem('user', JSON.stringify(this.user));
                    }
                }
                return response;
            })
            .catch((error) => {
                // Try to refresh token if current token is invalid
                if (this.refreshToken) {
                    return this.refreshAccessToken()
                        .then(() => this.getCurrentUser())
                        .then((response) => {
                            // Ensure consistent user data structure
                            this.user = response;
                            this.isAuthenticated = true;
                            
                            // Update localStorage with the validated user data
                            if (this.user) {
                                localStorage.setItem('user', JSON.stringify(this.user));
                            }
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

        // Restart auto-logout functionality with new tokens
        if (this.isAuthenticated) {
            this.startAutoLogout();
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

        // Stop auto-logout functionality
        this.stopAutoLogout();

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
                // Only set authenticated state if we have both token and user data
                // This prevents timing issues where guards check auth before user data is loaded
                this.isAuthenticated = !!(this.accessToken && this.user);
            } catch (e) {
                console.error('Failed to parse user data from localStorage');
                this.isAuthenticated = false;
            }
        } else {
            // Don't set authenticated state without user data
            this.isAuthenticated = false;
        }
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
     * Check if user has a specific role
     */
    hasRole: function(roleName) {
        if (!this.isLoggedIn() || !this.user || !this.user.roles) {
            return false;
        }
        return this.user.roles.includes(roleName);
    },

    /**
     * Check if user has any of the specified roles
     */
    hasAnyRole: function(roleNames) {
        if (!this.isLoggedIn() || !this.user || !this.user.roles) {
            return false;
        }
        return roleNames.some(role => this.user.roles.includes(role));
    },

    /**
     * Check if user has all of the specified roles
     */
    hasAllRoles: function(roleNames) {
        if (!this.isLoggedIn() || !this.user || !this.user.roles) {
            return false;
        }
        return roleNames.every(role => this.user.roles.includes(role));
    },

    /**
     * Check if user is an admin
     */
    isAdmin: function() {
        return this.hasRole('admin');
    },

    /**
     * Get user roles
     */
    getUserRoles: function() {
        if (!this.isLoggedIn() || !this.user || !this.user.roles) {
            return [];
        }
        return this.user.roles;
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
    },

    /**
     * Resend password reset email
     */
    resendPasswordReset: function(email) {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/forgot-password`,
            body: { email: email }
        });
    },

    /**
     * Resend email verification
     */
    resendEmailVerification: function(email) {
        return m.request({
            method: 'POST',
            url: `${this.baseUrl}/verify-email`,
            body: { email: email }
        });
    },

    /**
     * Start auto-logout functionality
     */
    startAutoLogout: function() {
        // Clear any existing intervals
        this.stopAutoLogout();
        
        // Only start auto-logout if user is authenticated
        if (this.isLoggedIn()) {
            // Check token validity periodically
            this.tokenCheckInterval = setInterval(() => {
                this.checkTokenValidity();
            }, this.tokenCheckIntervalMs);
            
            // Check for inactivity
            this.activityTimeout = setInterval(() => {
                this.checkInactivity();
            }, 60000); // Check every minute
        }
    },

    /**
     * Stop auto-logout functionality
     */
    stopAutoLogout: function() {
        if (this.tokenCheckInterval) {
            clearInterval(this.tokenCheckInterval);
            this.tokenCheckInterval = null;
        }
        if (this.activityTimeout) {
            clearInterval(this.activityTimeout);
            this.activityTimeout = null;
        }
    },

    /**
     * Setup activity tracking
     */
    setupActivityTracking: function() {
        // Track user activity
        const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        const updateActivity = () => {
            this.lastActivity = Date.now();
        };
        
        // Add event listeners for activity tracking
        activityEvents.forEach(event => {
            document.addEventListener(event, updateActivity, true);
        });
    },

    /**
     * Check token validity
     */
    checkTokenValidity: function() {
        if (!this.isLoggedIn()) {
            return;
        }

        // Try to get current user to validate token
        this.getCurrentUser()
            .then((response) => {
                if (response.success !== false) {
                    // Token is valid, update user data
                    this.user = response;
                    this.isAuthenticated = true;
                } else {
                    // Token is invalid, try to refresh
                    this.handleTokenExpiration();
                }
            })
            .catch((error) => {
                // Token validation failed, try to refresh
                this.handleTokenExpiration();
            });
    },

    /**
     * Handle token expiration
     */
    handleTokenExpiration: function() {
        if (this.refreshToken) {
            // Try to refresh the token
            this.refreshAccessToken()
                .then(() => {
                    console.log('Token refreshed successfully');
                })
                .catch((error) => {
                    console.log('Token refresh failed, logging out');
                    this.handleAutoLogout('Token expired and refresh failed');
                });
        } else {
            // No refresh token available, logout
            this.handleAutoLogout('Token expired');
        }
    },

    /**
     * Check for user inactivity
     */
    checkInactivity: function() {
        if (!this.isLoggedIn()) {
            return;
        }

        const now = Date.now();
        const timeSinceLastActivity = now - this.lastActivity;

        if (timeSinceLastActivity > this.inactivityTimeoutMs) {
            this.handleAutoLogout('User inactive for too long');
        }
    },

    /**
     * Handle automatic logout
     */
    handleAutoLogout: function(reason) {
        console.log('Auto-logout triggered:', reason);
        
        // Clear auth data
        this.clearAuthData();
        
        // Stop auto-logout intervals
        this.stopAutoLogout();
        
        // Redirect to login page if not already there
        if (m.route.get() !== '/login') {
            m.route.set('/login');
        }
        
        // Show notification to user
        this.showLogoutNotification(reason);
        
        // Trigger redraw
        m.redraw();
    },

    /**
     * Show logout notification to user
     */
    showLogoutNotification: function(reason) {
        // You can customize this to use your preferred notification system
        // For now, we'll use a simple alert
        setTimeout(() => {
            if (reason.includes('inactive')) {
                alert('You have been logged out due to inactivity.');
            } else if (reason.includes('expired')) {
                alert('Your session has expired. Please log in again.');
            } else {
                alert('You have been logged out.');
            }
        }, 100);
    }
};

export {AuthService};
