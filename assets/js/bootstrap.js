// Bootstrap file for global dependencies
window.m = require('mithril');

// Import and initialize PusherClient globally
import { pusherClient } from './lib/PusherClient';
window.pusherClient = pusherClient;