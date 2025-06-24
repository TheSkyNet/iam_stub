// PusherClient.js - Pusher WebSocket client for real-time functionality

import Pusher from 'pusher-js';

class PusherClient {
    constructor() {
        this.pusher = null;
        this.config = null;
        this.isConnected = false;
        this.channels = new Map();
        this.eventListeners = new Map();
    }

    /**
     * Initialize Pusher client with configuration from backend
     */
    async initialize() {
        try {
            // Get Pusher configuration from backend
            const response = await m.request({
                method: "GET",
                url: "/api/pusher/config",
                withCredentials: true
            });

            if (!response.success) {
                console.error("Failed to get Pusher config:", response.message);
                return false;
            }

            this.config = response.data;

            // Initialize Pusher client
            this.pusher = new Pusher(this.config.key, {
                cluster: this.config.cluster,
                forceTLS: this.config.forceTLS,
                host: this.config.host,
                port: this.config.port,
                disableStats: this.config.disableStats,
                enabledTransports: this.config.enabledTransports,
                authEndpoint: '/api/pusher/auth',
                auth: {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            });

            // Set up connection event listeners
            this.setupConnectionEvents();

            console.log("Pusher client initialized successfully");
            return true;

        } catch (error) {
            console.error("Failed to initialize Pusher client:", error);
            return false;
        }
    }

    /**
     * Set up connection event listeners
     */
    setupConnectionEvents() {
        if (!this.pusher) return;

        this.pusher.connection.bind('connected', () => {
            this.isConnected = true;
            console.log("Pusher connected");
            this.triggerEvent('pusher:connected');
        });

        this.pusher.connection.bind('disconnected', () => {
            this.isConnected = false;
            console.log("Pusher disconnected");
            this.triggerEvent('pusher:disconnected');
        });

        this.pusher.connection.bind('error', (error) => {
            console.error("Pusher connection error:", error);
            this.triggerEvent('pusher:error', error);
        });

        this.pusher.connection.bind('state_change', (states) => {
            console.log("Pusher state changed:", states.previous, "->", states.current);
            this.triggerEvent('pusher:state_change', states);
        });
    }

    /**
     * Subscribe to a channel
     */
    subscribe(channelName, callbacks = {}) {
        if (!this.pusher) {
            console.error("Pusher not initialized");
            return null;
        }

        try {
            const channel = this.pusher.subscribe(channelName);
            this.channels.set(channelName, channel);

            // Bind callbacks if provided
            Object.entries(callbacks).forEach(([event, callback]) => {
                channel.bind(event, callback);
            });

            console.log(`Subscribed to channel: ${channelName}`);
            return channel;

        } catch (error) {
            console.error(`Failed to subscribe to channel ${channelName}:`, error);
            return null;
        }
    }

    /**
     * Unsubscribe from a channel
     */
    unsubscribe(channelName) {
        if (!this.pusher) return;

        try {
            this.pusher.unsubscribe(channelName);
            this.channels.delete(channelName);
            console.log(`Unsubscribed from channel: ${channelName}`);
        } catch (error) {
            console.error(`Failed to unsubscribe from channel ${channelName}:`, error);
        }
    }

    /**
     * Get a subscribed channel
     */
    getChannel(channelName) {
        return this.channels.get(channelName);
    }

    /**
     * Bind event listener to a channel
     */
    bind(channelName, eventName, callback) {
        const channel = this.getChannel(channelName);
        if (channel) {
            channel.bind(eventName, callback);
            console.log(`Bound event ${eventName} to channel ${channelName}`);
        } else {
            console.error(`Channel ${channelName} not found`);
        }
    }

    /**
     * Unbind event listener from a channel
     */
    unbind(channelName, eventName, callback = null) {
        const channel = this.getChannel(channelName);
        if (channel) {
            channel.unbind(eventName, callback);
            console.log(`Unbound event ${eventName} from channel ${channelName}`);
        }
    }

    /**
     * Subscribe to a private channel
     */
    subscribePrivate(channelName, callbacks = {}) {
        return this.subscribe(`private-${channelName}`, callbacks);
    }

    /**
     * Subscribe to a presence channel
     */
    subscribePresence(channelName, callbacks = {}) {
        const channel = this.subscribe(`presence-${channelName}`, callbacks);

        if (channel) {
            // Bind presence-specific events
            channel.bind('pusher:subscription_succeeded', (members) => {
                console.log(`Presence channel ${channelName} subscription succeeded. Members:`, members);
                if (callbacks.onSubscriptionSucceeded) {
                    callbacks.onSubscriptionSucceeded(members);
                }
            });

            channel.bind('pusher:member_added', (member) => {
                console.log(`Member added to ${channelName}:`, member);
                if (callbacks.onMemberAdded) {
                    callbacks.onMemberAdded(member);
                }
            });

            channel.bind('pusher:member_removed', (member) => {
                console.log(`Member removed from ${channelName}:`, member);
                if (callbacks.onMemberRemoved) {
                    callbacks.onMemberRemoved(member);
                }
            });
        }

        return channel;
    }

    /**
     * Add global event listener
     */
    addEventListener(event, callback) {
        if (!this.eventListeners.has(event)) {
            this.eventListeners.set(event, []);
        }
        this.eventListeners.get(event).push(callback);
    }

    /**
     * Remove global event listener
     */
    removeEventListener(event, callback) {
        if (this.eventListeners.has(event)) {
            const listeners = this.eventListeners.get(event);
            const index = listeners.indexOf(callback);
            if (index > -1) {
                listeners.splice(index, 1);
            }
        }
    }

    /**
     * Trigger global event
     */
    triggerEvent(event, data = null) {
        if (this.eventListeners.has(event)) {
            this.eventListeners.get(event).forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`Error in event listener for ${event}:`, error);
                }
            });
        }
    }

    /**
     * Get connection state
     */
    getState() {
        return this.pusher ? this.pusher.connection.state : 'disconnected';
    }

    /**
     * Check if connected
     */
    isConnectedToPusher() {
        return this.isConnected;
    }

    /**
     * Disconnect from Pusher
     */
    disconnect() {
        if (this.pusher) {
            this.pusher.disconnect();
            this.channels.clear();
            this.isConnected = false;
            console.log("Pusher disconnected");
        }
    }

    /**
     * Get socket ID
     */
    getSocketId() {
        return this.pusher ? this.pusher.connection.socket_id : null;
    }
}

// Create global instance
const pusherClient = new PusherClient();

// Make available globally for browser compatibility
if (typeof window !== 'undefined') {
    window.PusherClient = PusherClient;
    window.pusherClient = pusherClient;
}

// ES6 module exports
export { PusherClient, pusherClient };
