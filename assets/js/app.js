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

m.route(root, "/", {
    "/": layout(Welcome),
    "/login": layout(LoginForm),
    "/register": layout(RegisterForm),
    "/forgot-password": layout(ForgotPasswordForm),
    "/pusher-test": layout(authGuard(PusherTest)),
    "/profile": layout(authGuard(Profile)),
});