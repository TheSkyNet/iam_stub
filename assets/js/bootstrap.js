// Bootstrap file for global dependencies
window.m = require('mithril');

// Import and initialize PusherClient globally
import { pusherClient } from './lib/PusherClient';
window.pusherClient = pusherClient;

// Initialize global error handling
import { initGlobalErrorHandling } from './lib/errorHandler';
initGlobalErrorHandling();

// Initialize AuthService
import { AuthService } from './services/AuthserviceService';
AuthService.init();