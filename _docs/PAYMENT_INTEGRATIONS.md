# Payment Integrations System

The IamLab project features a robust, multi-provider payment and subscription system. It follows a modular architecture using the Factory and Registry patterns, similar to the LMS integration service.

## Architecture Overview

The system is composed of several key layers:

1.  **Integrations Layer** (`IamLab/Service/Payment/Integrations/`): Contains the actual provider implementations. All providers MUST implement the `PaymentIntegrationInterface`.
2.  **Factory Layer** (`IamLab/Service/Payment/Factory/`): Responsible for instantiating the correct provider based on configuration.
3.  **Registry Layer** (`IamLab/Service/Payment/Registry/`): Manages the lifecycle and health status of all enabled integrations.
4.  **Configuration Layer** (`IamLab/Service/Payment/Configuration/`): Manages provider settings from environment variables.
5.  **Service Layer** (`IamLab/Service/Payment/PaymentService.php`): The main business logic layer used by the rest of the application.

## Supported Providers

The system supports the following providers with real sandbox integration capabilities:

-   **Stripe**: Comprehensive support for single payments and subscriptions via Stripe Elements and Checkout.
-   **PayPal**: Support for single payments and subscriptions via the PayPal SDK v6.
-   **Square**: Support for single payments and subscriptions via Square Web Payments SDK.
-   **Pace**: Fast payment provider for UK and Southeast Asia markets with hosted checkout support.
-   **Mollie**: Highly developer-friendly payment provider for UK and Europe with a simple REST API and hosted checkout.

## Configuration

Provider settings are managed via environment variables in the `.env` file.

### Provider Dashboards & API Keys

To get your API keys for testing, visit the following developer dashboards:

- **Stripe**: [Stripe Dashboard - API Keys](https://dashboard.stripe.com/test/apikeys)
- **PayPal**: [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/applications/sandbox)
- **Square**: [Square Developer Dashboard](https://developer.squareup.com/apps)
- **Mollie**: [Mollie Dashboard - API Keys](https://www.mollie.com/dashboard/developers/api-keys)
- **Pace**: [Pace Merchant Dashboard](https://dashboard.pacenow.co/)

### Environment Variables

```env
# Stripe Configuration
STRIPE_ENABLED=true
STRIPE_API_KEY=your_secret_key
STRIPE_PUBLIC_KEY=your_public_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret

# PayPal Configuration
PAYPAL_ENABLED=true
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_CLIENT_SECRET=your_client_secret
PAYPAL_MODE=sandbox # or live

# Square Configuration
SQUARE_ENABLED=true
SQUARE_ACCESS_TOKEN=your_access_token
SQUARE_APPLICATION_ID=your_app_id
SQUARE_LOCATION_ID=your_location_id

# Pace Configuration
PACE_ENABLED=true
PACE_API_KEY=your_api_key
PACE_SECRET=your_secret

# Mollie Configuration
MOLLIE_ENABLED=true
MOLLIE_API_KEY=your_api_key
```

## Usage in PHP

### Using the PaymentService

```php
use IamLab\Service\Payment\PaymentService;

// Initialize the service (defaults to stripe)
$paymentService = new PaymentService('paypal');

// Process a single payment
$payment = $paymentService->processSinglePayment($userId, 29.99, 'USD');

// Create a subscription
$subscription = $paymentService->createSubscription($userId, 'premium-plan');

// Switch provider dynamically
$paymentService->setProvider('square');
$payment = $paymentService->processSinglePayment($userId, 15.00, 'USD');
```

## Adding a New Provider

To add a new payment provider, follow these steps:

1.  **Create Integration Class**: Create a new class in `IamLab/Service/Payment/Integrations/` that implements `PaymentIntegrationInterface`.
2.  **Update ConfigurationManager**: Add the new provider's configuration mapping in `IamLab/Service/Payment/Configuration/ConfigurationManager.php`.
3.  **Update IntegrationFactory**: Add the new provider to the `create` method in `IamLab/Service/Payment/Factory/IntegrationFactory.php`.
4.  **Update Registry**: Add the new provider name to the `getEnabledIntegrations` list in `ConfigurationManager.php` and any relevant loops in `Registry`.
5.  **Update .env.example**: Add the new configuration placeholders for other developers.

## API Endpoints

The system is exposed via the following authenticated API endpoints:

-   `GET /api/payments`: List user's payment history.
-   `POST /api/payments`: Process a single payment (parameters: `amount`, `currency`, `provider`).
-   `GET /api/payments/providers`: List available payment providers.
-   `GET /api/subscriptions`: List user's active subscriptions.
-   `POST /api/subscriptions`: Create a new subscription (parameters: `plan_id`, `provider`).
-   `DELETE /api/subscriptions/{id}`: Cancel an active subscription.

## Frontend Implementation

The frontend uses the `PaymentsService.js` to interact with these APIs. When creating a payment or subscription, you can specify the desired provider:

```javascript
// Single Payment
this.paymentsService.createPayment(50.00, 'USD', 'paypal')
    .then(res => window.showToast("Payment Successful", "success"))
    .catch(err => window.showToast(err.response, "error"));

// Subscription
this.paymentsService.createSubscription('premium-plan', 'stripe')
    .then(res => window.showToast("Subscription Created", "success"))
    .catch(err => window.showToast(err.response, "error"));
```

### Frontend Pages and Demos

- **Payments Management**: `assets/js/pages/Payments/PaymentsPage.js` (User payment/subscription history)
- **PayPal Integration Demo**: `assets/js/pages/Demo/PayPalDemoPage.js` (Complete PayPal SDK integration example)
