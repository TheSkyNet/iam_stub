import m from "mithril";
import { Icon } from "../../../components/Icon";
import TestCardInfo from "../../../components/TestCardInfo";
import PaymentsService from "../../../services/PaymentsService";

export default class StripeDemoPage {
    oninit(vnode) {
        this.paymentsService = new PaymentsService();
        this.selectedPlan = 'premium_monthly';
        this.amount = 25.00;
        this.currency = 'USD';
        this.isLoading = true;
        this.stripeLoaded = false;
        this.stripe = null;
        this.elements = null;
        this.cardElement = null;
        this.publicKey = '';

        this.loadStripeConfig();
    }

    loadStripeConfig() {
        this.paymentsService.getStripeConfig()
            .then(res => {
                this.publicKey = res.data.publicKey;
                this.loadStripeSdk();
            })
            .catch(err => {
                window.showToast("Failed to load Stripe configuration", "error");
                this.isLoading = false;
                m.redraw();
            });
    }

    loadStripeSdk() {
        if (window.Stripe) {
            this.initStripe();
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://js.stripe.com/v3/';
        script.async = true;
        script.onload = () => this.initStripe();
        script.onerror = () => {
            window.showToast("Failed to load Stripe SDK", "error");
            this.isLoading = false;
            m.redraw();
        };
        document.head.appendChild(script);
    }

    initStripe() {
        if (!this.publicKey) {
            this.isLoading = false;
            m.redraw();
            return;
        }
        this.stripe = window.Stripe(this.publicKey);
        this.stripeLoaded = true;
        this.isLoading = false;
        m.redraw();
    }

    mountCardElement(vnode) {
        if (!this.stripeLoaded || !this.stripe) return;

        this.elements = this.stripe.elements();
        this.cardElement = this.elements.create('card', {
            hidePostalCode: true,
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                },
            },
        });
        this.cardElement.mount(vnode.dom);
    }

    handleCreatePayment() {
        if (!this.stripe || !this.cardElement) return;

        this.isLoading = true;
        
        // 1. Create PaymentIntent on backend
        this.paymentsService.createPayment(this.amount, this.currency, 'stripe')
            .then(res => {
                const clientSecret = res.data.client_secret;
                
                // 2. Confirm payment on frontend
                return this.stripe.confirmCardPayment(clientSecret, {
                    payment_method: {
                        card: this.cardElement,
                        billing_details: {
                            name: 'Demo User',
                        },
                    },
                });
            })
            .then(result => {
                if (result.error) {
                    window.showToast(result.error.message, "error");
                } else {
                    if (result.paymentIntent.status === 'succeeded') {
                        window.showToast("Stripe Payment Succeeded!", "success");
                        // 3. Optional: Verify on backend
                        this.paymentsService.createPayment(this.amount, this.currency, 'stripe', {
                            transaction_id: result.paymentIntent.id,
                            status: 'captured'
                        });
                    }
                }
                this.isLoading = false;
                m.redraw();
            })
            .catch(err => {
                window.showToast(err.response || "Payment failed", "error");
                this.isLoading = false;
                m.redraw();
            });
    }

    handleCreateSubscription() {
        this.isLoading = true;
        this.paymentsService.createSubscription(this.selectedPlan, 'stripe')
            .then(res => {
                if (res.data.checkout_url) {
                    window.location.href = res.data.checkout_url;
                } else {
                    window.showToast("Subscription session created", "success");
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

    renderLoadingOverlay() {
        if (!this.isLoading) return null;
        return m(".absolute.inset-0.bg-base-100.bg-opacity-50.flex.justify-center.items-center.z-10", [
            m("span.loading.loading-spinner.loading-lg")
        ]);
    }

    view() {

        const publicKeyCard = m(".card.bg-base-100.shadow-xl.mb-8", [
            m(".card-body", [
                m("h2.card-title.flex.items-center.gap-2", [
                    m(Icon, { icon: "fa-brands fa-stripe text-primary" }),
                    "Stripe Sandbox Credentials"
                ]),
                m(".grid.grid-cols-1.md:grid-cols-2.gap-4.mt-4", [
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "Public Key")),
                        m("input.input.input-bordered.input-sm.bg-base-200", { value: this.publicKey || 'Loading...', readonly: true })
                    ]),
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "Stripe Dashboard")),
                        m("a.btn.btn-outline.btn-primary.btn-sm", { 
                            href: "https://dashboard.stripe.com/test/apikeys", 
                            target: "_blank" 
                        }, [
                            m(Icon, { icon: "fa-solid fa-external-link" }),
                            " Get API Keys"
                        ])
                    ])
                ]),
                m("p.text-xs.mt-4.opacity-60", "Make sure you use your TEST keys for sandbox integration. Your secret key should never be shared with the frontend.")
            ])
        ]);

        const cards = [
            { label: "Visa (Succeeds)", number: "4242 4242 4242 4242" },
            { label: "Visa (3DS)", number: "4000 0000 0000 3106" },
            { label: "Visa (Decline)", number: "4000 0000 0000 0002" }
        ];

        return m(".container.mx-auto.p-4.py-12.max-w-6xl", [
            m(TestCardInfo, { cards }),
            m(".flex.items-center.gap-4.mb-12", [
                m(m.route.Link, { href: "/demo", class: "btn btn-ghost btn-sm" }, [
                    m(Icon, { icon: "fa-solid fa-arrow-left" }),
                    " Back to Demo"
                ]),
                m("h1.text-4xl.font-bold", "Stripe Integration Demo")
            ]),
            m(".grid.grid-cols-1.lg:grid-cols-2.gap-8", [
                m(".card.bg-base-100.shadow-xl.relative", [
                    this.renderLoadingOverlay(),
                    m(".card-body", [
                        m("h2.card-title", [
                            m(Icon, { icon: "fa-brands fa-stripe text-primary text-2xl" }),
                            "Stripe Payment Demo"
                        ]),
                        m("p.opacity-70", "Test Stripe single payments and subscriptions using our real integration."),
                        m(".divider"),
                        m(".form-control.w-full.mb-4", [
                            m("label.label", m("span.label-text", "Card Details")),
                            m("div", [
                                this.stripeLoaded 
                                    ? m(".border.rounded-lg.bg-base-200.p-4", {
                                        key: "stripe-card",
                                        oncreate: (vnode) => this.mountCardElement(vnode)
                                      })
                                    : m(".skeleton.h-14.w-full", { key: "stripe-skeleton" })
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
                            disabled: this.isLoading || !this.stripeLoaded
                        }, [
                            m(Icon, { icon: "fa-solid fa-credit-card" }),
                            " Pay with Stripe"
                        ])
                    ])
                ]),

                m(".card.bg-base-100.shadow-xl.relative", [
                    this.renderLoadingOverlay(),
                    m(".card-body", [
                        m("h2.card-title", "Stripe Subscriptions"),
                        m("p.opacity-70", "Redirect to Stripe Checkout for recurring billing."),
                        m(".divider"),
                        m(".form-control.w-full.max-w-xs.mb-4", [
                            m("label.label", m("span.label-text", "Select Plan")),
                            m("select.select.select-bordered", {
                                onchange: (e) => this.selectedPlan = e.target.value
                            }, [
                                m("option", { value: "premium_monthly" }, "Premium Monthly ($25/mo)"),
                                m("option", { value: "pro_yearly" }, "Pro Yearly ($250/yr)")
                            ])
                        ]),
                        m("button.btn.btn-secondary", { 
                            onclick: () => this.handleCreateSubscription(),
                            disabled: this.isLoading
                        }, [
                            m(Icon, { icon: "fa-solid fa-repeat" }),
                            " Subscribe via Checkout"
                        ])
                    ])
                ])
            ]),

            m(".mt-12", publicKeyCard),

            m(".mt-12.card.bg-base-100.shadow-xl", [
                m(".card-body", [
                    m("h2.card-title", "Stripe Features"),
                    m(".grid.grid-cols-1.md:grid-cols-3.gap-4.mt-4", [
                        m(".flex.items-center.gap-3", [
                            m(Icon, { icon: "fa-solid fa-check-circle text-success" }),
                            m("span", "Stripe Elements")
                        ]),
                        m(".flex.items-center.gap-3", [
                            m(Icon, { icon: "fa-solid fa-check-circle text-success" }),
                            m("span", "Apple Pay / Google Pay")
                        ]),
                        m(".flex.items-center.gap-3", [
                            m(Icon, { icon: "fa-solid fa-check-circle text-success" }),
                            m("span", "3D Secure 2.0")
                        ])
                    ])
                ])
            ])
        ]);
    }
}
