import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";

const SettingsPage = {
    settings: [],
    loading: true,
    error: null,
    editingId: null,
    editValue: "",

    oninit: function() {
        this.loadSettings();
    },

    loadSettings: function() {
        this.loading = true;
        this.error = null;
        
        return m.request({
            method: "GET",
            url: "/api/settings",
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                this.settings = response.data;
            } else {
                this.error = response.message || "Failed to load settings";
            }
            this.loading = false;
        }).catch((err) => {
            this.error = err.message || "An error occurred while loading settings";
            this.loading = false;
        });
    },

    startEdit: function(setting) {
        this.editingId = setting.id;
        this.editValue = setting.value;
    },

    cancelEdit: function() {
        this.editingId = null;
        this.editValue = "";
    },

    saveSetting: function(id) {
        return m.request({
            method: "PUT",
            url: `/api/settings/${id}`,
            body: { value: this.editValue },
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                window.showToast("Setting updated", "success");
                this.editingId = null;
                this.loadSettings();
            } else {
                window.showToast(response, "error");
            }
        }).catch((err) => {
            window.showToast(err, "error");
        });
    },

    renderValueInput: function(setting) {
        if (this.editingId === setting.id) {
            const inputId = `setting-${setting.id}`;
            if (setting.type === "boolean") {
                return m("select.select.select-sm.w-full", {
                    id: inputId,
                    "aria-label": `Value for ${setting.key}`,
                    value: this.editValue,
                    onchange: (e) => this.editValue = e.target.value
                }, [
                    m("option", { value: "1" }, "True"),
                    m("option", { value: "0" }, "False")
                ]);
            } else if (setting.type === "json" || setting.type === "array") {
                return m("label.textarea.textarea-sm.w-full", { for: inputId }, [
                    m("textarea.grow", {
                        id: inputId,
                        "aria-label": `Value for ${setting.key}`,
                        value: this.editValue,
                        oninput: (e) => this.editValue = e.target.value,
                        rows: 5,
                        autocomplete: "off"
                    })
                ]);
            } else {
                return m("label.input.input-sm.w-full", { for: inputId }, [
                    m("input.grow", {
                        id: inputId,
                        "aria-label": `Value for ${setting.key}`,
                        type: setting.type === "integer" || setting.type === "float" ? "number" : "text",
                        value: this.editValue,
                        oninput: (e) => this.editValue = e.target.value,
                        autocomplete: "off"
                    })
                ]);
            }
        }

        // Display mode
        if (setting.type === "boolean") {
            return m(".badge", { class: setting.value === "1" || setting.value === true ? "badge-success" : "badge-error" }, setting.value === "1" || setting.value === true ? "True" : "False");
        } else if (setting.type === "json" || setting.type === "array") {
            return m("pre.text-xs.bg-base-200.p-2.rounded.max-h-24.overflow-auto", setting.value);
        } else {
            return m("span", setting.value);
        }
    },

    view: function() {
        return m(".container.mx-auto.p-4", [
            m(".flex.justify-between.items-center.mb-6", [
                m("h1.text-3xl.font-bold", "Site Settings"),
                m("button.btn.btn-outline.btn-sm", { onclick: () => this.loadSettings() }, [
                    m(Icon, { icon: "fa-solid fa-rotate" }),
                    " Refresh"
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
                                    m("th", "Key"),
                                    m("th", "Type"),
                                    m("th", { style: { width: "50%" } }, "Value"),
                                    m("th", "Description"),
                                    m("th.text-right", "Actions")
                                ])
                            ]),
                            m("tbody", [
                                this.settings.length === 0 
                                    ? m("tr", m("td.text-center[colspan=5]", "No settings found"))
                                    : this.settings.map(setting => m("tr", [
                                        m("td.font-bold", setting.key),
                                        m("td", m(".badge.badge-ghost", setting.type)),
                                        m("td", this.renderValueInput(setting)),
                                        m("td.text-xs", setting.description),
                                        m("td.text-right", [
                                            this.editingId === setting.id 
                                                ? [
                                                    m("button.btn.btn-xs.btn-success.mr-1", { onclick: () => this.saveSetting(setting.id) }, m(Icon, { icon: "fa-solid fa-check" })),
                                                    m("button.btn.btn-xs.btn-ghost", { onclick: () => this.cancelEdit() }, m(Icon, { icon: "fa-solid fa-xmark" }))
                                                  ]
                                                : m("button.btn.btn-xs.btn-ghost", { onclick: () => this.startEdit(setting) }, m(Icon, { icon: "fa-solid fa-pen" }))
                                        ])
                                    ]))
                            ])
                        ])
                    ])
        ]);
    }
};

export default SettingsPage;
