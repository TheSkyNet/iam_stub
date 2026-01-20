import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";
import { Fieldset, FormField, SubmitButton } from "../../components/Form";
import {RolesService} from "../../services/RolesService";

const RolesPage = {

    roles: [],
    loading: true,
    error: null,
    editingRole: null,
    newRole: { name: "", description: "" },

    oninit: function() {
        this.rolesService = new RolesService();
        this.loadRoles();
    },

    loadRoles: function() {
        this.loading = true;
        this.error = null;
        
        return this.rolesService.getAll().then((response) => {
            if (response.success) {
                this.roles = response.data;
            } else {
                this.error = response.message || "Failed to load roles";
            }
            this.loading = false;
        }).catch((err) => {
            this.error = err.message || "An error occurred while loading roles";
            this.loading = false;
        });
    },

    saveRole: function() {
        const isEditing = !!this.editingRole;
        const method = isEditing ? "PUT" : "POST";
        const url = isEditing ? `/api/roles/${this.editingRole.id}` : "/api/roles";
        const data = isEditing ? this.editingRole : this.newRole;

        // use the servis

        if(isEditing){
            this.rolesService.update(data).then((response) => {
                if (response.success) {
                    window.showToast(`Role ${isEditing ? "updated" : "created"}`, "success");
                    this.editingRole = null;
                    this.newRole = { name: "", description: "" };
                    this.loadRoles();
                    document.getElementById('role_modal').close();
                } else {
                    window.showToast(response, "error");
                }
            })
        }
        if (!isEditing){
            this.rolesService.create(data).then((response) => {
                if (response.success) {
                    window.showToast(`Role ${isEditing ? "updated" : "created"}`, "success");
                    this.editingRole = null;
                    this.newRole = { name: "", description: "" };
                    this.loadRoles();
                    document.getElementById('role_modal').close();
                } else {
                    window.showToast(response, "error");
                }
            })
        }
    },

    deleteRole: function(id) {
        if (!confirm("Are you sure you want to delete this role?")) {
            return;
        }
        return this.rolesService.delete(id).then((response) => {
            if (response.success) {
                window.showToast("Role deleted", "success");
                this.loadRoles();
            } else {
                window.showToast(response, "error");
            }
        }).catch((err) => {
            window.showToast(err, "error");
        });
    },

    view: function() {
        return m(".container.mx-auto.p-4", [
            m(".flex.justify-between.items-center.mb-6", [
                m("h1.text-3xl.font-bold", "Roles Management"),
                m(".flex.gap-2", [
                    m("button.btn.btn-outline.btn-sm", { onclick: () => this.loadRoles() }, [
                        m(Icon, { icon: "fa-solid fa-rotate" }),
                        " Refresh"
                    ]),
                    m("button.btn.btn-primary", {
                        onclick: () => {
                            this.editingRole = null;
                            this.newRole = { name: "", description: "" };
                            document.getElementById('role_modal').showModal();
                        }
                    }, [
                        m(Icon, { icon: "fa-solid fa-plus" }),
                        " Add Role"
                    ])
                ])
            ]),

            this.loading 
                ? m(".flex.justify-center.p-12", m("span.loading.loading-spinner.loading-lg"))
                : this.error
                    ? m(".alert.alert-error", [
                        m(Icon, { icon: "fa-solid fa-circle-exclamation" }),
                        m("span", this.error)
                    ])
                    : m(".overflow-x-auto.bg-base-100.rounded-xl.shadow", [
                        m("table.table.table-zebra", [
                            m("thead", [
                                m("tr", [
                                    m("th", "ID"),
                                    m("th", "Name"),
                                    m("th", "Description"),
                                    m("th.text-right", "Actions")
                                ])
                            ]),
                            m("tbody", [
                                this.roles.length === 0 
                                    ? m("tr", m("td.text-center[colspan=4]", "No roles found"))
                                    : this.roles.map(role => m("tr", [
                                        m("td", role.id),
                                        m("td.font-bold", role.name),
                                        m("td", role.description),
                                        m("td.text-right", [
                                            m("button.btn.btn-sm.btn-ghost", {
                                                onclick: () => {
                                                    this.editingRole = JSON.parse(JSON.stringify(role));
                                                    document.getElementById('role_modal').showModal();
                                                }
                                            }, m(Icon, { icon: "fa-solid fa-pen" })),
                                            m("button.btn.btn-sm.btn-ghost.text-error", {
                                                onclick: () => this.deleteRole(role.id)
                                            }, m(Icon, { icon: "fa-solid fa-trash" }))
                                        ])
                                    ]))
                            ])
                        ])
                    ]),

            // Role Modal
            m("dialog#role_modal.modal", [
                m(".modal-box", [
                    m("h3.font-bold.text-lg.mb-4", this.editingRole ? "Edit Role" : "Add New Role"),
                    m(Fieldset, { legend: "Role Information", icon: "fa-solid fa-user-shield" }, [
                        m(FormField, {
                            label: "Role Name",
                            icon: "fa-solid fa-tag",
                            placeholder: "e.g. Editor",
                            value: this.editingRole ? this.editingRole.name : this.newRole.name,
                            oninput: (e) => {
                                if (this.editingRole) this.editingRole.name = e.target.value;
                                else this.newRole.name = e.target.value;
                            },
                            required: true
                        }),
                        m("fieldset.fieldset", [
                            m("legend.fieldset-legend", "Description"),
                            m("label.textarea.w-full", [
                                m("textarea.grow", {
                                    placeholder: "Role description...",
                                    value: this.editingRole ? this.editingRole.description : this.newRole.description,
                                    oninput: (e) => {
                                        if (this.editingRole) this.editingRole.description = e.target.value;
                                        else this.newRole.description = e.target.value;
                                    }
                                })
                            ])
                        ])
                    ]),
                    m(".modal-action", [
                        m(SubmitButton, { 
                            class: "btn-primary",
                            onclick: () => this.saveRole(),
                            icon: "fa-solid fa-save"
                        }, "Save"),
                        m("form[method=dialog]", [
                            m("button.btn", "Cancel")
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default RolesPage;
