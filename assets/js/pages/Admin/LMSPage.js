import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";
import { Fieldset, FormField, SubmitButton } from "../../components/Form";

const LMSPage = {
    data: {
        integrations: {},
        statistics: {}
    },
    loading: true,
    error: null,
    testing: false,
    testResult: null,
    testIntegration: "ollama",
    testPrompt: "Say hello!",

    oninit: function() {
        this.loadStatus();
    },

    loadStatus: function() {
        this.loading = true;
        this.error = null;
        
        return m.request({
            method: "GET",
            url: "/api/lms/status",
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                this.data = response.data;
            } else {
                this.error = response.message || "Failed to load LMS status";
            }
            this.loading = false;
        }).catch((err) => {
            this.error = err.message || "An error occurred while loading LMS status";
            this.loading = false;
        });
    },

    refreshStatus: function() {
        return m.request({
            method: "POST",
            url: "/api/lms/refresh",
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                window.showToast("LMS status refreshed", "success");
                this.loadStatus();
            } else {
                window.showToast(response.message || "Refresh failed", "error");
            }
        }).catch((err) => {
            window.showToast(err.message || "An error occurred", "error");
        });
    },

    runTest: function() {
        this.testing = true;
        this.testResult = null;
        
        return m.request({
            method: "POST",
            url: "/api/lms/test",
            body: { 
                integration: this.testIntegration,
                prompt: this.testPrompt
            },
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.success) {
                this.testResult = response.data;
            } else {
                window.showToast(response.message || "Test failed", "error");
            }
            this.testing = false;
        }).catch((err) => {
            window.showToast(err.message || "An error occurred", "error");
            this.testing = false;
        });
    },

    getStatusBadgeClass: function(status) {
        switch (status) {
            case "healthy": return "badge-success";
            case "unhealthy": return "badge-error";
            case "degraded": return "badge-warning";
            case "unknown": return "badge-ghost";
            default: return "badge-ghost";
        }
    },

    view: function() {
        return m(".container.mx-auto.p-4", [
            m(".flex.justify-between.items-center.mb-6", [
                m("h1.text-3xl.font-bold", "LMS & AI Integrations"),
                m("button.btn.btn-outline.btn-sm", { onclick: () => this.refreshStatus() }, [
                    m(Icon, { icon: "fa-solid fa-sync" }),
                    " Refresh Health"
                ])
            ]),

            this.loading 
                ? m(".flex.justify-center.p-12", m("span.loading.loading-spinner.loading-lg"))
                : this.error
                    ? m(".alert.alert-error.mb-6", [
                        m(Icon, { icon: "fa-solid fa-circle-exclamation" }),
                        m("span", this.error)
                    ])
                    : [
                        // Integrations Grid
                        m(".grid.grid-cols-1.md:grid-cols-3.gap-6.mb-8", 
                            Object.entries(this.data.integrations).map(([name, info]) => 
                                m(".card.bg-base-100.shadow-xl", [
                                    m(".card-body", [
                                        m(".flex.justify-between.items-start", [
                                            m("h2.card-title.capitalize", name),
                                            m(".badge", { class: this.getStatusBadgeClass(info.status) }, info.status)
                                        ]),
                                        m("p.text-sm.opacity-70", info.error || "No issues reported"),
                                        m(".mt-2", [
                                            m("p.text-xs.font-bold", "Capabilities:"),
                                            m(".flex.flex-wrap.gap-1.mt-1", 
                                                (info.capabilities || []).map(cap => m(".badge.badge-outline.badge-xs", cap))
                                            )
                                        ]),
                                        m(".card-actions.justify-end.mt-4", [
                                            m("button.btn.btn-xs.btn-ghost", {
                                                onclick: () => {
                                                    this.testIntegration = name;
                                                    document.getElementById('test_modal').showModal();
                                                }
                                            }, "Test Integration")
                                        ])
                                    ])
                                ])
                            )
                        ),

                        // Statistics
                        m("h2.text-2xl.font-bold.mb-4", "Usage Statistics"),
                        m(".grid.grid-cols-1.md:grid-cols-4.gap-4.mb-8", [
                            m(".stats.shadow", [
                                m(".stat", [
                                    m(".stat-title", "Total Requests"),
                                    m(".stat-value", this.data.statistics.total_requests || 0)
                                ])
                            ]),
                            m(".stats.shadow", [
                                m(".stat", [
                                    m(".stat-title", "Success Rate"),
                                    m(".stat-value.text-success", 
                                        this.data.statistics.total_requests > 0 
                                            ? `${Math.round((this.data.statistics.successful_requests / this.data.statistics.total_requests) * 100)}%`
                                            : "0%"
                                    )
                                ])
                            ]),
                            m(".stats.shadow", [
                                m(".stat", [
                                    m(".stat-title", "Errors"),
                                    m(".stat-value.text-error", this.data.statistics.failed_requests || 0)
                                ])
                            ]),
                            m(".stats.shadow", [
                                m(".stat", [
                                    m(".stat-title", "Last Request"),
                                    m(".stat-desc", this.data.statistics.last_request_at || "Never")
                                ])
                            ])
                        ])
                    ],

            // Test Modal
            m("dialog#test_modal.modal", [
                m(".modal-box", [
                    m("h3.font-bold.text-lg.mb-4", `Test ${this.testIntegration}`),
                    m(Fieldset, { legend: "Test Prompt", icon: "fa-solid fa-comment-dots" }, [
                        m("fieldset.fieldset", [
                            m("legend.fieldset-legend", "Prompt"),
                            m("label.textarea.w-full", [
                                m("textarea.grow", {
                                    placeholder: "Enter a prompt to test...",
                                    value: this.testPrompt,
                                    oninput: (e) => this.testPrompt = e.target.value
                                })
                            ])
                        ])
                    ]),
                    this.testResult && m(".mt-4", [
                        m("p.text-sm.font-bold", "Result:"),
                        m(".bg-base-200.p-3.rounded.mt-1.text-xs.max-h-60.overflow-auto", [
                            m("pre", JSON.stringify(this.testResult, null, 2))
                        ])
                    ]),
                    m(".modal-action", [
                        m(SubmitButton, { 
                            class: "btn-primary",
                            onclick: () => this.runTest(),
                            loading: this.testing,
                            icon: "fa-solid fa-play"
                        }, "Run Test"),
                        m("form[method=dialog]", [
                            m("button.btn", "Close")
                        ])
                    ])
                ])
            ])
        ]);
    }
};

export default LMSPage;
