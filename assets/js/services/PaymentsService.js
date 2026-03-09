export default class PaymentsService {
    constructor() {
        this.baseUrl = '/api/payments';
        this.subUrl = '/api/subscriptions';
    }

    /**
     * Create a single payment
     */
    createPayment(amount, currency = 'USD', provider = 'stripe') {
        return m.request({
            method: "POST",
            url: this.baseUrl,
            body: { amount, currency, provider }
        });
    }

    /**
     * Get user payments
     */
    getPayments() {
        return m.request({
            method: "GET",
            url: this.baseUrl
        });
    }

    /**
     * Get available payment providers
     */
    getProviders() {
        return m.request({
            method: "GET",
            url: `${this.baseUrl}/providers`
        });
    }

    /**
     * Create a subscription
     */
    createSubscription(plan_id, provider = 'stripe') {
        return m.request({
            method: "POST",
            url: this.subUrl,
            body: { plan_id, provider }
        });
    }

    /**
     * Get user subscriptions
     */
    getSubscriptions() {
        return m.request({
            method: "GET",
            url: this.subUrl
        });
    }

    /**
     * Cancel a subscription
     */
    cancelSubscription(id) {
        return m.request({
            method: "DELETE",
            url: `${this.subUrl}/${id}`
        });
    }
}
