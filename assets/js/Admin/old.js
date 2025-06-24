var Admin = {
    list: [],
    packages: [],
    posts: [],
    users: [],
    newPost: {
        name: '',
        img: '',
        text: '',
    },
    newProject: {
        title: '',
        img: '',
        body: '',
    },
    newPackage: {
        name: '',
        img: '',
        text: '',
    },
    newUser: {
        name: '',
        img: '',
        text: '',
    },
    current: [],
    loadList: function () {
        return m.request({
            method: "GET",
            url: "/api/v1/all",
            withCredentials: true,
        })
            .then(function (result) {
                Admin.list = result
            })
    },
    load: function (id) {
        return m.request({
                method: "GET",
                url: "/api/v1/all/" + id,
                withCredentials: true,
            }
        ).then(function (result) {
            Admin.current = result
        })
    },
    addProject() {
        console.log(Admin.newProject);

        return m.request({
                method: "POST",
                url: "/api/v1/project",
                headers: {
                    'X-CSRF-Token': this.CSRF,
                    'Accept': `application/json`
                },
                withCredentials: true,
                body: Admin.newProject,
            }
        ).then(function (result) {
            Admin.loadList()
        })
    }
};

var AdminList = {
    oninit: Admin.loadList(),
    view: function () {

        return m("div", {"class": "row", "id": "grid"},
            [

                m("div", {"class": "col-sm-3"},
                    [
                        m("div",
                            [
                                m("h1",
                                    "Projects"
                                ),

                                m("div", {"class": "form"},
                                    m("form", {
                                            onsubmit: (e) => {
                                                event.preventDefault();

                                                Admin.addProject()
                                            }
                                        },
                                        m("div", {"class": "input-group mb-3"},
                                            [
                                                m("input", {
                                                    "class": "form-control form-control-lg",
                                                    "type": "text",
                                                    value: Admin.newProject.title,
                                                    onchange: (e) => {
                                                        Admin.newProject.title = e.target.value;
                                                    },
                                                    "placeholder": ".form-control-lg"
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
                                                    "placeholder": ".form-control-lg",
                                                    value: Admin.newProject.img,
                                                    onchange: (e) => {
                                                        Admin.newProject.img = e.target.value;
                                                    },
                                                }), m("div", {"class": "input-group-append"},
                                                m("span", {"class": "input-group-text", "id": "basic-addon2"},
                                                    "Img"
                                                )
                                            )
                                            ]
                                        ),
                                        m("div", {"class": "form-group"},
                                            [
                                                m("label", {"for": "exampleFormControlTextarea1"},
                                                    "Example textarea"
                                                ),
                                                m("textarea", {
                                                        "class": "form-control",
                                                        "id": "exampleFormControlTextarea1",
                                                        "rows": "3",
                                                        onchange: (e) => {
                                                            Admin.newProject.body = e.target.value;
                                                        },
                                                    },
                                                    Admin.newProject.body
                                                )
                                            ]
                                        ),
                                        m("button", {"class": "btn btn-primary btn-lg btn-block", "type": "submit"},
                                            "save"
                                        )
                                    )
                                ),
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
                                                        Admin.posts.map(() => {
                                                            return m("tr",
                                                                [

                                                                    m("td",
                                                                        "Bootstrap Flexbox Tutorial and Examples"
                                                                    ),
                                                                    m("td",
                                                                        [
                                                                            m("button", {
                                                                                    "class": "btn btn-primary",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "far fa-eye"})
                                                                            ),
                                                                            m("button", {
                                                                                    "class": "btn btn-success",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "fas fa-edit"})
                                                                            ),
                                                                            m("button", {
                                                                                    "class": "btn btn-danger",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "far fa-trash-alt"})
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
                            ]
                        ),
                        m("div", {"id": "projects"},
                        )
                    ]
                ),
                m("div", {"class": "col-sm-3"},
                    [
                        m("div",
                            [
                                m("h1",
                                    "Projects"
                                ),
                                m("div", {"class": "form"},
                                    m("form", {"method": "post"},
                                        m("div", {"class": "input-group mb-3"},
                                            [
                                                m("input", {
                                                    "class": "form-control form-control-lg",
                                                    "type": "text",
                                                    "placeholder": ".form-control-lg"
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
                                                    "placeholder": ".form-control-lg"
                                                }), m("div", {"class": "input-group-append"},
                                                m("span", {"class": "input-group-text", "id": "basic-addon2"},
                                                    "Img"
                                                )
                                            )
                                            ]
                                        ),
                                        m("div", {"class": "form-group"},
                                            [
                                                m("label", {"for": "exampleFormControlTextarea1"},
                                                    "Example textarea"
                                                ),
                                                m("textarea", {
                                                    "class": "form-control",
                                                    "id": "exampleFormControlTextarea1",
                                                    "rows": "3"
                                                })
                                            ]
                                        ),
                                        m("button", {"class": "btn btn-primary btn-lg btn-block", "type": "button"},
                                            "Block level button"
                                        )
                                    )
                                ),
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
                                                        Admin.posts.map(() => {
                                                            return m("tr",
                                                                [

                                                                    m("td",
                                                                        "Bootstrap Flexbox Tutorial and Examples"
                                                                    ),
                                                                    m("td",
                                                                        [
                                                                            m("button", {
                                                                                    "class": "btn btn-primary",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "far fa-eye"})
                                                                            ),
                                                                            m("button", {
                                                                                    "class": "btn btn-success",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "fas fa-edit"})
                                                                            ),
                                                                            m("button", {
                                                                                    "class": "btn btn-danger",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "far fa-trash-alt"})
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
                            ]
                        ),
                        m("div", {"id": "projects"},
                        )
                    ]
                ),

                m("div", {"class": "col-sm-3"},
                    [
                        m("div",
                            [
                                m("h1",
                                    "Packages"
                                ),
                                m("div", {"class": "form"},
                                    m("form", {"method": "post"},
                                        m("div", {"class": "input-group mb-3"},
                                            [
                                                m("input", {
                                                    "class": "form-control form-control-lg",
                                                    "type": "text",
                                                    "placeholder": ".form-control-lg"
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
                                                    "placeholder": ".form-control-lg"
                                                }), m("div", {"class": "input-group-append"},
                                                m("span", {"class": "input-group-text", "id": "basic-addon2"},
                                                    "Img"
                                                )
                                            )
                                            ]
                                        ),
                                        m("div", {"class": "form-group"},
                                            [
                                                m("label", {"for": "exampleFormControlTextarea1"},
                                                    "Example textarea"
                                                ),
                                                m("textarea", {
                                                    "class": "form-control",
                                                    "id": "exampleFormControlTextarea1",
                                                    "rows": "3"
                                                })
                                            ]
                                        ),
                                        m("button", {"class": "btn btn-primary btn-lg btn-block", "type": "button"},
                                            "Block level button"
                                        )
                                    )
                                ),
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
                                                        Admin.posts.map(() => {
                                                            return m("tr",
                                                                [

                                                                    m("td",
                                                                        "Bootstrap Flexbox Tutorial and Examples"
                                                                    ),
                                                                    m("td",
                                                                        [
                                                                            m("button", {
                                                                                    "class": "btn btn-primary",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "far fa-eye"})
                                                                            ),
                                                                            m("button", {
                                                                                    "class": "btn btn-success",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "fas fa-edit"})
                                                                            ),
                                                                            m("button", {
                                                                                    "class": "btn btn-danger",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "far fa-trash-alt"})
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
                            ]
                        ),
                        m("div", {"id": "projects"},
                        )
                    ]
                ),
                m("div", {"class": "col-sm-3"},
                    [
                        m("div",
                            [
                                m("h1",
                                    "Posts"
                                ),
                                m("div", {"class": "form"},
                                    m("form", {"method": "post"},
                                        m("div", {"class": "input-group mb-3"},
                                            [
                                                m("input", {
                                                    "class": "form-control form-control-lg",
                                                    "type": "text",
                                                    "placeholder": ".form-control-lg"
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
                                                    "placeholder": ".form-control-lg"
                                                }), m("div", {"class": "input-group-append"},
                                                m("span", {"class": "input-group-text", "id": "basic-addon2"},
                                                    "Img"
                                                )
                                            )
                                            ]
                                        ),
                                        m("div", {"class": "form-group"},
                                            [
                                                m("label", {"for": "exampleFormControlTextarea1"},
                                                    "Example textarea"
                                                ),
                                                m("textarea", {
                                                    "class": "form-control",
                                                    "id": "exampleFormControlTextarea1",
                                                    "rows": "3"
                                                })
                                            ]
                                        ),
                                        m("button", {"class": "btn btn-primary btn-lg btn-block", "type": "button"},
                                            "Block level button"
                                        )
                                    )
                                ),
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
                                                        Admin.posts.map(() => {
                                                            return m("tr",
                                                                [

                                                                    m("td",
                                                                        "Bootstrap Flexbox Tutorial and Examples"
                                                                    ),
                                                                    m("td",
                                                                        [
                                                                            m("button", {
                                                                                    "class": "btn btn-primary",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "far fa-eye"})
                                                                            ),
                                                                            m("button", {
                                                                                    "class": "btn btn-success",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "fas fa-edit"})
                                                                            ),
                                                                            m("button", {
                                                                                    "class": "btn btn-danger",
                                                                                    "type": "button"
                                                                                },
                                                                                m("i", {"class": "far fa-trash-alt"})
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
                            ]
                        ),
                    ]
                ),

            ]
        )

    },
    oncreate: function (vnode) {

        //$grid.masonry('layout');

// trigger initial layout
        //  $grid.masonry();
    },
    onupdate: function (vnode) {

    }
};


module.exports = {AdminList, Admin}