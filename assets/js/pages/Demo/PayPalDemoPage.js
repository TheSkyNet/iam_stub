import m from "mithril";
import { Icon } from "../../components/Icon";
import PaymentsService from "../../services/PaymentsService";

export default class PayPalDemoPage {
    oninit(vnode) {
        this.paymentsService = new PaymentsService();
        this.selectedPlan = 'premium_monthly';
        this.amount = 10.00;
        this.currency = 'USD';
        this.isLoading = true;
        this.sdkLoaded = false;
        
        this.credentials = {
            clientId: '',
            mode: 'sandbox'
        };

        this.loadPayPalConfig();
    }

    loadPayPalConfig() {
        this.paymentsService.getPayPalConfig()
            .then(res => {
                this.credentials.clientId = res.data.clientId;
                this.credentials.mode = res.data.mode;
                this.loadPayPalSdk();
            })
            .catch(err => {
                window.showToast("Failed to load PayPal configuration from server", "error");
                this.isLoading = false;
                m.redraw();
            });
    }

    loadPayPalSdk() {
        if (window.paypal && window.paypal.createInstance) {
            this.sdkLoaded = true;
            this.isLoading = false;
            return;
        }

        const script = document.createElement('script');
        const baseUrl = this.credentials.mode === 'sandbox' 
            ? 'https://www.sandbox.paypal.com' 
            : 'https://www.paypal.com';
            
        script.src = `${baseUrl}/web-sdk/v6/core`;
        script.async = true;
        script.onload = () => {
            this.sdkLoaded = true;
            this.isLoading = false;
            m.redraw();
        };
        script.onerror = (err) => {
            window.showToast("Failed to load PayPal SDK v6 core. Check your internet connection.", "error");
            console.error('PayPal SDK Load Error:', err);
            this.isLoading = false;
            m.redraw();
        };
        document.head.appendChild(script);
    }

    async initPayPalV6(vnode) {
        if (!this.sdkLoaded || !window.paypal || !window.paypal.createInstance) {
            return;
        }

        try {
            // Create PayPal SDK instance
            const sdkInstance = await window.paypal.createInstance({
                clientId: this.credentials.clientId,
                components: ["paypal-payments"],
                pageType: "checkout",
            });

            // Check eligibility
            const paymentMethods = await sdkInstance.findEligibleMethods({
                currencyCode: this.currency,
            });

            if (paymentMethods.isEligible("paypal")) {
                const paypalPaymentSession = sdkInstance.createPayPalOneTimePaymentSession({
                    onApprove: async (data) => {
                        console.log("Payment approved:", data);
                        this.isLoading = true;
                        m.redraw();
                        
                        try {
                            // Call backend to record/capture
                            const res = await this.paymentsService.createPayment(this.amount, this.currency, 'paypal', {
                                paypal_order_id: data.orderId,
                                status: 'captured'
                            });
                            window.showToast("Payment successfully captured and recorded!", "success");
                        } catch (error) {
                            window.showToast("Failed to record payment on server", "error");
                        } finally {
                            this.isLoading = false;
                            m.redraw();
                        }
                    },
                    onCancel: () => {
                        window.showToast("Payment cancelled", "info");
                    },
                    onError: (error) => {
                        window.showToast("PayPal SDK Error", "error");
                        console.error("PayPal Error:", error);
                    }
                });

                const paypalButton = vnode.dom;
                paypalButton.removeAttribute("hidden");

                paypalButton.onclick = async () => {
                    try {
                        // In v6, .start() expects a Promise for createOrder, not a function
                        const createOrderPromise = (async () => {
                            const res = await this.paymentsService.createPayment(this.amount, this.currency, 'paypal', {
                                intent: 'create_only'
                            });
                            return { orderId: res.data.transaction_id };
                        })();

                        await paypalPaymentSession.start(
                            { presentationMode: "auto" },
                            createOrderPromise
                        );
                    } catch (error) {
                        console.error("PayPal payment start error:", error);
                    }
                };
            }
        } catch (error) {
            console.error("SDK initialization error:", error);
        }
    }

    renderPayPalSubscriptionButtons(vnode) {
        // For subscriptions, we might need to reload SDK with vault=true
        // But for now let's focus on single payments as requested "it not opwin the paypall flow"
    }

    handleCreatePayment() {
        this.isLoading = true;
        this.paymentsService.createPayment(this.amount, this.currency, 'paypal')
            .then(res => {
                window.showToast("Mock PayPal payment created successfully", "success");
                this.isLoading = false;
                m.redraw();
            })
            .catch(err => {
                window.showToast(err.response, "error");
                this.isLoading = false;
                m.redraw();
            });
    }

    handleCreateSubscription() {
        this.isLoading = true;
        this.paymentsService.createSubscription(this.selectedPlan, 'paypal')
            .then(res => {
                window.showToast("Mock PayPal subscription created successfully", "success");
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

        const clientIdValue = this.credentials.clientId || 'Loading...';
        
        const credentialsCard = m(".card.bg-base-100.shadow-xl.mb-8", [
            m(".card-body", [
                m("h2.card-title.flex.items-center.gap-2", [
                    m(Icon, { icon: "fa-brands fa-paypal text-info" }),
                    "PayPal Sandbox Credentials"
                ]),
                m(".grid.grid-cols-1.md:grid-cols-2.gap-4.mt-4", [
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "Client ID")),
                        m("input.input.input-bordered.input-sm.bg-base-200", { value: clientIdValue, readonly: true })
                    ]),
                    m(".form-control", [
                        m("label.label", m("span.label-text.font-bold", "Mode")),
                        m("input.input.input-bordered.input-sm.bg-base-200", { value: this.credentials.mode, readonly: true })
                    ]),
                    m(".form-control.md:col-span-2", [
                        m("label.label", m("span.label-text.font-bold", "Sandbox URL (if applicable)")),
                        m("a.link.link-primary.text-sm", { href: "https://sandbox.paypal.com", target: "_blank" }, "https://sandbox.paypal.com")
                    ])
                ])
            ])
        ]);

        let paypalButtonSection = null;
        if (this.sdkLoaded) {
            paypalButtonSection = m("paypal-button", { 
                hidden: true,
                style: "display: block; cursor: pointer;",
                oncreate: (vnode) => this.initPayPalV6(vnode)
            });
        } else {
            paypalButtonSection = m("div.flex.flex-col.items-center.gap-2", [
                m("span.loading.loading-dots.loading-md"),
                m("span.text-xs.opacity-50", "Loading PayPal SDK...")
            ]);
        }

        const singlePaymentSection = m(".flex.flex-col.gap-4", [
            m("h3.font-bold", "Single Payment"),
            m(".form-control", [
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
                        m("option", { value: "USD" }, "USD"),
                        m("option", { value: "EUR" }, "EUR"),
                        m("option", { value: "GBP" }, "GBP")
                    ])
                ])
            ]),
            paypalButtonSection,
            m(".divider", "OR (Mock Legacy)"),
            m("button.btn.btn-outline.btn-info.btn-sm", { onclick: () => this.handleCreatePayment() }, [
                m(Icon, { icon: "fa-solid fa-money-bill-1" }),
                " Mock PayPal Payment"
            ])
        ]);

        const subscriptionSection = m(".flex.flex-col.gap-4", [
            m("h3.font-bold", "Subscription"),
            m(".form-control", [
                m("label.label", m("span.label-text", "Select Plan")),
                m("select.select.select-bordered.w-full", {
                    onchange: (e) => this.selectedPlan = e.target.value
                }, [
                    m("option", { value: "premium_monthly" }, "Premium Monthly ($10/mo)"),
                    m("option", { value: "premium_yearly" }, "Premium Yearly ($100/yr)"),
                    m("option", { value: "enterprise" }, "Enterprise ($500/mo)")
                ])
            ]),
            m("button.btn.btn-primary", { onclick: () => this.handleCreateSubscription() }, [
                m(Icon, { icon: "fa-solid fa-repeat" }),
                " Mock PayPal Subscribe"
            ])
        ]);

        const actionsCard = m(".card.bg-base-100.shadow-xl.relative", [
            loadingOverlay,
            m(".card-body", [
                m("h2.card-title", "Demo Actions"),
                m(".grid.grid-cols-1.md:grid-cols-2.gap-8.mt-4", [
                    singlePaymentSection,
                    subscriptionSection
                ])
            ])
        ]);

        const featuresInfo = m(".mt-8.grid.grid-cols-1.md:grid-cols-3.gap-6", [
            m(".alert.alert-info.shadow-lg", [
                m(Icon, { icon: "fa-solid fa-shield-halved" }),
                m("div", [
                    m("h3.font-bold", "3D Secure"),
                    m("div.text-xs", "Accept 3D Secure payments and present branded fields.")
                ])
            ]),
            m(".alert.alert-success.shadow-lg", [
                m(Icon, { icon: "fa-solid fa-mobile-screen-button" }),
                m("div", [
                    m("h3.font-bold", "Apple & Google Pay"),
                    m("div.text-xs", "Enable mobile wallets for quicker checkouts.")
                ])
            ]),
            m(".alert.alert-warning.shadow-lg", [
                m(Icon, { icon: "fa-solid fa-bolt" }),
                m("div", [
                    m("h3.font-bold", "Fastlane"),
                    m("div.text-xs", "Pre-populate card and shipping data for members.")
                ])
            ])
        ]);

        return m(".container.mx-auto.p-4.py-12", [
            m(".flex.items-center.gap-4.mb-12", [
                m(m.route.Link, { href: "/demo", class: "btn btn-ghost btn-sm" }, [
                    m(Icon, { icon: "fa-solid fa-arrow-left" }),
                    " Back to Demo"
                ]),
                m("h1.text-4xl.font-bold", "PayPal Integration Demo")
            ]),
            credentialsCard,
            actionsCard,
            featuresInfo
        ]);
    }
}
