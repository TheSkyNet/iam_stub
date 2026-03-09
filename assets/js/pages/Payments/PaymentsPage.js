import PaymentsService from "../../services/PaymentsService";
import { Icon } from "../../components/Icon";

export default class PaymentsPage {
    oninit(vnode) {
        this.paymentsService = new PaymentsService();
        this.payments = [];
        this.subscriptions = [];
        this.providers = [];
        this.selectedProvider = 'stripe';
        this.loading = true;
        this.error = null;

        this.loadData();
    }

    loadData() {
        this.loading = true;
        Promise.all([
            this.paymentsService.getPayments(),
            this.paymentsService.getSubscriptions(),
            this.paymentsService.getProviders()
        ]).then(([paymentsRes, subsRes, providersRes]) => {
            this.payments = paymentsRes.data || [];
            this.subscriptions = subsRes.data || [];
            this.providers = providersRes.data || [];
            if (this.providers.length > 0 && !this.providers.includes(this.selectedProvider)) {
                this.selectedProvider = this.providers[0];
            }
            this.loading = false;
            m.redraw();
        }).catch(err => {
            this.error = err.response || "Failed to load data";
            this.loading = false;
            window.showToast(this.error, "error");
            m.redraw();
        });
    }

    handleCreatePayment() {
        this.paymentsService.createPayment(10.00, 'USD', this.selectedProvider)
            .then(res => {
                window.showToast(`Mock payment created via ${this.selectedProvider}`, "success");
                this.loadData();
            })
            .catch(err => window.showToast(err.response, "error"));
    }

    handleCreateSubscription() {
        this.paymentsService.createSubscription('premium_monthly', this.selectedProvider)
            .then(res => {
                window.showToast(`Mock subscription created via ${this.selectedProvider}`, "success");
                this.loadData();
            })
            .catch(err => window.showToast(err.response, "error"));
    }

    handleCancelSubscription(id) {
        this.paymentsService.cancelSubscription(id)
            .then(res => {
                window.showToast("Subscription canceled", "success");
                this.loadData();
            })
            .catch(err => window.showToast(err.response, "error"));
    }

    view() {
        let loadingSpinner = null;
        if (this.loading) {
            loadingSpinner = m("div", { class: "flex justify-center p-8" }, m("span", { class: "loading loading-spinner loading-lg" }));
        }
        
        const providerSelector = m("div", { class: "flex items-center gap-4 mb-8 p-4 bg-base-100 rounded-lg shadow-sm" }, [
            m("label", { class: "font-semibold" }, "Select Payment Provider:"),
            m("select", { 
                class: "select select-bordered select-sm",
                value: this.selectedProvider,
                onchange: (e) => this.selectedProvider = e.target.value
            }, this.providers.map(p => m("option", { value: p }, p.charAt(0).toUpperCase() + p.slice(1))))
        ]);

        let subscriptionsBody;
        if (this.subscriptions.length > 0) {
            subscriptionsBody = this.subscriptions.map(sub => {
                let statusBadge = m("span", { class: `badge badge-${sub.status === 'active' ? 'success' : 'ghost'}` }, sub.status);
                let action = "-";
                if (sub.status === 'active') {
                    action = m("button", { 
                        class: "btn btn-error btn-xs",
                        onclick: () => this.handleCancelSubscription(sub.id)
                    }, "Cancel");
                }

                return m("tr", [
                    m("td", sub.plan_id),
                    m("td", sub.payment_method),
                    m("td", [statusBadge]),
                    m("td", sub.ends_at),
                    m("td", action)
                ]);
            });
        } else {
            subscriptionsBody = m("tr", m("td", { colspan: 5, class: "text-center opacity-50" }, "No subscriptions found"));
        }

        let paymentsBody;
        if (this.payments.length > 0) {
            paymentsBody = this.payments.map(payment => {
                const statusClass = `badge badge-${payment.status === 'completed' ? 'success' : 'warning'}`;
                return m("tr", [
                    m("td", payment.transaction_id),
                    m("td", payment.payment_method),
                    m("td", `${payment.amount} ${payment.currency}`),
                    m("td", [
                        m("span", { class: statusClass }, payment.status)
                    ]),
                    m("td", payment.created_at)
                ]);
            });
        } else {
            paymentsBody = m("tr", m("td", { colspan: 5, class: "text-center opacity-50" }, "No payment history found"));
        }

        let content = null;
        if (!this.loading) {
            content = m("div", { class: "grid grid-cols-1 md:grid-cols-2 gap-6" }, [
                // Subscriptions Card
                m("div", { class: "card bg-base-100 shadow-xl" }, [
                    m("div", { class: "card-body" }, [
                        m("h2", { class: "card-title flex justify-between" }, [
                            m("span", "Your Subscriptions"),
                            m("button", { class: "btn btn-primary btn-sm", onclick: () => this.handleCreateSubscription() }, [
                                m(Icon, { icon: "fa-solid fa-plus" }),
                                " Mock Subscribe"
                            ])
                        ]),
                        m("div", { class: "overflow-x-auto" }, [
                            m("table", { class: "table table-zebra w-full" }, [
                                m("thead", [
                                    m("tr", [
                                        m("th", "Plan"),
                                        m("th", "Method"),
                                        m("th", "Status"),
                                        m("th", "Ends At"),
                                        m("th", "Actions")
                                    ])
                                ]),
                                m("tbody", subscriptionsBody)
                            ])
                        ])
                    ])
                ]),

                // Payments Card
                m("div", { class: "card bg-base-100 shadow-xl" }, [
                    m("div", { class: "card-body" }, [
                        m("h2", { class: "card-title flex justify-between" }, [
                            m("span", "Payment History"),
                            m("button", { class: "btn btn-outline btn-sm", onclick: () => this.handleCreatePayment() }, "Mock Single Payment")
                        ]),
                        m("div", { class: "overflow-x-auto" }, [
                            m("table", { class: "table table-sm w-full" }, [
                                m("thead", [
                                    m("tr", [
                                        m("th", "ID"),
                                        m("th", "Method"),
                                        m("th", "Amount"),
                                        m("th", "Status"),
                                        m("th", "Date")
                                    ])
                                ]),
                                m("tbody", paymentsBody)
                            ])
                        ])
                    ])
                ])
            ]);
        }

        return m("div", { class: "container mx-auto p-4" }, [
            m("h1", { class: "text-3xl font-bold mb-8 flex items-center gap-3" }, [
                m(Icon, { icon: "fa-solid fa-credit-card text-primary" }),
                "Payments & Subscriptions"
            ]),
            providerSelector,
            loadingSpinner,
            content
        ]);
    }
}
