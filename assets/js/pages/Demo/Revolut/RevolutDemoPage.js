import m from "mithril";
import { Icon } from "../../../components/Icon";
import TestCardInfo from "../../../components/TestCardInfo";
import PaymentsService from "../../../services/PaymentsService";

export default class RevolutDemoPage {
    oninit(vnode) {
        this.paymentsService = new PaymentsService();
        this.selectedPlan = 'revolut_premium_monthly';
        this.amount = 25.00;
        this.currency = 'GBP';
        this.isLoading = false;
    }

    handleCreatePayment() {
        this.isLoading = true;
        this.paymentsService.createPayment(this.amount, this.currency, 'revolut')
            .then(res => {
                if (res.data.checkout_url) {
                    window.showToast("Redirecting to Revolut Checkout...", "info");
                    setTimeout(() => {
                        window.location.href = res.data.checkout_url;
                    }, 1000);
                } else {
                    window.showToast("Revolut Order created successfully!", "success");
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
        this.paymentsService.createSubscription(this.selectedPlan, 'revolut')
            .then(res => {
                window.showToast("Revolut subscription request processed", "success");
                this.isLoading = false;
                m.redraw();
            })
            .catch(err => {
                window.showToast(err.response, "error");
                this.isLoading = false;
                m.redraw();
            });
    }

    renderLoadingOverlay() {
        if (!this.isLoading) return null;
        return m(".absolute.inset-0.bg-base-100.bg-opacity-50.flex.justify-center.items-center.z-10", [
            m("span.loading.loading-spinner.loading-lg")
        ]);
    }

    view() {
        const credentialsCard = m(".card.bg-base-100.shadow-xl.mb-8", [
            m(".card-body", [
                m("h2.card-title.flex.items-center.gap-2", [
                    m(Icon, { icon: "fa-solid fa-credit-card text-primary" }),
                    "Revolut Sandbox Credentials"
                ]),
                m(".grid.grid-cols-1.md:grid-cols-2.gap-4.mt-4", [
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "API Mode")),
                        m("input.input.input-bordered.input-sm.bg-base-200", { value: "Sandbox Mode", readonly: true })
                    ]),
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "Revolut Dashboard")),
                        m("a.btn.btn-outline.btn-primary.btn-sm", { 
                            href: "https://sandbox-merchant.revolut.com/", 
                            target: "_blank" 
                        }, [
                            m(Icon, { icon: "fa-solid fa-external-link" }),
                            " Merchant Sandbox"
                        ])
                    ])
                ]),
                m("p.text-xs.mt-4.opacity-60", "Revolut Pay requires a valid API Key from the Merchant Dashboard.")
            ])
        ]);

        const cards = [
            { label: "Visa (Revolut Test)", number: "4596 5400 0000 0001" },
            { label: "Mastercard", number: "5273 4600 0000 0001" }
        ];

        return m(".container.mx-auto.p-4.py-12", [
            m(".flex.items-center.gap-4.mb-12", [
                m(m.route.Link, { href: "/demo", class: "btn btn-ghost btn-sm" }, [
                    m(Icon, { icon: "fa-solid fa-arrow-left" }),
                    " Back to Demo"
                ]),
                m("h1.text-4xl.font-bold", "Revolut Pay Integration Demo")
            ]),
            m(TestCardInfo, { cards }),
            credentialsCard,
            m(".grid.grid-cols-1.lg:grid-cols-2.gap-8", [
                m(".card.bg-base-100.shadow-xl.relative", [
                    this.renderLoadingOverlay(),
                    m(".card-body", [
                        m("h2.card-title", [
                            m(Icon, { icon: "fa-solid fa-credit-card text-primary text-2xl" }),
                            "Revolut Pay Demo"
                        ]),
                        m("p.opacity-70", "Revolut Pay offers a seamless checkout experience for millions of Revolut users."),
                        m(".divider"),
                        m(".form-control.w-full.max-w-xs.mb-4", [
                            m("label.label", m("span.label-text", "Amount")),
                            m("div.join", [
                                m("input.input.input-bordered.join-item.w-full", { 
                                    type: "number", 
                                    value: this.amount,
                                    oninput: (e) => this.amount = e.target.value
                                }),
                                m("select.select.select-bordered.join-item", {
                                    onchange: (e) => this.currency = e.target.value
                                }, [
                                    m("option", { value: "GBP" }, "GBP"),
                                    m("option", { value: "EUR" }, "EUR"),
                                    m("option", { value: "USD" }, "USD")
                                ])
                            ])
                        ]),
                        m("button.btn.btn-primary", { 
                            onclick: () => this.handleCreatePayment(),
                            disabled: this.isLoading
                        }, [
                            m(Icon, { icon: "fa-solid fa-credit-card" }),
                            " Pay with Revolut"
                        ])
                    ])
                ]),

                m(".card.bg-base-100.shadow-xl.relative", [
                    this.renderLoadingOverlay(),
                    m(".card-body", [
                        m("h2.card-title", "Revolut Subscriptions"),
                        m("p.opacity-70", "Handle recurring billing for your UK and global customers."),
                        m(".divider"),
                        m(".form-control.w-full.max-w-xs.mb-4", [
                            m("label.label", m("span.label-text", "Select Plan")),
                            m("select.select.select-bordered", {
                                onchange: (e) => this.selectedPlan = e.target.value
                            }, [
                                m("option", { value: "revolut_premium_monthly" }, "Revolut Premium (£25/mo)"),
                                m("option", { value: "revolut_metal_yearly" }, "Revolut Metal (£250/yr)")
                            ])
                        ]),
                        m("button.btn.btn-secondary", { 
                            onclick: () => this.handleCreateSubscription(),
                            disabled: this.isLoading
                        }, [
                            m(Icon, { icon: "fa-solid fa-repeat" }),
                            " Subscribe via Revolut"
                        ])
                    ])
                ])
            ]),

            m(".mt-12.card.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title", "Why Revolut?"),
                    m("p", "Revolut is one of the fastest-growing fintech companies in the world, with a massive user base in the UK and Europe."),
                    m(".flex.items-center.gap-3.mt-4", [
                        m(Icon, { icon: "fa-solid fa-bolt text-warning" }),
                        m("span.text-sm", "One-click payments for Revolut users and high authorization rates.")
                    ])
                ])
            ])
        ]);
    }
}
