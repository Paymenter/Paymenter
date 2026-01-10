# 💳 Mercado Pago Gateway for Paymenter

This is an advanced **Mercado Pago** extension for the **Paymenter** platform. It allows you to process payments automatically and is fully compatible with multiple currencies (ARS, BRL, MXN, etc.) by detecting your site's configuration.

---

```bash

# Install the official Mercado Pago SDK
composer require mercadopago/dx-php

# Apply correct permissions
chown -R www-data:www-data extensions/Gateways/MercadoPago
chmod -R 755 extensions/Gateways/MercadoPago
```

### Webhook (IPN) Setup

To have invoices automatically marked as paid, you must configure the Webhook in your Mercado Pago Developer Panel:

```bash Webhook URL: https://tu-dominio.com/extensions/mercadopago/webhook```

Events: All

Mode: Make sure you are using the corresponding mode (Production or Sandbox).

### Multi-currency Support
Automatic Detection: Reads the currency configured in Settings > General in your Paymenter.

Brazil: If your currency is BRL, the plugin will send the request in Reais and the Checkout will automatically be shown in Portuguese.

Argentina/Mexico/Others: It will work with ARS, MXN, etc., as long as your Mercado Pago account is from the same country as the configured currency.

### Security Requirements
SSL Required: Mercado Pago will not send payment confirmations (Webhooks) to sites that do not have an SSL certificate (https).

PHP: >= 8.1

Paymenter: Compatible with version 1.0.0+ (Extension structure at root).

### License
Distributed under the MIT license. Created by Vefixy.