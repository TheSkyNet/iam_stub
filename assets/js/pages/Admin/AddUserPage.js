import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";
import { Fieldset, FormField, CheckboxField, SubmitButton } from "../../components/Form";

const AddUserPage = {
    user: {
        name: "",
        email: "",
        password: "",
        roles: []
    },
    roles: [],
    loading: false,

    oninit: function() {
        this.loadRoles();
    },

    loadRoles: function() {
        return m.request({
            method: "GET",
            url: "/api/roles",
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                this.roles = response.data;
            }
        });
    },

    save: function() {
        this.loading = true;
        return m.request({
            method: "POST",
            url: "/api/users",
            body: this.user,
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                window.showToast("User created successfully", "success");
                m.route.set("/admin/users");
            } else {
                window.showToast(response.message || "Failed to create user", "error");
            }
            this.loading = false;
        }).catch((err) => {
            window.showToast(err.message || "An error occurred", "error");
            this.loading = false;
        });
    },

    toggleRole: function(roleName) {
        const index = this.user.roles.indexOf(roleName);
        if (index > -1) {
            this.user.roles.splice(index, 1);
        } else {
            this.user.roles.push(roleName);
        }
    },

    view: function() {
        return m(".container.mx-auto.p-4", [
            m(".max-w-2xl.mx-auto", [
                m(".flex.items-center.gap-4.mb-6", [
                    m(m.route.Link, { href: "/admin/users", class: "btn btn-circle btn-ghost" }, m(Icon, { icon: "fa-solid fa-arrow-left" })),
                    m("h1.text-3xl.font-bold", "Add New User")
                ]),
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m(Fieldset, { legend: "Account Details", icon: "fa-solid fa-user-gear" }, [
                            m(".grid.grid-cols-1.md:grid-cols-2.gap-4", [
                                m(FormField, {
                                    label: "Full Name",
                                    icon: "fa-solid fa-user",
                                    placeholder: "Jane Doe",
                                    value: this.user.name,
                                    oninput: (e) => this.user.name = e.target.value,
                                    required: true
                                }),
                                m(FormField, {
                                    label: "Email",
                                    icon: "fa-solid fa-envelope",
                                    type: "email",
                                    placeholder: "jane@example.com",
                                    value: this.user.email,
                                    oninput: (e) => this.user.email = e.target.value,
                                    required: true
                                }),
                                m(FormField, {
                                    containerClass: "md:col-span-2",
                                    label: "Password",
                                    icon: "fa-solid fa-lock",
                                    type: "password",
                                    placeholder: "********",
                                    value: this.user.password,
                                    oninput: (e) => this.user.password = e.target.value,
                                    required: true
                                })
                            ])
                        ]),

                        m(Fieldset, { legend: "Roles", icon: "fa-solid fa-user-shield", class: "mt-6" }, [
                            m(".flex.flex-wrap.gap-2", 
                                this.roles.map(role => 
                                    m(CheckboxField, {
                                        label: role.name,
                                        checked: this.user.roles.includes(role.name),
                                        onchange: () => this.toggleRole(role.name)
                                    })
                                )
                            )
                        ]),

                        m(".card-actions.justify-end.mt-6", [
                            m(m.route.Link, { href: "/admin/users", class: "btn btn-ghost" }, "Cancel"),
                            m(SubmitButton, {
                                class: "btn-primary",
                                onclick: () => this.save(),
                                loading: this.loading,
                                icon: "fa-solid fa-user-plus"
                            }, " Create User")
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default AddUserPage;
