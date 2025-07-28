import {UsersService} from "../services/UsersService";
import {RolesService} from "../services/RolesService";

const AddUser = {
    data: {
        form: {
            name: '',
            email: '',
            password: '',
            roles: [],
            email_verified: false
        },
        availableRoles: [],
        loading: false,
        error: null,
        success: false
    },

    oninit: function(vnode) {
        this.loadRoles();
    },

    loadRoles: function() {
        RolesService.getAll()
            .then(response => {
                this.data.availableRoles = response.data || [];
                m.redraw();
            })
            .catch(error => {
                console.error('Failed to load roles:', error);
            });
    },

    handleSubmit: function(e) {
        e.preventDefault();
        
        if (!this.validateForm()) {
            return;
        }

        this.data.loading = true;
        this.data.error = null;

        UsersService.create(this.data.form)
            .then(response => {
                this.data.loading = false;
                this.data.success = true;
                setTimeout(() => {
                    m.route.set('/admin/users');
                }, 1500);
                m.redraw();
            })
            .catch(error => {
                this.data.loading = false;
                this.data.error = error.message || 'Failed to create user';
                m.redraw();
            });
    },

    validateForm: function() {
        if (!this.data.form.name.trim()) {
            this.data.error = 'Name is required';
            return false;
        }
        if (!this.data.form.email.trim()) {
            this.data.error = 'Email is required';
            return false;
        }
        if (!this.data.form.password.trim()) {
            this.data.error = 'Password is required';
            return false;
        }
        if (this.data.form.password.length < 6) {
            this.data.error = 'Password must be at least 6 characters';
            return false;
        }
        return true;
    },

    toggleRole: function(roleName) {
        const index = this.data.form.roles.indexOf(roleName);
        if (index > -1) {
            this.data.form.roles.splice(index, 1);
        } else {
            this.data.form.roles.push(roleName);
        }
    },

    view: function(vnode) {
        return m(".container.mx-auto.p-6", [
            // Breadcrumb
            m(".breadcrumbs.text-sm.mb-6", [
                m("ul", [
                    m("li", [m("a", {onclick: () => m.route.set('/admin/users')}, "Users")]),
                    m("li", "Add User")
                ])
            ]),

            m("h1.text-3xl.font-bold.text-base-content.mb-6", "Add New User"),

            // Success message
            this.data.success ? m(".alert.alert-success.mb-4", [
                m("span", "User created successfully! Redirecting...")
            ]) : null,

            // Error message
            this.data.error ? m(".alert.alert-error.mb-4", [
                m("span", this.data.error)
            ]) : null,

            // Form
            m(".card.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("form", {onsubmit: this.handleSubmit.bind(this)}, [
                        // Name field
                        m(".form-control.mb-4", [
                            m("label.label", [m("span.label-text", "Name *")]),
                            m("input.input.input-bordered", {
                                type: "text",
                                value: this.data.form.name,
                                oninput: (e) => this.data.form.name = e.target.value,
                                required: true
                            })
                        ]),

                        // Email field
                        m(".form-control.mb-4", [
                            m("label.label", [m("span.label-text", "Email *")]),
                            m("input.input.input-bordered", {
                                type: "email",
                                value: this.data.form.email,
                                oninput: (e) => this.data.form.email = e.target.value,
                                required: true
                            })
                        ]),

                        // Password field
                        m(".form-control.mb-4", [
                            m("label.label", [m("span.label-text", "Password *")]),
                            m("input.input.input-bordered", {
                                type: "password",
                                value: this.data.form.password,
                                oninput: (e) => this.data.form.password = e.target.value,
                                required: true,
                                minlength: 6
                            })
                        ]),

                        // Roles selection
                        m(".form-control.mb-4", [
                            m("label.label", [m("span.label-text", "Roles")]),
                            m(".grid.grid-cols-2.gap-2", 
                                this.data.availableRoles.map(role => 
                                    m("label.cursor-pointer.label", [
                                        m("input.checkbox", {
                                            type: "checkbox",
                                            checked: this.data.form.roles.includes(role.name),
                                            onchange: () => this.toggleRole(role.name)
                                        }),
                                        m("span.label-text.ml-2", role.name)
                                    ])
                                )
                            )
                        ]),

                        // Email verified toggle
                        m(".form-control.mb-6", [
                            m("label.cursor-pointer.label", [
                                m("span.label-text", "Email Verified"),
                                m("input.toggle", {
                                    type: "checkbox",
                                    checked: this.data.form.email_verified,
                                    onchange: (e) => this.data.form.email_verified = e.target.checked
                                })
                            ])
                        ]),

                        // Submit buttons
                        m(".card-actions.justify-end", [
                            m("button.btn.btn-ghost", {
                                type: "button",
                                onclick: () => m.route.set('/admin/users')
                            }, "Cancel"),
                            m("button.btn.btn-primary", {
                                type: "submit",
                                disabled: this.data.loading
                            }, this.data.loading ? "Creating..." : "Create User")
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export {AddUser};