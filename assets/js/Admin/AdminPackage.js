var AdminPackage = {
    list: [],
    package: {
        name: '',
        link: '',
        body: '',
    },

    current: [],
    loadList: function () {
        return m.request({
            method: "GET",
            url: "/api/v1/package",
            withCredentials: true,
        })
            .then(function (result) {
                AdminPackage.list = result
            })
    },
    load: function (id) {
        return m.request({
                method: "GET",
                url: "/api/v1/package/" + id,
                withCredentials: true,
            }
        ).then(function (result) {
            AdminPackage.package = result
        })
    },
    addPackage() {
        return m.request({
                method: "POST",
                url: "/api/v1/package",
                headers: {
                    'X-CSRF-Token': this.CSRF,
                    'Accept': `application/json`
                },
                withCredentials: true,
                body: AdminPackage.package,
            }
        ).then(function (result) {
            AdminPackage.loadList()
        })
    },
    updatePackage() {
        return m.request({
                method: "PUT",
                url: "/api/v1/package",
                headers: {
                    'X-CSRF-Token': this.CSRF,
                    'Accept': `application/json`
                },
                withCredentials: true,
                body: AdminPackage.package,
            }
        ).then(function (result) {
            AdminPackage.loadList()
        })
    }
};

var AdminPackageList = {
    oninit: (vnode) => {
        if (vnode.attrs.id) {
            AdminPackage.load(vnode.attrs.id)
        }
        AdminPackage.loadList()

    },
    view: function (vnode) {
        return m("div", {"class": "row"},
            [
                m('div', {class: 'col-12'}, m("h1",
                    "Packages"
                )),
                m("div", {class: 'col-6'},
                    m("div", {"class": "form"},
                        m("form", {
                                onsubmit: (e) => {
                                    event.preventDefault();
                                    if (vnode.attrs.id) {
                                        AdminPackage.updatePackage()
                                    } else {
                                        AdminPackage.addPackage()
                                    }

                                }
                            },
                            m("div", {"class": "input-group mb-3"},
                                [
                                    m("input", {
                                        "class": "form-control form-control-lg",
                                        "type": "text",
                                        value: AdminPackage.package.name,
                                        onchange: (e) => {
                                            AdminPackage.package.name = e.target.value;
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
                                        "placeholder": "link",
                                        value: AdminPackage.package.link,
                                        onchange: (e) => {
                                            AdminPackage.package.link = e.target.value;
                                        },
                                    }), m("div", {"class": "input-group-append"},
                                    m("span", {"class": "input-group-text", "id": "basic-addon2"},
                                        "link"
                                    )
                                )
                                ]
                            ),
                            m("div", {"class": "input-group mb-3"},
                            m("select", {
                                    "class": "form-select", "aria-label": "Default select example",
                                    value: AdminPackage.package.type,
                                    onchange: (e) => {
                                        AdminPackage.package.type = e.target.value;
                                    },
                                },
                                [

                                    m("option", {"value": "1"},
                                        "NPM"
                                    ),
                                    m("option", {"value": "2"},
                                        "COMPOSER"
                                    ),
                                ]
                            ),
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
                                                        "Package Name"
                                                    ),
                                                    m("th", {"scope": "col"},
                                                        "Actions"
                                                    )
                                                ]
                                            )
                                        ),
                                        m("tbody",
                                            AdminPackage.list.map((pac) => {
                                                console.log(pac, 'E')
                                                return m("tr",
                                                    [

                                                        m("td",
                                                            pac.name
                                                        ),
                                                        m("td",
                                                            [
                                                                m(m.route.Link, {
                                                                        href: `/package/${pac.id}`,
                                                                        class: "btn btn-primary",
                                                                        options: {replace: true},
                                                                    },
                                                                    m("i", {"class": "octicon octicon-eye"})
                                                                ),

                                                                m("button", {
                                                                        "class": "btn btn-success",
                                                                        "type": "button"
                                                                    },
                                                                    m("i", {"class": "octicon octicon-pencil "})
                                                                ),
                                                                m("button", {
                                                                        "class": "btn btn-danger",
                                                                        "type": "button"
                                                                    },
                                                                    m("i", {"class": "octicon octicon-trash"})
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


module.exports = {AdminPackageList, AdminPackage}