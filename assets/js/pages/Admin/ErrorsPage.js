import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";
import { Fieldset, FormField } from "../../components/Form";

const ErrorsPage = {
    errors: [],
    loading: true,
    error: null,
    total: 0,
    limit: 20,
    offset: 0,
    filters: {
        level: "",
        q: ""
    },

    oninit: function() {
        this.loadErrors();
    },

    loadErrors: function() {
        this.loading = true;
        this.error = null;
        
        let url = `/api/errors?limit=${this.limit}&offset=${this.offset}`;
        if (this.filters.level) {
            url += `&level=${this.filters.level}`;
        }
        if (this.filters.q) {
            url += `&q=${encodeURIComponent(this.filters.q)}`;
        }

        return m.request({
            method: "GET",
            url: url,
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                this.errors = response.data.items;
                this.total = response.data.total;
            } else {
                this.error = response.message || "Failed to load error logs";
            }
            this.loading = false;
        }).catch((err) => {
            this.error = err.message || "An error occurred while loading error logs";
            this.loading = false;
        });
    },

    deleteError: function(id) {
        if (!confirm("Are you sure you want to delete this log entry?")) {
            return;
        }
        return m.request({
            method: "DELETE",
            url: `/api/errors/${id}`,
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                window.showToast("Log entry deleted", "success");
                this.loadErrors();
            } else {
                window.showToast(response.message || "Failed to delete log entry", "error");
            }
        }).catch((err) => {
            window.showToast(err.message || "An error occurred", "error");
        });
    },

    cleanupErrors: function() {
        const days = prompt("Delete logs older than how many days?", "30");
        if (days === null) return;
        
        return m.request({
            method: "POST",
            url: "/api/errors/cleanup",
            body: { days: parseInt(days) },
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                window.showToast(`Cleaned up ${response.data.deleted} entries`, "success");
                this.loadErrors();
            } else {
                window.showToast(response.message || "Cleanup failed", "error");
            }
        }).catch((err) => {
            window.showToast(err.message || "An error occurred", "error");
        });
    },

    getLevelBadgeClass: function(level) {
        switch (level.toLowerCase()) {
            case "error": case "critical": case "alert": case "emergency": return "badge-error";
            case "warning": return "badge-warning";
            case "info": case "notice": return "badge-info";
            case "debug": return "badge-ghost";
            default: return "badge-ghost";
        }
    },

    view: function() {
        return m(".container.mx-auto.p-4", [
            m(".flex.justify-between.items-center.mb-6", [
                m("h1.text-3xl.font-bold", "Application Error Logs"),
                m(".flex.gap-2", [
                    m("button.btn.btn-outline.btn-sm", { onclick: () => this.cleanupErrors() }, [
                        m(Icon, { icon: "fa-solid fa-broom" }),
                        " Cleanup"
                    ]),
                    m("button.btn.btn-outline.btn-sm", { onclick: () => this.loadErrors() }, [
                        m(Icon, { icon: "fa-solid fa-rotate" }),
                        " Refresh"
                    ])
                ])
            ]),

            // Filters
            m(Fieldset, { legend: "Filters", icon: "fa-solid fa-filter", class: "mb-6" }, [
                m(".grid.grid-cols-1.md:grid-cols-3.gap-4", [
                    m("fieldset.fieldset", [
                        m("legend.fieldset-legend", "Level"),
                        m("select.select.w-full", {
                            value: this.filters.level,
                            onchange: (e) => {
                                this.filters.level = e.target.value;
                                this.offset = 0;
                                this.loadErrors();
                            }
                        }, [
                            m("option", { value: "" }, "All Levels"),
                            m("option", { value: "error" }, "Error"),
                            m("option", { value: "warning" }, "Warning"),
                            m("option", { value: "info" }, "Info"),
                            m("option", { value: "debug" }, "Debug")
                        ])
                    ]),
                    m(".md:col-span-2", [
                        m(FormField, {
                            label: "Search",
                            icon: "fa-solid fa-magnifying-glass",
                            placeholder: "Search message or URL...",
                            value: this.filters.q,
                            oninput: (e) => this.filters.q = e.target.value,
                            onkeypress: (e) => { if (e.key === 'Enter') { this.offset = 0; this.loadErrors(); } }
                        })
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
                        m("table.table.table-zebra.table-sm", [
                            m("thead", [
                                m("tr", [
                                    m("th", "ID"),
                                    m("th", "Level"),
                                    m("th", "Message"),
                                    m("th", "URL"),
                                    m("th", "Created At"),
                                    m("th.text-right", "Actions")
                                ])
                            ]),
                            m("tbody", [
                                this.errors.length === 0 
                                    ? m("tr", m("td.text-center[colspan=6]", "No log entries found"))
                                    : this.errors.map(log => m("tr", [
                                        m("td", log.id),
                                        m("td", m(".badge", { class: this.getLevelBadgeClass(log.level) }, log.level)),
                                        m("td.max-w-md.truncate", { title: log.message }, log.message),
                                        m("td.max-w-xs.truncate", { title: log.url }, log.url),
                                        m("td", log.created_at),
                                        m("td.text-right", [
                                            m("button.btn.btn-xs.btn-ghost.text-error", {
                                                onclick: () => this.deleteError(log.id),
                                                title: "Delete Entry"
                                            }, m(Icon, { icon: "fa-solid fa-trash" }))
                                        ])
                                    ]))
                            ])
                        ]),
                        
                        // Pagination
                        m(".flex.justify-between.items-center.p-4", [
                            m("span.text-sm", `Showing ${this.errors.length} of ${this.total} entries`),
                            m(".join", [
                                m("button.join-item.btn.btn-sm", {
                                    disabled: this.offset === 0,
                                    onclick: () => {
                                        this.offset -= this.limit;
                                        this.loadErrors();
                                    }
                                }, "Previous"),
                                m("button.join-item.btn.btn-sm", {
                                    disabled: (this.offset + this.limit) >= this.total,
                                    onclick: () => {
                                        this.offset += this.limit;
                                        this.loadErrors();
                                    }
                                }, "Next")
                            ])
                        ])
                    ])
        ]);
    }
};

export default ErrorsPage;
