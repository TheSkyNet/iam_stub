import {UsersService} from "../services/UsersService";
import {RolesService} from "../services/RolesService";
import { Icon } from "./Icon";

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
        return m(".container.mx-auto.p-6.max-w-4xl", [
            // Breadcrumb with enhanced styling
            m(".breadcrumbs.text-sm.mb-8", [
                m("ul.bg-base-200.rounded-lg.px-4.py-2", [
                    m("li", [
                        m("a.flex.items-center.gap-2.hover:text-primary.transition-colors", {
                            onclick: () => m.route.set('/admin/users')
                        }, [
                            m(Icon, { name: 'fa-solid fa-users', class: 'w-4 h-4' }),
                            "Users"
                        ])
                    ]),
                    m("li.text-base-content.font-medium", [
                        m("span.flex.items-center.gap-2", [
                            m(Icon, { name: 'fa-solid fa-user-plus', class: 'w-4 h-4' }),
                            "Add User"
                        ])
                    ])
                ])
            ]),

            // Header section with icon
            m(".flex.items-center.gap-4.mb-8", [
                m(".avatar.placeholder", [
                    m(".bg-primary.text-primary-content.rounded-full.w-16.h-16", [
                        m(Icon, { name: 'fa-solid fa-user-plus', class: 'w-8 h-8' })
                    ])
                ]),
                m("div", [
                    m("h1.text-4xl.font-bold.text-base-content", "Add New User"),
                    m("p.text-base-content.opacity-70.text-lg", "Create a new user account with roles and permissions")
                ])
            ]),

            // Success message with enhanced styling
            this.data.success ? m(".alert.alert-success.mb-6.shadow-lg", [
                m(Icon, { name: 'fa-solid fa-circle-check', class: 'w-6 h-6' }),
                m("span.font-medium", "User created successfully! Redirecting...")
            ]) : null,

            // Error message with enhanced styling
            this.data.error ? m(".alert.alert-error.mb-6.shadow-lg", [
                m(Icon, { name: 'fa-solid fa-circle-exclamation', class: 'w-6 h-6' }),
                m("span.font-medium", this.data.error)
            ]) : null,

            // Enhanced form with sections
            m(".card.bg-base-100.shadow-2xl.border.border-base-300", [
                m(".card-body.p-8", [
                    m("form", {onsubmit: this.handleSubmit.bind(this)}, [
                        // Basic Information Section
                        m(".mb-8", [
                            m(".flex.items-center.gap-3.mb-6", [
                                m("div.bg-primary.text-primary-content.rounded-full.p-2", [
                                    m(Icon, { name: 'fa-solid fa-user', class: 'w-5 h-5' })
                                ]),
                                m("h2.text-2xl.font-semibold.text-base-content", "Basic Information"),
                                m(".flex-1.h-px.bg-base-300")
                            ]),

                            m(".grid.grid-cols-1.md:grid-cols-2.gap-6", [
                                // Name field with icon
                                m(".form-control", [
                                    m("label.label", [
                                        m("span.label-text.font-medium.flex.items-center.gap-2", [
                                            m(Icon, { name: 'fa-solid fa-user', class: 'w-4 h-4 text-primary' }),
                                            "Full Name *"
                                        ])
                                    ]),
                                    m("input.input.input-bordered.input-lg.focus:input-primary", {
                                        type: "text",
                                        value: this.data.form.name,
                                        oninput: (e) => this.data.form.name = e.target.value,
                                        placeholder: "Enter user's full name",
                                        required: true
                                    })
                                ]),

                                // Email field with icon
                                m(".form-control", [
                                    m("label.label", [
                                        m("span.label-text.font-medium.flex.items-center.gap-2", [
                                            m(Icon, { name: 'fa-solid fa-envelope', class: 'w-4 h-4 text-primary' }),
                                            "Email Address *"
                                        ])
                                    ]),
                                    m("input.input.input-bordered.input-lg.focus:input-primary", {
                                        type: "email",
                                        value: this.data.form.email,
                                        oninput: (e) => this.data.form.email = e.target.value,
                                        placeholder: "user@example.com",
                                        required: true
                                    })
                                ])
                            ]),

                            // Password field with icon (full width)
                            m(".form-control.mt-6", [
                                m("label.label", [
                                    m("span.label-text.font-medium.flex.items-center.gap-2", [
                                        m(Icon, { name: 'fa-solid fa-lock', class: 'w-4 h-4 text-primary' }),
                                        "Password *"
                                    ])
                                ]),
                                m("input.input.input-bordered.input-lg.focus:input-primary", {
                                    type: "password",
                                    value: this.data.form.password,
                                    oninput: (e) => this.data.form.password = e.target.value,
                                    placeholder: "Enter a secure password (min. 6 characters)",
                                    required: true,
                                    minlength: 6
                                }),
                                m("label.label", [
                                    m("span.label-text-alt.text-base-content.opacity-60", "Password must be at least 6 characters long")
                                ])
                            ])
                        ]),

                        // Roles & Permissions Section
                        m(".mb-8", [
                            m(".flex.items-center.gap-3.mb-6", [
                                m("div.bg-secondary.text-secondary-content.rounded-full.p-2", [
                                    m(Icon, { name: 'fa-solid fa-user-shield', class: 'w-5 h-5' })
                                ]),
                                m("h2.text-2xl.font-semibold.text-base-content", "Roles & Permissions"),
                                m(".flex-1.h-px.bg-base-300")
                            ]),

                            // Enhanced roles selection
                            m(".form-control", [
                                m("label.label", [
                                    m("span.label-text.font-medium.flex.items-center.gap-2", [
                                        m(Icon, { name: 'fa-solid fa-users', class: 'w-4 h-4 text-secondary' }),
                                        "User Roles"
                                    ])
                                ]),
                                m(".grid.grid-cols-1.sm:grid-cols-2.gap-4.mt-3", 
                                    this.data.availableRoles.map(role => 
                                        m(".card.bg-base-200.hover:bg-base-300.transition-colors.cursor-pointer", {
                                            onclick: () => this.toggleRole(role.name)
                                        }, [
                                            m(".card-body.p-4", [
                                                m("label.cursor-pointer.flex.items-center.gap-3", [
                                                    m("input.checkbox.checkbox-primary.checkbox-lg", {
                                                        type: "checkbox",
                                                        checked: this.data.form.roles.includes(role.name),
                                                        onchange: () => this.toggleRole(role.name)
                                                    }),
                                                    m("div.flex-1", [
                                                        m("div.font-semibold.text-base-content.capitalize", role.name),
                                                        m("div.text-sm.text-base-content.opacity-70", role.description || `${role.name} role permissions`)
                                                    ])
                                                ])
                                            ])
                                        ])
                                    )
                                )
                            ])
                        ]),

                        // Account Settings Section
                        m(".mb-8", [
                            m(".flex.items-center.gap-3.mb-6", [
                                m("div.bg-accent.text-accent-content.rounded-full.p-2", [
                                    m(Icon, { name: 'fa-solid fa-gears', class: 'w-5 h-5' })
                                ]),
                                m("h2.text-2xl.font-semibold.text-base-content", "Account Settings"),
                                m(".flex-1.h-px.bg-base-300")
                            ]),

                            // Email verified toggle with enhanced styling
                            m(".form-control", [
                                m("label.label.cursor-pointer.bg-base-200.rounded-lg.p-4.hover:bg-base-300.transition-colors", [
                                    m("span.label-text.font-medium.flex.items-center.gap-2", [
                                        m(Icon, { name: 'fa-solid fa-circle-check', class: 'w-5 h-5 text-accent' }),
                                        m("div", [
                                            m("div", "Email Verified"),
                                            m("div.text-sm.opacity-70", "Mark this account as email verified")
                                        ])
                                    ]),
                                    m("input.toggle.toggle-accent.toggle-lg", {
                                        type: "checkbox",
                                        checked: this.data.form.email_verified,
                                        onchange: (e) => this.data.form.email_verified = e.target.checked
                                    })
                                ])
                            ])
                        ]),

                        // Enhanced submit buttons
                        m(".card-actions.justify-end.gap-4.pt-6.border-t.border-base-300", [
                            m("button.btn.btn-ghost.btn-lg.gap-2", {
                                type: "button",
                                onclick: () => m.route.set('/admin/users')
                            }, [
                                m(Icon, { name: 'fa-solid fa-xmark', class: 'w-5 h-5' }),
                                "Cancel"
                            ]),
                            m("button.btn.btn-primary.btn-lg.gap-2", {
                                type: "submit",
                                disabled: this.data.loading
                            }, [
                                this.data.loading ? 
                                    m("span.loading.loading-spinner.loading-sm") :
                                    m(Icon, { name: 'fa-solid fa-user-plus', class: 'w-5 h-5' }),
                                this.data.loading ? "Creating User..." : "Create User"
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export {AddUser};