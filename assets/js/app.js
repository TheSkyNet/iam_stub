require("./bootstrap")
import {LoginList} from "./Login/LoginModule";
import {layout} from "./components/layout";
import {Welcome} from "./components/Welcome";

const root = document.getElementById('app');


m.route(root, "/", {
    "/": layout(Welcome),
    "/login": layout(LoginList),
});
