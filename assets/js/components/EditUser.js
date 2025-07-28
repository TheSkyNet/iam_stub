import {UsersService} from "../services/UsersService";
import {RolesService} from "../services/RolesService";
const {AuthService} = require("../services/AuthserviceService");

const EditUser = {
    data: {
        userId: null,
        form: {
            name: '',
            email: '',
            password: '',
            roles: [],
            email_verified: false
        },
        availableRoles: [],
        loading: false,
        loadingUser: false,
        error: null,
        success: false
    },

    oninit: function(vnode) {
        this.data.userId = vnode.attrs.id || m.route.param('id');
        if (!this.data.userId) {
            this.data.error = 'User ID is required';
            return;
        }
        
        this.loadRoles();
        this.loadUser();
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

    loadUser: function() {
        this.data.loadingUser = true;
        this.data.error = null;

        UsersService.getById(this.data.userId)
            .then(response => {
                if (response.success && response.data) {
                    const user = response.data;
                    this.data.form.name = user.name || '';
                    this.data.form.email = user.email || '';
                    this.data.form.password = ''; // Always empty for security
                    this.data.form.roles = user.roles || [];
                    this.data.form.email_verified = user.email_verified || false;
                }
                this.data.loadingUser = false;
                m.redraw();
            })
            .catch(error => {
                this.data.loadingUser = false;
                this.data.error = error.message || 'Failed to load user';
                m.redraw();
            });
    },

    handleSubmit: function(e) {
        e.preventDefault();
        
        if (!this.validateForm()) {
            return;
        }

        this.data.loading = true;
        this.data.error = null;

        // Prepare update data - only include password if it's not empty
        const updateData = {
            id: this.data.userId, // Include ID in request body
            name: this.data.form.name,
            email: this.data.form.email,
            roles: this.data.form.roles,
            email_verified: this.data.form.email_verified
        };

        // Only include password if it's provided
        if (this.data.form.password.trim()) {
            updateData.password = this.data.form.password;
        }

        UsersService.update(this.data.userId, updateData)
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
                this.data.error = error.message || 'Failed to update user';
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
        // Password is optional for updates, but if provided, must be at least 6 characters
        if (this.data.form.password.trim() && this.data.form.password.length < 6) {
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

    handleResendPasswordReset: function() {
        if (!this.data.form.email) {
            this.data.error = 'Email is required to send password reset';
            m.redraw();
            return;
        }

        AuthService.resendPasswordReset(this.data.form.email)
            .then(response => {
                if (response.success) {
                    this.data.error = null;
                    // Show success message temporarily
                    const originalError = this.data.error;
                    this.data.error = null;
                    this.data.success = false;
                    
                    // Create a temporary success state
                    const tempSuccess = true;
                    setTimeout(() => {
                        if (tempSuccess) {
                            alert('Password reset email sent successfully!');
                        }
                    }, 100);
                } else {
                    this.data.error = response.message || 'Failed to send password reset email';
                }
                m.redraw();
            })
            .catch(error => {
                this.data.error = error.message || 'Failed to send password reset email';
                m.redraw();
            });
    },

    handleResendEmailVerification: function() {
        if (!this.data.form.email) {
            this.data.error = 'Email is required to send verification email';
            m.redraw();
            return;
        }

        AuthService.resendEmailVerification(this.data.form.email)
            .then(response => {
                if (response.success) {
                    this.data.error = null;
                    // Show success message temporarily
                    setTimeout(() => {
                        alert('Email verification sent successfully!');
                    }, 100);
                } else {
                    this.data.error = response.message || 'Failed to send verification email';
                }
                m.redraw();
            })
            .catch(error => {
                this.data.error = error.message || 'Failed to send verification email';
                m.redraw();
            });
    },

    view: function(vnode) {
        // Show loading state while loading user data
        if (this.data.loadingUser) {
            return m(".container.mx-auto.p-6.max-w-4xl", [
                m(".flex.justify-center.items-center.py-12", [
                    m(".loading.loading-spinner.loading-lg")
                ])
            ]);
        }

        return m(".container.mx-auto.p-6.max-w-4xl", [
            // Enhanced breadcrumb
            m(".breadcrumbs.text-sm.mb-8", [
                m("ul.bg-base-200.rounded-lg.px-4.py-2", [
                    m("li", [
                        m("a.flex.items-center.gap-2.hover:text-primary.transition-colors", {
                            onclick: () => m.route.set('/admin/users')
                        }, [
                            m("svg.w-4.h-4", {
                                fill: "none",
                                stroke: "currentColor",
                                viewBox: "0 0 24 24"
                            }, [
                                m("path", {
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    "stroke-width": "2",
                                    d: "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                                })
                            ]),
                            "Users"
                        ])
                    ]),
                    m("li.text-base-content.font-medium", [
                        m("span.flex.items-center.gap-2", [
                            m("svg.w-4.h-4", {
                                fill: "none",
                                stroke: "currentColor",
                                viewBox: "0 0 24 24"
                            }, [
                                m("path", {
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    "stroke-width": "2",
                                    d: "M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                })
                            ]),
                            "Edit User"
                        ])
                    ])
                ])
            ]),

            // Header section with icon
            m(".flex.items-center.gap-4.mb-8", [
                m(".avatar.placeholder", [
                    m(".bg-secondary.text-secondary-content.rounded-full.w-16.h-16", [
                        m("svg.w-8.h-8", {
                            fill: "none",
                            stroke: "currentColor",
                            viewBox: "0 0 24 24"
                        }, [
                            m("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                "stroke-width": "2",
                                d: "M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            })
                        ])
                    ])
                ]),
                m("div", [
                    m("h1.text-4xl.font-bold.text-base-content", "Edit User"),
                    m("p.text-base-content.opacity-70.text-lg", "Update user information, roles, and account settings")
                ])
            ]),

            // Enhanced success message
            this.data.success ? m(".alert.alert-success.mb-6.shadow-lg", [
                m("svg.w-6.h-6", {
                    fill: "none",
                    stroke: "currentColor",
                    viewBox: "0 0 24 24"
                }, [
                    m("path", {
                        "stroke-linecap": "round",
                        "stroke-linejoin": "round",
                        "stroke-width": "2",
                        d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                    })
                ]),
                m("span.font-medium", "User updated successfully! Redirecting...")
            ]) : null,

            // Enhanced error message
            this.data.error ? m(".alert.alert-error.mb-6.shadow-lg", [
                m("svg.w-6.h-6", {
                    fill: "none",
                    stroke: "currentColor",
                    viewBox: "0 0 24 24"
                }, [
                    m("path", {
                        "stroke-linecap": "round",
                        "stroke-linejoin": "round",
                        "stroke-width": "2",
                        d: "M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    })
                ]),
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
                                    m("svg.w-5.h-5", {
                                        fill: "none",
                                        stroke: "currentColor",
                                        viewBox: "0 0 24 24"
                                    }, [
                                        m("path", {
                                            "stroke-linecap": "round",
                                            "stroke-linejoin": "round",
                                            "stroke-width": "2",
                                            d: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        })
                                    ])
                                ]),
                                m("h2.text-2xl.font-semibold.text-base-content", "Basic Information"),
                                m(".flex-1.h-px.bg-base-300")
                            ]),

                            m(".grid.grid-cols-1.md:grid-cols-2.gap-6", [
                                // Name field with icon
                                m(".form-control", [
                                    m("label.label", [
                                        m("span.label-text.font-medium.flex.items-center.gap-2", [
                                            m("svg.w-4.h-4.text-primary", {
                                                fill: "none",
                                                stroke: "currentColor",
                                                viewBox: "0 0 24 24"
                                            }, [
                                                m("path", {
                                                    "stroke-linecap": "round",
                                                    "stroke-linejoin": "round",
                                                    "stroke-width": "2",
                                                    d: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                })
                                            ]),
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
                                            m("svg.w-4.h-4.text-primary", {
                                                fill: "none",
                                                stroke: "currentColor",
                                                viewBox: "0 0 24 24"
                                            }, [
                                                m("path", {
                                                    "stroke-linecap": "round",
                                                    "stroke-linejoin": "round",
                                                    "stroke-width": "2",
                                                    d: "M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                                })
                                            ]),
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
                                        m("svg.w-4.h-4.text-primary", {
                                            fill: "none",
                                            stroke: "currentColor",
                                            viewBox: "0 0 24 24"
                                        }, [
                                            m("path", {
                                                "stroke-linecap": "round",
                                                "stroke-linejoin": "round",
                                                "stroke-width": "2",
                                                d: "M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                            })
                                        ]),
                                        "Password (Optional)"
                                    ])
                                ]),
                                m("input.input.input-bordered.input-lg.focus:input-primary", {
                                    type: "password",
                                    value: this.data.form.password,
                                    oninput: (e) => this.data.form.password = e.target.value,
                                    placeholder: "Enter new password or leave empty to keep current"
                                }),
                                m("label.label", [
                                    m("span.label-text-alt.text-base-content.opacity-60", "Leave empty to keep current password")
                                ])
                            ])
                        ]),

                        // Roles & Permissions Section
                        m(".mb-8", [
                            m(".flex.items-center.gap-3.mb-6", [
                                m("div.bg-secondary.text-secondary-content.rounded-full.p-2", [
                                    m("svg.w-5.h-5", {
                                        fill: "none",
                                        stroke: "currentColor",
                                        viewBox: "0 0 24 24"
                                    }, [
                                        m("path", {
                                            "stroke-linecap": "round",
                                            "stroke-linejoin": "round",
                                            "stroke-width": "2",
                                            d: "M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                        })
                                    ])
                                ]),
                                m("h2.text-2xl.font-semibold.text-base-content", "Roles & Permissions"),
                                m(".flex-1.h-px.bg-base-300")
                            ]),

                            // Enhanced roles selection
                            m(".form-control", [
                                m("label.label", [
                                    m("span.label-text.font-medium.flex.items-center.gap-2", [
                                        m("svg.w-4.h-4.text-secondary", {
                                            fill: "none",
                                            stroke: "currentColor",
                                            viewBox: "0 0 24 24"
                                        }, [
                                            m("path", {
                                                "stroke-linecap": "round",
                                                "stroke-linejoin": "round",
                                                "stroke-width": "2",
                                                d: "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                            })
                                        ]),
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
                                                    m("input.checkbox.checkbox-secondary.checkbox-lg", {
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
                                    m("svg.w-5.h-5", {
                                        fill: "none",
                                        stroke: "currentColor",
                                        viewBox: "0 0 24 24"
                                    }, [
                                        m("path", {
                                            "stroke-linecap": "round",
                                            "stroke-linejoin": "round",
                                            "stroke-width": "2",
                                            d: "M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                        })
                                    ])
                                ]),
                                m("h2.text-2xl.font-semibold.text-base-content", "Account Settings"),
                                m(".flex-1.h-px.bg-base-300")
                            ]),

                            // Email verified toggle with enhanced styling
                            m(".form-control.mb-6", [
                                m("label.label.cursor-pointer.bg-base-200.rounded-lg.p-4.hover:bg-base-300.transition-colors", [
                                    m("span.label-text.font-medium.flex.items-center.gap-2", [
                                        m("svg.w-5.h-5.text-accent", {
                                            fill: "none",
                                            stroke: "currentColor",
                                            viewBox: "0 0 24 24"
                                        }, [
                                            m("path", {
                                                "stroke-linecap": "round",
                                                "stroke-linejoin": "round",
                                                "stroke-width": "2",
                                                d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            })
                                        ]),
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

                        // Enhanced Email Actions Section
                        m(".mb-8", [
                            m(".flex.items-center.gap-3.mb-6", [
                                m("div.bg-info.text-info-content.rounded-full.p-2", [
                                    m("svg.w-5.h-5", {
                                        fill: "none",
                                        stroke: "currentColor",
                                        viewBox: "0 0 24 24"
                                    }, [
                                        m("path", {
                                            "stroke-linecap": "round",
                                            "stroke-linejoin": "round",
                                            "stroke-width": "2",
                                            d: "M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                        })
                                    ])
                                ]),
                                m("h2.text-2xl.font-semibold.text-base-content", "Email Actions"),
                                m(".flex-1.h-px.bg-base-300")
                            ]),

                            m(".form-control", [
                                m("label.label", [
                                    m("span.label-text.font-medium.flex.items-center.gap-2", [
                                        m("svg.w-4.h-4.text-info", {
                                            fill: "none",
                                            stroke: "currentColor",
                                            viewBox: "0 0 24 24"
                                        }, [
                                            m("path", {
                                                "stroke-linecap": "round",
                                                "stroke-linejoin": "round",
                                                "stroke-width": "2",
                                                d: "M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                                            })
                                        ]),
                                        "Send Email Notifications"
                                    ])
                                ]),
                                m(".grid.grid-cols-1.sm:grid-cols-2.gap-4.mt-3", [
                                    // Password Reset Card
                                    m(".card.bg-base-200.hover:bg-base-300.transition-colors", [
                                        m(".card-body.p-4", [
                                            m("div.flex.items-center.gap-3.mb-3", [
                                                m("div.bg-warning.text-warning-content.rounded-full.p-2", [
                                                    m("svg.w-4.h-4", {
                                                        fill: "none",
                                                        stroke: "currentColor",
                                                        viewBox: "0 0 24 24"
                                                    }, [
                                                        m("path", {
                                                            "stroke-linecap": "round",
                                                            "stroke-linejoin": "round",
                                                            "stroke-width": "2",
                                                            d: "M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                                                        })
                                                    ])
                                                ]),
                                                m("div.flex-1", [
                                                    m("div.font-semibold.text-base-content", "Password Reset"),
                                                    m("div.text-sm.text-base-content.opacity-70", "Send password reset link")
                                                ])
                                            ]),
                                            m("button.btn.btn-warning.btn-sm.w-full", {
                                                type: "button",
                                                onclick: this.handleResendPasswordReset.bind(this),
                                                disabled: !this.data.form.email
                                            }, "Send Reset Link")
                                        ])
                                    ]),

                                    // Email Verification Card
                                    m(".card.bg-base-200.hover:bg-base-300.transition-colors", [
                                        m(".card-body.p-4", [
                                            m("div.flex.items-center.gap-3.mb-3", [
                                                m("div.bg-success.text-success-content.rounded-full.p-2", [
                                                    m("svg.w-4.h-4", {
                                                        fill: "none",
                                                        stroke: "currentColor",
                                                        viewBox: "0 0 24 24"
                                                    }, [
                                                        m("path", {
                                                            "stroke-linecap": "round",
                                                            "stroke-linejoin": "round",
                                                            "stroke-width": "2",
                                                            d: "M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                                        })
                                                    ])
                                                ]),
                                                m("div.flex-1", [
                                                    m("div.font-semibold.text-base-content", "Email Verification"),
                                                    m("div.text-sm.text-base-content.opacity-70", "Send verification link")
                                                ])
                                            ]),
                                            m("button.btn.btn-success.btn-sm.w-full", {
                                                type: "button",
                                                onclick: this.handleResendEmailVerification.bind(this),
                                                disabled: !this.data.form.email || this.data.form.email_verified
                                            }, this.data.form.email_verified ? "Already Verified" : "Send Verification")
                                        ])
                                    ])
                                ]),
                                m(".text-sm.text-base-content.opacity-60.mt-4.p-4.bg-base-200.rounded-lg", [
                                    m("div.flex.items-start.gap-2", [
                                        m("svg.w-4.h-4.mt-0.5.text-info", {
                                            fill: "none",
                                            stroke: "currentColor",
                                            viewBox: "0 0 24 24"
                                        }, [
                                            m("path", {
                                                "stroke-linecap": "round",
                                                "stroke-linejoin": "round",
                                                "stroke-width": "2",
                                                d: "M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            })
                                        ]),
                                        m("div", [
                                            m("strong", "Email Actions:"),
                                            m("br"),
                                            "• Password reset sends a secure link to change the user's password",
                                            m("br"),
                                            "• Email verification sends a link to verify the email address",
                                            m("br"),
                                            "• Verification is disabled if the email is already verified"
                                        ])
                                    ])
                                ])
                            ])
                        ]),

                        // Enhanced submit buttons
                        m(".card-actions.justify-end.gap-4.pt-6.border-t.border-base-300", [
                            m("button.btn.btn-ghost.btn-lg.gap-2", {
                                type: "button",
                                onclick: () => m.route.set('/admin/users')
                            }, [
                                m("svg.w-5.h-5", {
                                    fill: "none",
                                    stroke: "currentColor",
                                    viewBox: "0 0 24 24"
                                }, [
                                    m("path", {
                                        "stroke-linecap": "round",
                                        "stroke-linejoin": "round",
                                        "stroke-width": "2",
                                        d: "M6 18L18 6M6 6l12 12"
                                    })
                                ]),
                                "Cancel"
                            ]),
                            m("button.btn.btn-primary.btn-lg.gap-2", {
                                type: "submit",
                                disabled: this.data.loading
                            }, [
                                this.data.loading ? 
                                    m("span.loading.loading-spinner.loading-sm") :
                                    m("svg.w-5.h-5", {
                                        fill: "none",
                                        stroke: "currentColor",
                                        viewBox: "0 0 24 24"
                                    }, [
                                        m("path", {
                                            "stroke-linecap": "round",
                                            "stroke-linejoin": "round",
                                            "stroke-width": "2",
                                            d: "M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                        })
                                    ]),
                                this.data.loading ? "Updating User..." : "Update User"
                            ])
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export {EditUser};