import m from "mithril";
import { Icon } from "../../../components/Icon";
import PaymentsService from "../../../services/PaymentsService";

export default class PaceDemoPage {
    oninit(vnode) {
        this.paymentsService = new PaymentsService();
        this.selectedPlan = 'uk_standard_monthly';
        this.amount = 15.00;
        this.currency = 'GBP';
        this.isLoading = false;
    }

    handleCreatePayment() {
        this.isLoading = true;
        this.paymentsService.createPayment(this.amount, this.currency, 'pace')
            .then(res => {
                if (res.data.checkout_url) {
                    window.showToast("Redirecting to Pace Checkout...", "info");
                    setTimeout(() => {
                        window.location.href = res.data.checkout_url;
                    }, 1000);
                } else {
                    window.showToast("Pace Payment created (simulated sandbox)", "success");
                    this.isLoading = false;
                    m.redraw();
                }
            })
            .catch(err => {
                window.showToast(err.response, "error");
                this.isLoading = false;
                m.redraw();
            });
    }

    handleCreateSubscription() {
        this.isLoading = true;
        this.paymentsService.createSubscription(this.selectedPlan, 'pace')
            .then(res => {
                window.showToast("Pace subscription request processed", "success");
                this.isLoading = false;
                m.redraw();
            })
            .catch(err => {
                window.showToast(err.response, "error");
                this.isLoading = false;
                m.redraw();
            });
    }

    view() {
        let loadingOverlay = null;
        if (this.isLoading) {
            loadingOverlay = m(".absolute.inset-0.bg-base-100.bg-opacity-50.flex.justify-center.items-center.z-10", [
                m("span.loading.loading-spinner.loading-lg")
            ]);
        }

        return m(".container.mx-auto.p-4.py-12", [
            m(".flex.items-center.gap-4.mb-12", [
                m(m.route.Link, { href: "/demo", class: "btn btn-ghost btn-sm" }, [
                    m(Icon, { icon: "fa-solid fa-arrow-left" }),
                    " Back to Demo"
                ]),
                m("h1.text-4xl.font-bold", "Pace Integration Demo")
            ]),

            m(".grid.grid-cols-1.lg:grid-cols-2.gap-8", [
                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", [
                            m(Icon, { icon: "fa-solid fa-credit-card text-primary text-2xl" }),
                            "Pace Payment Demo"
                        ]),
                        m("p.opacity-70", "Pace provides efficient payment processing for UK and Southeast Asian markets."),
                        m(".divider"),
                        m(".form-control.w-full.max-w-xs.mb-4", [
                            m("label.label", m("span.label-text", "Amount")),
                            m("input.input.input-bordered", { 
                                type: "number", 
                                value: this.amount,
                                oninput: (e) => this.amount = e.target.value
                            })
                        ]),
                        m("button.btn.btn-primary", { 
                            onclick: () => this.handleCreatePayment(),
                            disabled: this.isLoading
                        }, [
                            m(Icon, { icon: "fa-solid fa-credit-card" }),
                            " Pay with Pace"
                        ])
                    ])
                ]),

                m(".card.bg-base-100.shadow-xl", [
                    m(".card-body", [
                        m("h2.card-title", "Pace Subscriptions"),
                        m("p.opacity-70", "Test recurring payments specifically for the UK market with Pace."),
                        m(".divider"),
                        m(".form-control.w-full.max-w-xs.mb-4", [
                            m("label.label", m("span.label-text", "Select Plan")),
                            m("select.select.select-bordered", {
                                onchange: (e) => this.selectedPlan = e.target.value
                            }, [
                                m("option", { value: "uk_standard_monthly" }, "UK Standard ($15/mo)"),
                                m("option", { value: "sea_premium_monthly" }, "SEA Premium ($25/mo)")
                            ])
                        ]),
                        m("button.btn.btn-secondary", { 
                            onclick: () => this.handleCreateSubscription(),
                            disabled: this.isLoading
                        }, [
                            m(Icon, { icon: "fa-solid fa-repeat" }),
                            " Subscribe via Pace"
                        ])
                    ])
                ])
            ]),

            m(".mt-12.card.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title", "Why Pace?"),
                    m("p", "Pace is a rapidly growing payment provider known for its cost-effective transaction fees and high reliability in its target markets."),
                    m(".flex.items-center.gap-3.mt-4", [
                        m(Icon, { icon: "fa-solid fa-bolt text-warning" }),
                        m("span.text-sm", "Fast settlement and easy-to-use developer dashboard.")
                    ])
                ])
            ])
        ]);
    }
}
