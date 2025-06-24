const AdminPostModel = {
    list: [],
    current: {
        title: '',
        img: '',
        body: '',
    },
    loading: false,
    error: null,

    loadList: function() {
        AdminPostModel.loading = true;
        return m.request({
            method: "GET",
            url: "/api/v1/post",
            withCredentials: true,
        })
            .then(result => {
                AdminPostModel.list = result;
            })
            .catch(e => {
                AdminPostModel.error = e.message;
            })
            .finally(() => {
                AdminPostModel.loading = false;
            });
    },

    load: function(id) {
        AdminPostModel.loading = true;
        return m.request({
            method: "GET",
            url: `/api/v1/post/${id}`,
            withCredentials: true,
        })
            .then(result => {
                AdminPostModel.current = result;
            })
            .catch(e => {
                AdminPostModel.error = e.message;
            })
            .finally(() => {
                AdminPostModel.loading = false;
            });
    },

    save: function() {
        return m.request({
            method: "POST",
            url: "/api/v1/post",
            headers: {
                'Accept': 'application/json'
            },
            withCredentials: true,
            body: AdminPostModel.current,
        });
    },

    delete: function(id) {
        return m.request({
            method: "DELETE",
            url: `/api/v1/post/${id}`,
            withCredentials: true,
        });
    },

    validate: function() {
        const errors = [];
        if (!AdminPostModel.current.title.trim()) {
            errors.push("Title is required");
        }
        if (!AdminPostModel.current.body.trim()) {
            errors.push("Content is required");
        }
        return errors;
    }
};

const AdminPostList = {
    oninit: AdminPostModel.loadList,
    view: function() {
        if (AdminPostModel.loading) {
            return m(".loading-spinner.text-center.p-5",
                m("i.fas.fa-circle-notch.fa-spin.fa-3x.text-pastel-blue")
            );
        }

        if (AdminPostModel.error) {
            return m(".alert.bg-pastel-red.text-white.m-4", AdminPostModel.error);
        }

        return m(".container-fluid.p-4", [
            m(".row.align-items-center.mb-4.bg-pastel-blue.p-3.rounded", [
                m(".col-6",
                    m("h2.text-white.mb-0", "Posts")
                ),
                m(".col-6.text-right",
                    m(m.route.Link, {
                        class: "btn bg-pastel-green text-white hover:bg-pastel-green-dark",
                        href: "/post/new"
                    }, [
                        m("i.fas.fa-plus.mr-2"),
                        "Create New Post"
                    ])
                )
            ]),
            m(".card.shadow-sm", [
                m(".card-body",
                    m("table.table.table-hover", [
                        m("thead.bg-pastel-purple.text-white",
                            m("tr", [
                                m("th.border-0", "Title"),
                                m("th.border-0.w-25", "Created"),
                                m("th.border-0.w-25", "Actions")
                            ])
                        ),
                        m("tbody",
                            AdminPostModel.list.map(post =>
                                m("tr.hover:bg-pastel-yellow-light", [
                                    m("td", post.title),
                                    m("td", new Date(post.created_at).toLocaleDateString()),
                                    m("td", [
                                        m(m.route.Link, {
                                            class: "btn btn-sm bg-pastel-blue text-white mr-2",
                                            href: `/post/${post.id}`
                                        }, [
                                            m("i.fas.fa-edit.mr-1"),
                                            "Edit"
                                        ]),
                                        m("button.btn.btn-sm.bg-pastel-red.text-white", {
                                            onclick: () => {
                                                if (confirm("Are you sure you want to delete this post?")) {
                                                    AdminPostModel.delete(post.id)
                                                        .then(AdminPostModel.loadList);
                                                }
                                            }
                                        }, [
                                            m("i.fas.fa-trash.mr-1"),
                                            "Delete"
                                        ])
                                    ])
                                ])
                            )
                        )
                    ])
                )
            ])
        ]);
    }
};

// components/AdminPostForm.js
// AdminPostForm component
const AdminPostForm = {
    editor: null,
    validationErrors: [],
    oninit: function(vnode) {
        if (vnode.attrs.id) {
            AdminPostModel.load(vnode.attrs.id);
        } else {
            AdminPostModel.current = {
                title: '',
                img: '',
                body: ''
            };
        }
    },
    oncreate: function(vnode) {
        this.editor = new SimpleMDE({
            element: document.getElementById("postEditor"),
            spellChecker: false,
            autosave: {
                enabled: true,
                unique_id: "postEditor_" + (vnode.attrs.id || 'new'),
            },
            toolbar: [
                "bold", "italic", "heading", "|",
                "quote", "unordered-list", "ordered-list", "|",
                "link", "image", "|",
                "preview", "side-by-side", "fullscreen", "|",
                "guide"
            ],
            status: ["autosave", "lines", "words", "cursor"]
        });

        this.editor.value(AdminPostModel.current.body);

        // Setup image upload handling
        this.editor.codemirror.on("drop", function(cm, event) {
            event.preventDefault();
            const files = event.dataTransfer.files;
            if (files && files.length > 0) {
                // Handle file upload logic here
            }
        });
    },
    onremove: function() {
        if (this.editor) {
            this.editor.toTextArea();
            this.editor = null;
        }
    },
    view: function(vnode) {
        if (AdminPostModel.loading && vnode.attrs.id) {
            return m(".loading-spinner.text-center.p-5",
                m("i.fas.fa-circle-notch.fa-spin.fa-3x.text-pastel-blue")
            );
        }

        return m(".container-fluid.p-4", [
            m(".card.shadow-sm", [
                m(".card-header.bg-pastel-blue.text-white",
                    m("h2.mb-0", vnode.attrs.id ? "Edit Post" : "Create New Post")
                ),
                m(".card-body", [
                    this.validationErrors.length > 0 && m(".alert.bg-pastel-red.text-white.mb-4", [
                        m("strong", "Please correct the following errors:"),
                        m("ul.mb-0.mt-2", this.validationErrors.map(error =>
                            m("li", error)
                        ))
                    ]),
                    m("form.post-form", {
                        onsubmit: (e) => {
                            e.preventDefault();
                            AdminPostModel.current.body = this.editor.value();
                            const errors = AdminPostModel.validate();
                            if (errors.length) {
                                this.validationErrors = errors;
                                return;
                            }
                            this.validationErrors = [];
                            AdminPostModel.save()
                                .then(() => m.route.set("/post"))
                                .catch(e => {
                                    this.validationErrors = [e.message];
                                });
                        }
                    }, [
                        m(".form-group", [
                            m("label.text-pastel-blue", "Title"),
                            m("input.form-control.bg-pastel-yellow-light[type=text]", {
                                value: AdminPostModel.current.title,
                                onchange: (e) => AdminPostModel.current.title = e.target.value
                            })
                        ]),
                        m(".form-group", [
                            m("label.text-pastel-blue", "Image URL"),
                            m("input.form-control.bg-pastel-yellow-light[type=text]", {
                                value: AdminPostModel.current.img,
                                onchange: (e) => AdminPostModel.current.img = e.target.value
                            }),
                            AdminPostModel.current.img && m(".mt-2.border.rounded.p-2",
                                m("img.img-fluid", {
                                    src: AdminPostModel.current.img,
                                    alt: "Preview"
                                })
                            )
                        ]),
                        m(".form-group", [
                            m("label.text-pastel-blue", "Content"),
                            m("textarea#postEditor")
                        ]),
                        m(".form-group.text-right", [
                            m("button.btn.bg-pastel-green.text-white.mr-2[type=submit]", [
                                m("i.fas.fa-save.mr-2"),
                                vnode.attrs.id ? "Update Post" : "Create Post"
                            ]),
                            m(m.route.Link, {
                                class: "btn bg-pastel-red text-white",
                                href: "/post"
                            }, [
                                m("i.fas.fa-times.mr-2"),
                                "Cancel"
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    }
};


module.exports = { AdminPostList, AdminPostForm };