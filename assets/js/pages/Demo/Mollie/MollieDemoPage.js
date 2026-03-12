import m from "mithril";
import { Icon } from "../../../components/Icon";
import TestCardInfo from "../../../components/TestCardInfo";
import PaymentsService from "../../../services/PaymentsService";

export default class MollieDemoPage {
    oninit(vnode) {
        this.paymentsService = new PaymentsService();
        this.selectedPlan = 'uk_pro_monthly';
        this.amount = 20.00;
        this.currency = 'GBP';
        this.isLoading = false;
    }

    handleCreatePayment() {
        this.isLoading = true;
        this.paymentsService.createPayment(this.amount, this.currency, 'mollie')
            .then(res => {
                if (res.data.checkout_url) {
                    window.showToast("Redirecting to Mollie Checkout...", "info");
                    setTimeout(() => {
                        window.location.href = res.data.checkout_url;
                    }, 1000);
                } else {
                    window.showToast("Mollie Payment created successfully!", "success");
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
        this.paymentsService.createSubscription(this.selectedPlan, 'mollie')
            .then(res => {
                window.showToast("Mollie subscription request processed", "success");
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
                    "Mollie Sandbox Credentials"
                ]),
                m(".grid.grid-cols-1.md:grid-cols-2.gap-4.mt-4", [
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "API Mode")),
                        m("input.input.input-bordered.input-sm.bg-base-200", { value: "Test Mode", readonly: true })
                    ]),
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "Mollie Dashboard")),
                        m("a.btn.btn-outline.btn-primary.btn-sm", { 
                            href: "https://www.mollie.com/dashboard/developers/api-keys", 
                            target: "_blank" 
                        }, [
                            m(Icon, { icon: "fa-solid fa-external-link" }),
                            " Get API Keys"
                        ])
                    ])
                ]),
                m("p.text-xs.mt-4.opacity-60", "Use your 'test_' prefix keys for sandbox testing. Your API keys are managed in the Mollie Dashboard.")
            ])
        ]);

        const cards = [
            { label: "Visa (Success)", number: "4242 4242 4242 4242" },
            { label: "Visa (Open)", number: "4242 4242 4242 4244" }
        ];

        return m(".container.mx-auto.p-4.py-12.max-w-6xl", [
            m(TestCardInfo, { cards }),
            m(".flex.items-center.gap-4.mb-12", [
                m(m.route.Link, { href: "/demo", class: "btn btn-ghost btn-sm" }, [
                    m(Icon, { icon: "fa-solid fa-arrow-left" }),
                    " Back to Demo"
                ]),
                m("h1.text-4xl.font-bold", "Mollie Integration Demo")
            ]),
            m(".grid.grid-cols-1.lg:grid-cols-2.gap-8", [
                m(".card.bg-base-100.shadow-xl.relative", [
                    this.renderLoadingOverlay(),
                    m(".card-body", [
                        m("h2.card-title", [
                            m(Icon, { icon: "fa-solid fa-credit-card text-primary text-2xl" }),
                            "Mollie Payment Demo"
                        ]),
                        m("p.opacity-70", "Mollie offers a very simple setup for UK businesses with support for all major payment methods."),
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
                            " Pay with Mollie"
                        ])
                    ])
                ]),

                m(".card.bg-base-100.shadow-xl.relative", [
                    this.renderLoadingOverlay(),
                    m(".card-body", [
                        m("h2.card-title", "Mollie Subscriptions"),
                        m("p.opacity-70", "Easily manage recurring payments in the UK and Europe."),
                        m(".divider"),
                        m(".form-control.w-full.max-w-xs.mb-4", [
                            m("label.label", m("span.label-text", "Select Plan")),
                            m("select.select.select-bordered", {
                                onchange: (e) => this.selectedPlan = e.target.value
                            }, [
                                m("option", { value: "uk_pro_monthly" }, "UK Pro ($20/mo)"),
                                m("option", { value: "eu_standard_monthly" }, "EU Standard (€15/mo)")
                            ])
                        ]),
                        m("button.btn.btn-secondary", { 
                            onclick: () => this.handleCreateSubscription(),
                            disabled: this.isLoading
                        }, [
                            m(Icon, { icon: "fa-solid fa-repeat" }),
                            " Subscribe via Mollie"
                        ])
                    ])
                ])
            ]),

            m(".mt-12", credentialsCard),

            m(".mt-12.card.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title", "Why Mollie?"),
                    m("p", "Mollie is renowned for its effortless integration and transparent pricing. It's particularly strong in the UK and European markets."),
                    m(".flex.items-center.gap-3.mt-4", [
                        m(Icon, { icon: "fa-solid fa-bolt text-warning" }),
                        m("span.text-sm", "Ready for Apple Pay, Google Pay, and localized payment methods.")
                    ])
                ])
            ])
        ]);
    }
}
