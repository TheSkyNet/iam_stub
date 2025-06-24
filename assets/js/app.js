require("./bootstrap")
import {LoginForm, RegisterForm, ForgotPasswordForm, LoginList} from "./Login/LoginModule";
import {layout} from "./components/layout";
import {Welcome} from "./components/Welcome";

const root = document.getElementById('app');


m.route(root, "/", {
    "/": layout(Welcome),
    "/login": layout(LoginForm),
    "/register": layout(RegisterForm),
    "/forgot-password": layout(ForgotPasswordForm),
});
