import m from "mithril";
import "./bootstrap";
import {layout} from "./components/layout";
import { AuthService } from "./services/AuthserviceService";

// Import Page Components
import WelcomePage from "./pages/WelcomePage";
import ProfilePage from "./pages/ProfilePage";
import LoginFormPage from "./pages/Auth/LoginFormPage";
import RegisterFormPage from "./pages/Auth/RegisterFormPage";
import ForgotPasswordFormPage from "./pages/Auth/ForgotPasswordFormPage";
import ResetPasswordFormPage from "./pages/Auth/ResetPasswordFormPage";
import AdminPage from "./pages/Admin/AdminPage";
import RolesPage from "./pages/Admin/RolesPage";
import UsersPage from "./pages/Admin/UsersPage";
import AddUserPage from "./pages/Admin/AddUserPage";
import EditUserPage from "./pages/Admin/EditUserPage";
import JobsPage from "./pages/Admin/JobsPage";
import ErrorsPage from "./pages/Admin/ErrorsPage";
import SettingsPage from "./pages/Admin/SettingsPage";
import LMSPage from "./pages/Admin/LMSPage";
import EditorWelcomePage from "./pages/Role/EditorWelcomePage";
import MemberWelcomePage from "./pages/Role/MemberWelcomePage";
import StaffWelcomePage from "./pages/Role/StaffWelcomePage";
import PusherTestPage from "./pages/Test/PusherTestPage";
import SseTestPage from "./pages/Test/SseTestPage";
import TestPage from "./pages/Test/TestPage";
import ComponentsPage from "./pages/ComponentsPage";

const root = document.getElementById('app');

// Authentication guard function
function authGuard(component) {
    return {
        view: function(vnode) {
            if (AuthService.isInitializing) {
                return m(".flex.justify-center.items-center.min-h-screen", [
                    m("span.loading.loading-spinner.loading-lg")
                ]);
            }
            if (!AuthService.isLoggedIn()) {
                m.route.set('/login');
                return null;
            }
            return m(component, vnode.attrs);
        }
    };
}

// Admin guard function
function adminGuard(component) {
    return {
        view: function(vnode) {
            if (AuthService.isInitializing) {
                return m(".flex.justify-center.items-center.min-h-screen", [
                    m("span.loading.loading-spinner.loading-lg")
                ]);
            }
            if (!AuthService.isLoggedIn()) {
                m.route.set('/login');
                return null;
            }
            if (!AuthService.isAdmin()) {
                m.route.set('/');
                return null;
            }
            return m(component, vnode.attrs);
        }
    };
}

// Role-based guard function
function roleGuard(component, requiredRoles) {
    if (typeof requiredRoles === 'string') {
        requiredRoles = [requiredRoles];
    }
    
    return {
        view: function(vnode) {
            if (AuthService.isInitializing) {
                return m(".flex.justify-center.items-center.min-h-screen", [
                    m("span.loading.loading-spinner.loading-lg")
                ]);
            }
            if (!AuthService.isLoggedIn()) {
                m.route.set('/login');
                return null;
            }
            if (!AuthService.hasAnyRole(requiredRoles)) {
                m.route.set('/');
                return null;
            }
            return m(component, vnode.attrs);
        }
    };
}

m.route(root, "/", {
    "/": layout(WelcomePage),
    "/login": layout(LoginFormPage),
    "/register": layout(RegisterFormPage),
    "/forgot-password": layout(ForgotPasswordFormPage),
    "/reset-password": layout(ResetPasswordFormPage),
    "/components": layout(ComponentsPage),
    "/pusher-test": layout(authGuard(PusherTestPage)),
    "/sse-test": layout(authGuard(SseTestPage)),
    "/test": layout(TestPage),
    "/profile": layout(authGuard(ProfilePage)),
    // Admin
    "/admin": layout(adminGuard(AdminPage)),
    "/admin/roles": layout(adminGuard(RolesPage)),
    "/admin/users": layout(adminGuard(UsersPage)),
    "/admin/users/add": layout(adminGuard(AddUserPage)),
    "/admin/users/edit/:id": layout(adminGuard(EditUserPage)),
    "/admin/jobs": layout(adminGuard(JobsPage)),
    "/admin/errors": layout(adminGuard(ErrorsPage)),
    "/admin/settings": layout(adminGuard(SettingsPage)),
    "/admin/lms": layout(adminGuard(LMSPage)),
    // Role-based
    "/editor": layout(roleGuard(EditorWelcomePage, 'editor')),
    "/member": layout(roleGuard(MemberWelcomePage, 'member')),
    "/staff": layout(roleGuard(StaffWelcomePage, ['admin', 'editor'])),
});