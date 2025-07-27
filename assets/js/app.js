require("./bootstrap")
import {LoginForm, RegisterForm, ForgotPasswordForm} from "./Login/LoginModule";
import {layout} from "./components/layout";
import {Welcome} from "./components/Welcome";
import {PusherTest} from "./components/PusherTest";
import {Profile} from "./components/Profile";
const {AuthService} = require("./services/AuthserviceService");

const root = document.getElementById('app');

// Authentication guard function
function authGuard(component) {
    return {
        oninit: function(vnode) {
            // Check if user is authenticated
            if (!AuthService.isLoggedIn()) {
                // Redirect to login page if not authenticated
                m.route.set('/login');
                return;
            }
            // Call component's oninit if it exists
            if (component.oninit) {
                component.oninit.call(component, vnode);
            }
        },
        view: function(vnode) {
            // Only render if authenticated
            if (!AuthService.isLoggedIn()) {
                return null; // Don't render anything while redirecting
            }
            // Render the protected component
            return m(component, vnode.attrs);
        }
    };
}

// Admin guard function - checks authentication and admin role
function adminGuard(component) {
    return {
        oninit: function(vnode) {
            // Check if user is authenticated
            if (!AuthService.isLoggedIn()) {
                // Redirect to login page if not authenticated
                m.route.set('/login');
                return;
            }
            // Check if user has admin role
            if (!AuthService.isAdmin()) {
                // Redirect to home page if not admin
                m.route.set('/');
                return;
            }
            // Call component's oninit if it exists
            if (component.oninit) {
                component.oninit.call(component, vnode);
            }
        },
        view: function(vnode) {
            // Only render if authenticated and admin
            if (!AuthService.isLoggedIn() || !AuthService.isAdmin()) {
                return null; // Don't render anything while redirecting
            }
            // Render the protected component
            return m(component, vnode.attrs);
        }
    };
}

// Role-based guard function - checks authentication and specific roles
function roleGuard(component, requiredRoles) {
    // Ensure requiredRoles is an array
    if (typeof requiredRoles === 'string') {
        requiredRoles = [requiredRoles];
    }
    
    return {
        oninit: function(vnode) {
            // Check if user is authenticated
            if (!AuthService.isLoggedIn()) {
                // Redirect to login page if not authenticated
                m.route.set('/login');
                return;
            }
            // Check if user has any of the required roles
            if (!AuthService.hasAnyRole(requiredRoles)) {
                // Redirect to home page if user doesn't have required roles
                m.route.set('/');
                return;
            }
            // Call component's oninit if it exists
            if (component.oninit) {
                component.oninit.call(component, vnode);
            }
        },
        view: function(vnode) {
            // Only render if authenticated and has required roles
            if (!AuthService.isLoggedIn() || !AuthService.hasAnyRole(requiredRoles)) {
                return null; // Don't render anything while redirecting
            }
            // Render the protected component
            return m(component, vnode.attrs);
        }
    };
}

m.route(root, "/", {
    "/": layout(Welcome),
    "/login": layout(LoginForm),
    "/register": layout(RegisterForm),
    "/forgot-password": layout(ForgotPasswordForm),
    "/pusher-test": layout(authGuard(PusherTest)),
    "/profile": layout(authGuard(Profile)),
    // Admin-only routes
    "/admin": layout(adminGuard(Welcome)), // Example admin route
    "/admin/roles": layout(adminGuard(Welcome)), // Role management (admin only)
    // Role-based routes examples
    "/editor": layout(roleGuard(Welcome, 'editor')), // Editor only
    "/member": layout(roleGuard(Welcome, 'member')), // Member only
    "/staff": layout(roleGuard(Welcome, ['admin', 'editor'])), // Admin or Editor
});