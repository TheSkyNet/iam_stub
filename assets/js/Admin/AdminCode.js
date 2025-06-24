var AdminCode = {
    list: [],
    code: {
        title: '',
        img: '',
        body: '',
    },

    current: [],
    loadList: function () {
        return m.request({
            method: "GET",
            url: "/api/v1/code",
            withCredentials: true,
        })
            .then(function (result) {
                AdminCode.list = result
            })
    },
    load: function (id) {
        return m.request({
                method: "GET",
                url: "/api/v1/code/" + id,
                withCredentials: true,
            }
        ).then(function (result) {
            AdminCode.code = result
        })
    },
    delete(id) {
        return m.request({
                method: "DELETE",
                url: "/api/v1/code/" + id,
                withCredentials: true,
            }
        ).then(function (result) {
            AdminCode.loadList()
        })
    },
    addCode() {
        return m.request({
                method: "POST",
                url: "/api/v1/code",
                headers: {
                    'X-CSRF-Token': this.CSRF,
                    'Accept': `application/json`
                },
                withCredentials: true,
                body: AdminCode.code,
            }
        ).then(function (result) {
            AdminCode.loadList()
            //m.route.set("/codes")
        })
    },
    updateCode() {
        return m.request({
                method: "POST",
                url: "/api/v1/code",
                headers: {
                    'X-CSRF-Token': this.CSRF,
                    'Accept': `application/json`
                },
                withCredentials: true,
                body: AdminCode.code,
            }
        ).then(function (result) {
            AdminCode.loadList()
            // m.route.set("/codes")
        })
    }
};

var AdminCodeList = {
    oninit: (vnode) => {
        if (vnode.attrs.id) {
            AdminCode.load(vnode.attrs.id)
        }
        AdminCode.loadList()

    },
    view: function (vnode) {
        return m("div", {"class": "row"},
            [
                m('div', {class: 'col-12'}, m("h1",
                    "Codes"
                )),
                m("div", {class: 'col-6'},
                    m("div", {"class": "form"},
                        m("form", {
                                onsubmit: (e) => {
                                    event.preventDefault();
                                    if (vnode.attrs.id) {
                                        AdminCode.updateCode()
                                    } else {
                                        AdminCode.addCode()
                                    }

                                }
                            },
                            m("div", {"class": "input-group mb-3"},
                                [
                                    m("input", {
                                        "class": "form-control form-control-lg",
                                        "type": "text",
                                        value: AdminCode.code.title,
                                        onchange: (e) => {
                                            AdminCode.code.title = e.target.value;
                                        },
                                        "placeholder": "title"
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
                                        "placeholder": "img",
                                        value: AdminCode.code.img,
                                        onchange: (e) => {
                                            AdminCode.code.img = e.target.value;
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
                                                AdminCode.code.body = e.target.value;
                                            },
                                        },
                                        AdminCode.code.body
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
                                            AdminCode.list.map((code) => {
                                                return m("tr",
                                                    [

                                                        m("td",
                                                            code.title
                                                        ),
                                                        m("td",
                                                            [
                                                                m(m.route.Link, {
                                                                        href: `/code/${code.id}`,
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
                                                                                AdminCode.delete(code.id)
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
    }
};


module.exports = {AdminCodeList, AdminCode}