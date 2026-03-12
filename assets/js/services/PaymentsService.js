import { AuthService } from "./AuthserviceService";

export default class PaymentsService {
    constructor() {
        this.baseUrl = '/api/payments';
        this.subUrl = '/api/subscriptions';
    }

    /**
     * Create a single payment
     */
    createPayment(amount, currency = 'GBP', provider = 'stripe', options = {}) {
        return m.request({
            method: "POST",
            url: this.baseUrl,
            body: { amount, currency, provider, ...options },
            headers: AuthService.getAuthHeaders()
        });
    }

    /**
     * Get user payments
     */
    getPayments() {
        return m.request({
            method: "GET",
            url: this.baseUrl,
            headers: AuthService.getAuthHeaders()
        });
    }

    /**
     * Get available payment providers
     */
    getProviders() {
        return m.request({
            method: "GET",
            url: `${this.baseUrl}/providers`,
            headers: AuthService.getAuthHeaders()
        });
    }

    /**
     * Get PayPal configuration
     */
    getPayPalConfig() {
        return m.request({
            method: "GET",
            url: `${this.baseUrl}/paypal-config`,
            headers: AuthService.getAuthHeaders()
        });
    }

    /**
     * Get Stripe configuration
     */
    getStripeConfig() {
        return m.request({
            method: "GET",
            url: `${this.baseUrl}/stripe-config`,
            headers: AuthService.getAuthHeaders()
        });
    }

    /**
     * Get Square configuration
     */
    getSquareConfig() {
        return m.request({
            method: "GET",
            url: `${this.baseUrl}/square-config`,
            headers: AuthService.getAuthHeaders()
        });
    }

    /**
     * Create a subscription
     */
    createSubscription(plan_id, provider = 'stripe', options = {}) {
        return m.request({
            method: "POST",
            url: this.subUrl,
            body: { plan_id, provider, ...options },
            headers: AuthService.getAuthHeaders()
        });
    }

    /**
     * Get user subscriptions
     */
    getSubscriptions() {
        return m.request({
            method: "GET",
            url: this.subUrl,
            headers: AuthService.getAuthHeaders()
        });
    }

    /**
     * Cancel a subscription
     */
    cancelSubscription(id) {
        return m.request({
            method: "DELETE",
            url: `${this.subUrl}/${id}`,
            headers: AuthService.getAuthHeaders()
        });
    }
}
