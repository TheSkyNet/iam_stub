const EasyMDE = require("easymde");
const {MessageDisplay} = require("../components/MessageDisplay");

const AdminProjectList = {
    loading: true,
    oninit: (vnode) => {
        if (vnode.attrs.id) {
            AdminProject.load(vnode.attrs.id).then(() => {
                AdminProjectList.loading = false
            });
        }
        AdminProject.loadList().then(() => {
            AdminProjectList.loading = false
        })
    },
    onremove: () => {
        if (AdminProjectList.easyMDE) {
            AdminProjectList.easyMDE.toTextArea();
            AdminProjectList.easyMDE = null;
        }
    },
    view: function(vnode) {
        if (AdminProjectList.loading) {
            return m("div.loading", [
                m("div.spinner-border.text-primary", {
                    role: "status"
                }),
                m("span.sr-only", "Loading...")
            ]);
        }
        return [
            m(MessageDisplay),  // Add MessageDisplay at the top
            m("div.project-page", {
                style: {
                    background: '#fff',
                    borderRadius: '4px',
                    padding: '0.25rem'
                }
            }, [
                // Header
                m("div.header", {
                    style: {
                        display: 'flex',
                        alignItems: 'center'
                    }
                }, [
                    m("h1", {
                        style: {
                            color: '#8198c4',
                            fontSize: '2rem'
                        }
                    }, [
                        m("i.octicon.octicon-project"),
                        "Projects"
                    ])
                ]),

                // Content
                m("div.row", {
                    style: { width: '100%' }
                }, [
                    // Form
                    m("div.col-6",
                        m("form", {
                            onsubmit: (e) => {
                                e.preventDefault();
                                vnode.attrs.id ?
                                    AdminProject.updateProject(vnode.attrs.id) :
                                    AdminProject.addProject();
                            },
                            style: {
                                background: '#f8fafc',
                                borderRadius: '4px',
                                border: '1px solid rgba(129, 152, 196, 0.2)',
                                padding: '0.25rem'
                            }
                        }, [
                            m("input.form-control", {
                                style: {
                                    border: '1px solid #8198c4',
                                    borderRadius: '4px'
                                },
                                value: AdminProject.project.title,
                                onchange: (e) => AdminProject.project.title = e.target.value,
                                placeholder: "Title"
                            }),

                            m("input.form-control", {
                                style: {
                                    border: '1px solid #8198c4',
                                    borderRadius: '4px',
                                    marginTop: '0.25rem'
                                },
                                value: AdminProject.project.img,
                                onchange: (e) => AdminProject.project.img = e.target.value,
                                placeholder: "Image URL"
                            }),

                            m("textarea", {
                                style: { marginTop: '0.25rem' },
                                oncreate: (vnode) => {
                                    if (AdminProjectList.easyMDE) {
                                        AdminProjectList.easyMDE.toTextArea();
                                        AdminProjkectList.easyMDE = null;
                                    }
                                    AdminProjectList.easyMDE = new EasyMDE({
                                        element: vnode.dom,
                                        spellChecker: false
                                    });
                                    AdminProjectList.easyMDE.value(AdminProject.project.body);
                                    AdminProjectList.easyMDE.codemirror.on("change", () => {
                                        AdminProject.project.body = AdminProjectList.easyMDE.value();
                                    });
                                },
                                onbeforeupdate: () => {
                                    if (AdminProjectList.easyMDE) {
                                        AdminProjectList.easyMDE.toTextArea();
                                        AdminProjectList.easyMDE = null;
                                        return true;
                                    }
                                },
                                onupdate: (vnode) => {
                                    if (!AdminProjectList.easyMDE) {
                                        AdminProjectList.easyMDE = new EasyMDE({
                                            element: vnode.dom,
                                            spellChecker: false
                                        });
                                        AdminProjectList.easyMDE.value(AdminProject.project.body);
                                        AdminProjectList.easyMDE.codemirror.on("change", () => {
                                            AdminProject.project.body = AdminProjectList.easyMDE.value();
                                        });
                                    }
                                }
                            }),
                            m("button.btn", {
                                type: "submit",
                                style: {
                                    background: '#8198c4',
                                    color: '#fff',
                                    border: 'none',
                                    borderRadius: '4px',
                                    width: '100%',
                                    marginTop: '0.25rem'
                                }
                            }, [
                                m("i.octicon.octicon-check"),
                                vnode.attrs.id ? " Update" : " Create"
                            ])
                        ])
                    ),

                    // Projects List
                    m("div.col-6", {
                            style: { padding: '0.25rem' }
                        },
                        m("div.table-responsive",
                            m("table.table", {
                                style: {
                                    background: '#fff',
                                    borderRadius: '4px',
                                    border: '1px solid rgba(129, 152, 196, 0.2)'
                                }
                            }, [
                                m("thead",
                                    m("tr", [
                                        m("th", "Name"),
                                        m("th", "Actions")
                                    ])
                                ),
                                m("tbody",
                                    AdminProject.list.map(project =>
                                        m("tr", [
                                            m("td", project.title),
                                            m("td", [
                                                m(m.route.Link, {
                                                    href: `/project/${project.id}`,
                                                    class: "btn",
                                                    style: {
                                                        background: '#8198c4',
                                                        color: '#fff',
                                                        borderRadius: '4px',
                                                        marginRight: '0.25rem'
                                                    }
                                                }, m("i.octicon.octicon-pencil")),
                                                m("button.btn", {
                                                    onclick: () => {
                                                        if (confirm("Delete?")) AdminProject.delete(project.id);
                                                    },
                                                    style: {
                                                        background: '#ff6b6b',
                                                        color: '#fff',
                                                        borderRadius: '4px'
                                                    }
                                                }, m("i.octicon.octicon-trashcan"))
                                            ])
                                        ])
                                    )
                                )
                            ])
                        )
                    )
                ])
            ])
        ];
    }


};
const AdminProject = {
    list: [],
    project: {
        title: '',
        img: '',
        body: '',
    },

    current: [],
    loadList: function () {
        return m.request({
            method: "GET",
            url: "/api/v1/project",
            withCredentials: true,
        })
            .then(function (result) {
                AdminProject.list = result
            })
    },
    delete(id) {
        return m.request({
                method: "DELETE",
                url: "/api/v1/project/" + id,
                withCredentials: true,
            }
        ).then(function () {
            AdminProject.loadList()
        })
    },

    load: function (id) {
        return m.request({
                method: "GET",
                url: "/api/v1/project/" + id,
                withCredentials: true,
            }
        ).then(function (result) {
            AdminProject.project = result
        })
    },
    addProject() {
        if (!this.validateProject()) {
            return;
        }

        return m.request({
            method: "POST",
            url: "/api/v1/project",
            headers: {
                'X-CSRF-Token': this.CSRF,
                'Accept': `application/json`
            },
            withCredentials: true,
            body: AdminProject.project,
        })
            .then(function () {
                MessageDisplay.setMessage("Project created successfully!", "success");
                AdminProject.loadList();
                AdminProject.clearForm();
            })
            .catch(function (error) {
                MessageDisplay.setMessage(error.message || "Failed to create project", "error");
            });
    },

    updateProject(id) {
        if (!this.validateProject()) {
            return;
        }

        return m.request({
            method: "PUT",
            url: "/api/v1/project/" + id,
            headers: {
                'X-CSRF-Token': this.CSRF,
                'Accept': `application/json`
            },
            withCredentials: true,
            body: AdminProject.project,
        })
            .then(function () {
                MessageDisplay.setMessage("Project updated successfully!", "success");
                AdminProject.loadList();
            })
            .catch(function (error) {
                MessageDisplay.setMessage(error.message || "Failed to update project", "error");
            });
    },

    validateProject() {
        if (!AdminProject.project.title.trim()) {
            MessageDisplay.setMessage("Title is required", "error");
            return false;
        }
        if (!AdminProject.project.body.trim()) {
            MessageDisplay.setMessage("Content is required", "error");
            return false;
        }
        return true;
    },

    clearForm() {
        AdminProject.project = {
            title: '',
            img: '',
            body: ''
        };
        if (AdminProjectList.easyMDE) {
            AdminProjectList.easyMDE.value('');
        }
    }

};

module.exports = {AdminProjectList, AdminProject}