import {AllList} from "./All/AllModule";
import {ProjectList, ProjectOne} from "./Project/ProjectModule";
import {PackageList} from "./Package/PackageModule";
import {PostList} from "./Post/PostsModule";
import {AboutModule} from "./About/AboutModule";
import {CodeList, CodeOne} from "./Code/CodeModule";
import {LoginList} from "./Login/LoginModule";
import {layout} from "./components/layout";

require('./ImaLab.js');
const root = document.getElementById('app');


m.route(root, "/", {
    "/": layout(AllList),
    "/projects": layout(ProjectList),
    "/project/:key": layout(ProjectOne),
    "/packages": layout(PackageList),
    "/posts": layout(PostList),
    "/posts/:id": layout(ProjectList),
    "/code/:id": layout(CodeOne),
    "/code": layout(CodeList),
    "/about": layout(AboutModule),
    "/login": layout(LoginList),
});

