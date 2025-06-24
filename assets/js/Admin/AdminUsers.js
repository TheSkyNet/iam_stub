var AdminUser = {
    list: [],
    user: {
        name: '',
        email: '',
        password: '',
    },

    current: [],
    loadList: function () {
        return m.request({
            method: "GET",
            url: "/api/v1/user",
            withCredentials: true,
        })
            .then(function (result) {
                AdminUser.list = result
            })
    },
    delete(id) {
        return m.request({
                method: "DELETE",
                url: "/api/v1/user/" + id,
                withCredentials: true,
            }
        ).then(function (result) {
            AdminUser.loadList()
        })
    },

    load: function (id) {
        return m.request({
                method: "GET",
                url: "/api/v1/user/" + id,
                withCredentials: true,
            }
        ).then(function (result) {
            AdminUser.user = result
        })
    },
    addUser() {
        return m.request({
                method: "POST",
                url: "/api/v1/user",
                headers: {
                    'X-CSRF-Token': this.CSRF,
                    'Accept': `application/json`
                },
                withCredentials: true,
                body: AdminUser.user,
            }
        ).then(function (result) {
            AdminUser.loadList()
        })
    },
    updateUser() {
        return m.request({
                method: "POST",
                url: "/api/v1/user",
                headers: {
                    'X-CSRF-Token': this.CSRF,
                    'Accept': `application/json`
                },
                withCredentials: true,
                body: AdminUser.user,
            }
        ).then(function (result) {
            AdminUser.loadList()
        })
    }
};

var AdminUserList = {
    oninit: (vnode) => {
        if (vnode.attrs.id) {
            AdminUser.load(vnode.attrs.id)
        }
        AdminUser.loadList()

    },
    view: function (vnode) {
        return m("div", {"class": "row"},
            [
                m('div', {class: 'col-12'}, m("h1",
                    "Users"
                )),
                m("div", {class: 'col-6'},
                    m("div", {"class": "form"},
                        m("form", {
                                onsubmit: (e) => {
                                    event.preventDefault();
                                    if (vnode.attrs.id) {
                                        AdminUser.updateUser()
                                    } else {
                                        AdminUser.addUser()
                                    }

                                }
                            },
                            m("div", {"class": "input-group mb-3"},
                                [
                                    m("input", {
                                        "class": "form-control form-control-lg",
                                        "type": "text",
                                        value: AdminUser.user.name,
                                        onchange: (e) => {
                                            AdminUser.user.name = e.target.value;
                                        },
                                        "placeholder": "name"
                                    }), m("div", {"class": "input-group-append"},
                                    m("span", {"class": "input-group-text", "id": "basic-addon2"},
                                        "Name"
                                    )
                                )
                                ]
                            ),

                            m("div", {"class": "input-group mb-3"},
                                [
                                    m("input", {
                                        "class": "form-control form-control-lg",
                                        "type": "text",
                                        "placeholder": "email",
                                        value: AdminUser.user.email,
                                        onchange: (e) => {
                                            AdminUser.user.email = e.target.value;
                                        },
                                    }), m("div", {"class": "input-group-append"},
                                    m("span", {"class": "input-group-text", "id": "basic-addon2"},
                                        "email"
                                    )
                                )
                                ]
                            ),
                           m("div", {"class": "input-group mb-3"},
                                [
                                    m("input", {
                                        "class": "form-control form-control-lg",
                                        "type": "password",
                                        "placeholder": "password",
                                        value: AdminUser.user.password,
                                        onchange: (e) => {
                                            AdminUser.user.password = e.target.value;
                                        },
                                    }), m("div", {"class": "input-group-append"},
                                    m("span", {"class": "input-group-text", "id": "basic-addon2"},
                                        "password"
                                    )
                                )
                                ]
                            ),

                            m("button", {"class": "btn btn-primary btn-lg btn-block", "type": "submit"},
                                "save"
                            )
                        )
                    ),
                ),
                m("div", {class: 'col-6'},

                    m("div", {"class": "container"},
                        m("div", {"class": "row"},
                            m("div", {"class": "col-12"},
                                m("table", {"class": "table table-bordered"},
                                    [
                                        m("thead",
                                            m("tr",
                                                [

                                                    m("th", {"scope": "col"},
                                                        "Article Name"
                                                    ),
                                                    m("th", {"scope": "col"},
                                                        "Actions"
                                                    )
                                                ]
                                            )
                                        ),
                                        m("tbody",
                                            AdminUser.list.map((user) => {
                                                return m("tr",
                                                    [

                                                        m("td",
                                                            user.title
                                                        ),
                                                        m("td",
                                                            [

                                                                m(m.route.Link, {
                                                                        href: `/user/${user.id}`,
                                                                        class: "btn btn-primary",
                                                                        options: {replace: true},
                                                                    },
                                                                    m("i", {"class": "octicon octicon-pencil "})
                                                                ),
                                                                m("button", {
                                                                        "class": "btn btn-danger",
                                                                        "type": "button",
                                                                        onclick: () => {

                                                                            if (confirm("Are You Sure") === true) {
                                                                                AdminUser.delete(user.id)
                                                                            }


                                                                        }
                                                                    },
                                                                    m.trust('&#128465;')
                                                                )
                                                            ]
                                                        )
                                                    ]
                                                )
                                            })
                                        )
                                    ]
                                )
                            )
                        )
                    )
                ),

            ]
        );


    },
    oncreate: function (vnode) {

        //$grid.masonry('layout');

// trigger initial layout
        //  $grid.masonry();
    },
    onupdate: function (vnode) {

    }
};


module.exports = {AdminUserList, AdminUser}