// Bootstrap file for global dependencies
window.m = require('mithril');

// Import and initialize PusherClient globally
import { pusherClient } from './components/PusherClient';
window.pusherClient = pusherClient;

// Global error handler and toast notifications (DaisyUI)
import { initErrorHandler, showToast } from './lib/errorHandler';
initErrorHandler();
window.showToast = showToast;