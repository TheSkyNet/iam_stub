import m from "mithril";
import { Icon } from "../../components/Icon";
import { AuthService } from "../../services/AuthserviceService";
import { Fieldset, FormField } from "../../components/Form";

const JobsPage = {
    jobs: [],
    loading: true,
    error: null,
    pagination: {
        total: 0,
        limit: 50,
        offset: 0,
        has_more: false
    },
    filters: {
        status: "",
        type: ""
    },
    types: [],

    oninit: function() {
        this.loadJobs();
        this.loadTypes();
    },

    loadJobs: function() {
        this.loading = true;
        this.error = null;
        
        let url = `/api/jobs?limit=${this.pagination.limit}&offset=${this.pagination.offset}`;
        if (this.filters.status) {
            url += `&status=${this.filters.status}`;
        }
        if (this.filters.type) {
            url += `&type=${this.filters.type}`;
        }

        return m.request({
            method: "GET",
            url: url,
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.status === "success") {
                this.jobs = response.data;
                this.pagination = response.pagination;
            } else {
                this.error = response.message || "Failed to load jobs";
            }
            this.loading = false;
        }).catch((err) => {
            this.error = err.message || "An error occurred while loading jobs";
            this.loading = false;
        });
    },

    loadTypes: function() {
        return m.request({
            method: "GET",
            url: "/api/jobs/types",
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.status === "success") {
                this.types = response.data;
            }
        }).catch(() => {});
    },

    retryJob: function(id) {
        return m.request({
            method: "POST",
            url: `/api/jobs/${id}/retry`,
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.status === "success") {
                window.showToast("Job queued for retry", "success");
                this.loadJobs();
            } else {
                window.showToast(response, "error");
            }
        }).catch((err) => {
            window.showToast(err, "error");
        });
    },

    cancelJob: function(id) {
        if (!confirm("Are you sure you want to cancel this job?")) {
            return;
        }
        return m.request({
            method: "DELETE",
            url: `/api/jobs/${id}`,
            headers: AuthService.getAuthHeaders()
        }).then((response) => {
            if (response.status === "success") {
                window.showToast("Job cancelled", "success");
                this.loadJobs();
            } else {
                window.showToast(response, "error");
            }
        }).catch((err) => {
            window.showToast(err, "error");
        });
    },

    getStatusBadgeClass: function(status) {
        switch (status) {
            case "completed": return "badge-success";
            case "failed": return "badge-error";
            case "processing": return "badge-info";
            case "pending": return "badge-warning";
            default: return "badge-ghost";
        }
    },

    view: function() {
        return m(".container.mx-auto.p-4", [
            m(".flex.justify-between.items-center.mb-6", [
                m("h1.text-3xl.font-bold", "Job Queue Management"),
                m("button.btn.btn-outline.btn-sm", { onclick: () => this.loadJobs() }, [
                    m(Icon, { icon: "fa-solid fa-rotate" }),
                    " Refresh"
                ])
            ]),

            // Filters
            m(Fieldset, { legend: "Filters", icon: "fa-solid fa-filter", class: "mb-6" }, [
                m(".grid.grid-cols-1.md:grid-cols-2.gap-4", [
                    m("fieldset.fieldset", [
                        m("legend.fieldset-legend", "Status"),
                        m("select.select.w-full", {
                            value: this.filters.status,
                            onchange: (e) => {
                                this.filters.status = e.target.value;
                                this.pagination.offset = 0;
                                this.loadJobs();
                            }
                        }, [
                            m("option", { value: "" }, "All Statuses"),
                            m("option", { value: "pending" }, "Pending"),
                            m("option", { value: "processing" }, "Processing"),
                            m("option", { value: "completed" }, "Completed"),
                            m("option", { value: "failed" }, "Failed")
                        ])
                    ]),
                    m("fieldset.fieldset", [
                        m("legend.fieldset-legend", "Job Type"),
                        m("select.select.w-full", {
                            value: this.filters.type,
                            onchange: (e) => {
                                this.filters.type = e.target.value;
                                this.pagination.offset = 0;
                                this.loadJobs();
                            }
                        }, [
                            m("option", { value: "" }, "All Types"),
                            this.types.map(type => m("option", { value: type }, type))
                        ])
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
                                    m("th", "Type"),
                                    m("th", "Status"),
                                    m("th", "Priority"),
                                    m("th", "Attempts"),
                                    m("th", "Created At"),
                                    m("th.text-right", "Actions")
                                ])
                            ]),
                            m("tbody", [
                                this.jobs.length === 0 
                                    ? m("tr", m("td.text-center[colspan=7]", "No jobs found"))
                                    : this.jobs.map(job => m("tr", [
                                        m("td", job.id),
                                        m("td.font-mono.text-xs", job.type),
                                        m("td", m(".badge", { class: this.getStatusBadgeClass(job.status) }, job.status)),
                                        m("td", job.priority),
                                        m("td", `${job.attempts}/${job.max_attempts}`),
                                        m("td", job.created_at),
                                        m("td.text-right", [
                                            job.status === "failed" && m("button.btn.btn-sm.btn-ghost.text-success", {
                                                onclick: () => this.retryJob(job.id),
                                                title: "Retry Job"
                                            }, m(Icon, { icon: "fa-solid fa-rotate-right" })),
                                            (job.status === "pending" || job.status === "failed") && m("button.btn.btn-sm.btn-ghost.text-error", {
                                                onclick: () => this.cancelJob(job.id),
                                                title: "Cancel Job"
                                            }, m(Icon, { icon: "fa-solid fa-ban" }))
                                        ])
                                    ]))
                            ])
                        ]),
                        
                        // Pagination
                        m(".flex.justify-between.items-center.p-4", [
                            m("span.text-sm", `Showing ${this.jobs.length} of ${this.pagination.total} jobs`),
                            m(".join", [
                                m("button.join-item.btn.btn-sm", {
                                    disabled: this.pagination.offset === 0,
                                    onclick: () => {
                                        this.pagination.offset -= this.pagination.limit;
                                        this.loadJobs();
                                    }
                                }, "Previous"),
                                m("button.join-item.btn.btn-sm", {
                                    disabled: !this.pagination.has_more,
                                    onclick: () => {
                                        this.pagination.offset += this.pagination.limit;
                                        this.loadJobs();
                                    }
                                }, "Next")
                            ])
                        ])
                    ])
        ]);
    }
};

export default JobsPage;
