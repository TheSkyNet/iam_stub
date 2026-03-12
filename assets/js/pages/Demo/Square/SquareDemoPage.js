import m from "mithril";
import { Icon } from "../../../components/Icon";
import TestCardInfo from "../../../components/TestCardInfo";
import PaymentsService from "../../../services/PaymentsService";

export default class SquareDemoPage {
    oninit(vnode) {
        this.paymentsService = new PaymentsService();
        this.selectedPlan = 'business_monthly';
        this.amount = 30.00;
        this.currency = 'GBP';
        this.isLoading = true;
        this.squareLoaded = false;
        this.card = null;
        this.applicationId = '';
        this.locationId = '';

        this.loadSquareConfig();
    }

    loadSquareConfig() {
        this.paymentsService.getSquareConfig()
            .then(res => {
                this.applicationId = res.data.applicationId;
                this.locationId = res.data.locationId;
                this.loadSquareSdk();
            })
            .catch(err => {
                window.showToast("Failed to load Square configuration", "error");
                this.isLoading = false;
                m.redraw();
            });
    }

    loadSquareSdk() {
        if (window.Square) {
            this.initSquare();
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://sandbox.web.squarecdn.com/v1/square.js';
        script.async = true;
        script.onload = () => this.initSquare();
        script.onerror = () => {
            window.showToast("Failed to load Square SDK", "error");
            this.isLoading = false;
            m.redraw();
        };
        document.head.appendChild(script);
    }

    async initSquare() {
        if (!this.applicationId || !window.Square) {
            this.isLoading = false;
            m.redraw();
            return;
        }

        try {
            const payments = window.Square.payments(this.applicationId, this.locationId);
            this.card = await payments.card();
            this.squareLoaded = true;
        } catch (e) {
            console.error('Square initialization failed', e);
        } finally {
            this.isLoading = false;
            m.redraw();
        }
    }

    async mountCardElement(vnode) {
        if (!this.squareLoaded || !this.card) return;
        await this.card.attach(vnode.dom);
    }

    async handleCreatePayment() {
        if (!this.card) return;

        this.isLoading = true;
        m.redraw();

        try {
            const result = await this.card.tokenize();
            if (result.status === 'OK') {
                const token = result.token;
                
                // Send token to backend
                const res = await this.paymentsService.createPayment(this.amount, this.currency, 'square', {
                    source_id: token
                });
                
                window.showToast("Square Payment Successful!", "success");
            } else {
                window.showToast(`Tokenization failed: ${result.errors[0].message}`, "error");
            }
        } catch (e) {
            window.showToast("Payment processing failed", "error");
        } finally {
            this.isLoading = false;
            m.redraw();
        }
    }

    handleCreateSubscription() {
        this.isLoading = true;
        this.paymentsService.createSubscription(this.selectedPlan, 'square')
            .then(res => {
                if (res.data.success === false) {
                    window.showToast(res.data.message || "Failed to create subscription", "warning");
                } else {
                    window.showToast("Square Subscription Request Sent", "success");
                }
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
                    m(Icon, { icon: "fa-brands fa-square text-primary" }),
                    "Square Sandbox Credentials"
                ]),
                m(".grid.grid-cols-1.md:grid-cols-3.gap-4.mt-4", [
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "Application ID")),
                        m("input.input.input-bordered.input-sm.bg-base-200", { value: this.applicationId || 'Loading...', readonly: true })
                    ]),
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "Location ID")),
                        m("input.input.input-bordered.input-sm.bg-base-200", { value: this.locationId || 'Loading...', readonly: true })
                    ]),
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "Square Dashboard")),
                        m("a.btn.btn-outline.btn-primary.btn-sm", { 
                            href: "https://developer.squareup.com/apps", 
                            target: "_blank" 
                        }, [
                            m(Icon, { icon: "fa-solid fa-external-link" }),
                            " Get Credentials"
                        ])
                    ])
                ]),
                m("p.text-xs.mt-4.opacity-60", "Use your Sandbox Application ID and Location ID. Ensure the Access Token is set in your server-side .env file.")
            ])
        ]);

        const cards = [
            { label: "Visa (Square Test)", number: "4111 1111 1111 1111" },
            { label: "Mastercard", number: "5555 5555 5555 4444" }
        ];

        return m(".container-fluid.p-4.py-12", [
            m(TestCardInfo, { cards }),
            m(".flex.items-center.gap-4.mb-12", [
                m(m.route.Link, { href: "/demo", class: "btn btn-ghost btn-sm" }, [
                    m(Icon, { icon: "fa-solid fa-arrow-left" }),
                    " Back to Demo"
                ]),
                m("h1.text-4xl.font-bold", "Square Integration Demo")
            ]),
            m(".grid.grid-cols-1.lg:grid-cols-2.gap-8", [
                m(".card.bg-base-100.shadow-xl.relative", [
                    this.renderLoadingOverlay(),
                    m(".card-body", [
                        m("h2.card-title", [
                            m(Icon, { icon: "fa-brands fa-square text-primary text-2xl" }),
                            "Square Payment Demo"
                        ]),
                        m("p.opacity-70", "Accept payments with Square's modern API and branded fields."),
                        m(".divider"),
                        m(".form-control.w-full.mb-4", [
                            m("label.label", m("span.label-text", "Card Details")),
                            m("div", [
                                this.squareLoaded
                                    ? m("#card-container.border.rounded-lg.bg-base-200.p-4", {
                                        key: "square-card",
                                        oncreate: (vnode) => this.mountCardElement(vnode)
                                      })
                                    : m(".skeleton.h-14.w-full", { key: "square-skeleton" })
                            ])
                        ]),
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
                            disabled: this.isLoading || !this.squareLoaded
                        }, [
                            m(Icon, { icon: "fa-solid fa-credit-card" }),
                            " Pay with Square"
                        ])
                    ])
                ]),

                m(".card.bg-base-100.shadow-xl.relative", [
                    this.renderLoadingOverlay(),
                    m(".card-body", [
                        m("h2.card-title", "Square Subscriptions"),
                        m("p.opacity-70", "Handle recurring billing and customer vaulting with Square."),
                        m(".divider"),
                        m(".form-control.w-full.max-w-xs.mb-4", [
                            m("label.label", m("span.label-text", "Select Plan")),
                            m("select.select.select-bordered", {
                                onchange: (e) => this.selectedPlan = e.target.value
                            }, [
                                m("option", { value: "business_monthly" }, "Business Monthly ($30/mo)"),
                                m("option", { value: "enterprise_yearly" }, "Enterprise Yearly ($300/yr)")
                            ])
                        ]),
                        m("button.btn.btn-secondary", { 
                            onclick: () => this.handleCreateSubscription(),
                            disabled: this.isLoading
                        }, [
                            m(Icon, { icon: "fa-solid fa-repeat" }),
                            " Subscribe via Square"
                        ])
                    ])
                ])
            ]),

            m(".mt-12", credentialsCard),

            m(".mt-12.card.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title", "Square Terminal & Point of Sale"),
                    m("p", "Square also supports physical terminal integrations for in-person payments."),
                    m(".flex.items-center.gap-3.mt-4", [
                        m(Icon, { icon: "fa-solid fa-info-circle text-info" }),
                        m("span.text-sm", "The current integration supports online payments via the Web Payments SDK.")
                    ])
                ])
            ])
        ]);
    }
}
