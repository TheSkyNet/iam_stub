require("./bootstrap")
import {adminLayout} from "./Admin/adminLayout";
import {AdminUserList} from "./Admin/AdminUsers";
import {Auth, LoginList} from "./Login/LoginModule";
import {AdminSettings} from "./Admin/AdminSettings";
import AdminDashboard from "./Admin/AdminDashboard";

let root = document.getElementById('main');
m.request({
        method: "GET",
        url: "/auth",
        headers: {
            'Accept': `application/json`
        },
        withCredentials: true,
    }
).then(function (result) {
      Object.assign(Auth, result);
      if(Auth.id){
          m.route(root, "/", {
              "/": adminLayout(AdminDashboard),
              "/user": adminLayout(AdminUserList),
              "/user/:id": adminLayout(AdminUserList),
              "/settings": adminLayout(AdminSettings),
          });
      }else {
          m.route(root, "/", {
              "/": LoginList
          });
      }


  });
