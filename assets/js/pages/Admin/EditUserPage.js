import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";
import { Fieldset, FormField, CheckboxField, SubmitButton } from "../../components/Form";
import {UsersService} from "../../services/UsersService";
import {RolesService} from "../../services/RolesService";

const EditUserPage = {
    usersService: null,
    roalsService: null,
    user: null,
    roles: [],
    loading: true,
    saving: false,

    oninit: function(vnode) {
        this.usersService = new UsersService();
        this.roalsService = new RolesService();
        this.loadData(vnode.attrs.id);
    },

    loadData: function(id) {
        this.loading = true;
        Promise.all([
            this.loadUser(id),
            this.loadRoles()
        ]).finally(() => {
            this.loading = false;
            m.redraw();
        });
    },

    loadUser: function(id) {
        return this.usersService.get(id).then((response) => {
            if (response.success) {
                this.user = response.data;
            } else {
                window.showToast(response, "error");
            }
        });
    },

    loadRoles: function() {
        return this.roalsService.get().then((response) => {
            if (response.success) {
                this.roles = response.data;
            }
        });
    },

    save: function() {
        this.saving = true;
        this.usersService.update(this.user.id, this.user).then((response) => {
            if (response.success) {
                window.showToast("User updated successfully", "success");
            } else {
                window.showToast(response, "error");
            }
            this.saving = false;
        }).catch((err) => {
            window.showToast(err.response , "error");
            this.saving = false;
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

    view: function(vnode) {
        const userId = vnode.attrs.id;
        
        if (this.loading) {
            return m(".container.mx-auto.p-4", m(".flex.justify-center.p-12", m("span.loading.loading-spinner.loading-lg")));
        }

        if (!this.user) {
            return m(".container.mx-auto.p-4", [
                m(".alert.alert-error", [
                    m(Icon, { icon: "fa-solid fa-circle-exclamation" }),
                    m("span", "User not found")
                ]),
                m(m.route.Link, { href: "/admin/users", class: "btn btn-ghost mt-4" }, "Back to Users")
            ]);
        }

        return m(".container.mx-auto.p-4", [
            m(".max-w-2xl.mx-auto", [
                m(".flex.items-center.gap-4.mb-6", [
                    m(m.route.Link, { href: "/admin/users", class: "btn btn-circle btn-ghost" }, m(Icon, { name: "fa-solid fa-arrow-left" })),
                    m("h1.text-3xl.font-bold", `Edit User #${userId}`)
                ]),
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m(Fieldset, { legend: "Account Details", icon: "fa-solid fa-user-gear" }, [
                            m(".grid.grid-cols-1.md:grid-cols-2.gap-4", [
                                m(FormField, {
                                    label: "Full Name",
                                    icon: "fa-solid fa-user",
                                    autocomplete: "name",
                                    value: this.user.name,
                                    oninput: (e) => this.user.name = e.target.value,
                                    required: true
                                }),
                                m(FormField, {
                                    label: "Email",
                                    icon: "fa-solid fa-envelope",
                                    type: "email",
                                    autocomplete: "email",
                                    value: this.user.email,
                                    oninput: (e) => this.user.email = e.target.value,
                                    required: true
                                }),
                                m(FormField, {
                                    containerClass: "md:col-span-2",
                                    label: "Password (leave blank to keep current)",
                                    icon: "fa-solid fa-lock",
                                    type: "password",
                                    placeholder: "********",
                                    autocomplete: "new-password",
                                    oninput: (e) => this.user.password = e.target.value
                                })
                            ])
                        ]),

                        m(Fieldset, { legend: "Roles & Status", icon: "fa-solid fa-user-shield", class: "mt-6" }, [
                            m(".flex.flex-wrap.gap-2.mb-4", 
                                this.roles.map(role => 
                                    m(CheckboxField, {
                                        label: role.name,
                                        checked: this.user.roles.includes(role.name),
                                        onchange: () => this.toggleRole(role.name)
                                    })
                                )
                            ),
                            m(".form-control", [
                                m("label.label.cursor-pointer.justify-start.gap-4", [
                                    m("input.toggle.toggle-success", { 
                                        type: "checkbox", 
                                        checked: this.user.email_verified,
                                        onchange: (e) => this.user.email_verified = e.target.checked
                                    }),
                                    m("span.label-text", `Account Status: ${this.user.email_verified ? "Verified" : "Unverified"}`)
                                ])
                            ])
                        ]),

                        m(".card-actions.justify-end.mt-6", [
                            m(m.route.Link, { href: "/admin/users", class: "btn btn-ghost" }, "Cancel"),
                            m(SubmitButton, {
                                class: "btn-primary",
                                onclick: () => this.save(),
                                loading: this.saving,
                                icon: "fa-solid fa-save"
                            }, " Save Changes")
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default EditUserPage;
