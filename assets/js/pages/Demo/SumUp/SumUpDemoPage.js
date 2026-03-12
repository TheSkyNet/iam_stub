import m from "mithril";
import { Icon } from "../../../components/Icon";
import TestCardInfo from "../../../components/TestCardInfo";
import PaymentsService from "../../../services/PaymentsService";

export default class SumUpDemoPage {
    oninit(vnode) {
        this.paymentsService = new PaymentsService();
        this.selectedPlan = 'sumup_starter_monthly';
        this.amount = 10.00;
        this.currency = 'GBP';
        this.isLoading = false;
    }

    handleCreatePayment() {
        this.isLoading = true;
        this.paymentsService.createPayment(this.amount, this.currency, 'sumup')
            .then(res => {
                if (res.data.checkout_url) {
                    window.showToast("Redirecting to SumUp Checkout...", "info");
                    setTimeout(() => {
                        window.location.href = res.data.checkout_url;
                    }, 1000);
                } else {
                    window.showToast("SumUp Checkout created successfully!", "success");
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
        this.paymentsService.createSubscription(this.selectedPlan, 'sumup')
            .then(res => {
                window.showToast("SumUp subscription request processed", "success");
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
        return m(".absolute.inset-0.bg-base-100.bg-opacity-50.flex.justify-center.items-center.z-10", { key: "loading-overlay" }, [
            m("span.loading.loading-spinner.loading-lg")
        ]);
    }

    view() {
        const credentialsCard = m(".card.bg-base-100.shadow-xl.mb-8", [
            m(".card-body", [
                m("h2.card-title.flex.items-center.gap-2", [
                    m(Icon, { icon: "fa-solid fa-credit-card text-primary" }),
                    "SumUp Sandbox Credentials"
                ]),
                m(".grid.grid-cols-1.md:grid-cols-2.gap-4.mt-4", [
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "API Mode")),
                        m("input.input.input-bordered.input-sm.bg-base-200", { value: "OAuth 2.0 / API Key", readonly: true })
                    ]),
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "SumUp Dashboard")),
                        m("a.btn.btn-outline.btn-primary.btn-sm", { 
                            href: "https://me.sumup.com/developers", 
                            target: "_blank" 
                        }, [
                            m(Icon, { icon: "fa-solid fa-external-link" }),
                            " Developer Portal"
                        ])
                    ])
                ]),
                m("p.text-xs.mt-4.opacity-60", "SumUp requires a Client ID and Secret or a personal Access Token.")
            ])
        ]);

        const cards = [
            { label: "Visa (SumUp Test)", number: "4111 1111 1111 1111" },
            { label: "Mastercard", number: "5412 7500 0000 0000" }
        ];

        return m(".container-fluid.p-4.py-12", [
            // TEST CARDS AT THE TOP
            m(TestCardInfo, { cards, key: "test-card-info" }),

            m(".flex.items-center.gap-4.mb-12", { key: "header" }, [
                m(m.route.Link, { href: "/demo", class: "btn btn-ghost btn-sm" }, [
                    m(Icon, { icon: "fa-solid fa-arrow-left" }),
                    " Back to Demo"
                ]),
                m("h1.text-4xl.font-bold", "SumUp Integration Demo")
            ]),

            m(".grid.grid-cols-1.lg:grid-cols-2.gap-8", { key: "main-grid" }, [
                m(".card.bg-base-100.shadow-xl.relative", { key: "payment-card" }, [
                    this.renderLoadingOverlay(),
                    m(".card-body", { key: "card-body" }, [
                        m("h2.card-title", { key: "title" }, [
                            m(Icon, { icon: "fa-solid fa-credit-card text-primary text-2xl" }),
                            "SumUp Payment Demo"
                        ]),
                        m("p.opacity-70", { key: "desc" }, "SumUp is an excellent choice for UK startups and small businesses."),
                        m(".divider", { key: "divider" }),
                        m(".form-control.w-full.max-w-xs.mb-4", { key: "amount-form" }, [
                            m("label.label", { key: "label" }, m("span.label-text", "Amount")),
                            m("div.join", { key: "join" }, [
                                m("input.input.input-bordered.join-item.w-full", { 
                                    key: "input",
                                    type: "number", 
                                    value: this.amount,
                                    oninput: (e) => this.amount = e.target.value
                                }),
                                m("select.select.select-bordered.join-item", {
                                    key: "select",
                                    onchange: (e) => this.currency = e.target.value
                                }, [
                                    m("option", { value: "GBP", key: "gbp" }, "GBP"),
                                    m("option", { value: "EUR", key: "eur" }, "EUR"),
                                    m("option", { value: "USD", key: "usd" }, "USD")
                                ])
                            ])
                        ]),
                        m("button.btn.btn-primary", { 
                            key: "btn",
                            onclick: () => this.handleCreatePayment(),
                            disabled: this.isLoading
                        }, [
                            m(Icon, { icon: "fa-solid fa-credit-card" }),
                            " Pay with SumUp"
                        ])
                    ])
                ]),

                m(".card.bg-base-100.shadow-xl.relative", { key: "subscription-card" }, [
                    this.renderLoadingOverlay(),
                    m(".card-body", { key: "card-body" }, [
                        m("h2.card-title", { key: "title" }, "SumUp Subscriptions"),
                        m("p.opacity-70", { key: "desc" }, "Easily manage recurring payments with SumUp's card vaulting."),
                        m(".divider", { key: "divider" }),
                        m(".form-control.w-full.max-w-xs.mb-4", { key: "plan-form" }, [
                            m("label.label", { key: "label" }, m("span.label-text", "Select Plan")),
                            m("select.select.select-bordered", {
                                key: "select",
                                onchange: (e) => this.selectedPlan = e.target.value
                            }, [
                                m("option", { value: "sumup_starter_monthly", key: "opt1" }, "Starter Monthly (£10/mo)"),
                                m("option", { value: "sumup_business_yearly", key: "opt2" }, "Business Yearly (£100/yr)")
                            ])
                        ]),
                        m("button.btn.btn-secondary", { 
                            key: "btn",
                            onclick: () => this.handleCreateSubscription(),
                            disabled: this.isLoading
                        }, [
                            m(Icon, { icon: "fa-solid fa-repeat" }),
                            " Subscribe via SumUp"
                        ])
                    ])
                ])
            ]),

            m(".mt-12", { key: "creds-section" }, credentialsCard),

            m(".mt-12.card.bg-base-100.shadow-xl", { key: "info-section" }, [
                m(".card-body", [
                    m("h2.card-title", "Why SumUp?"),
                    m("p", "SumUp is known for its transparent pricing and simple hardware. Their online payment APIs are equally straightforward."),
                    m(".flex.items-center.gap-3.mt-4", [
                        m(Icon, { icon: "fa-solid fa-bolt text-warning" }),
                        m("span.text-sm", "Low transaction fees and no fixed monthly costs for many plans.")
                    ])
                ])
            ])
        ]);
    }
}
