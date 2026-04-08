=== Express Pay Payment Module ===
Contributors: express-pay
Tags: express pay, payment, ERIP, bank cards, e-pos, payment gateway
Requires at least: 4.0
Tested up to: 6.4
Requires PHP: 5.6
Stable tag: 1.3.0
License: Proprietary
License URI: https://express-pay.by/

WordPress plugin for accepting payments through Express Pay service via ERIP, bank cards and E-POS.

== Description ==

Express Pay Payment Module is a WordPress plugin that integrates your website with Express Pay service, allowing you to accept payments through ERIP, bank cards, and E-POS methods.

The plugin adds a payment form to your site using a shortcode and provides payment method settings and invoice management through the WordPress admin panel in the **Express Payments** menu.

= Features =

* Add multiple payment methods in admin panel (ERIP, card, E-POS types)
* Display payment form on site using shortcode with customizable amount and payment description
* Generate invoice parameters and signatures (HMAC-SHA1) for Express Pay API v1
* Test mode support using sandbox environment and test credentials
* Incoming notification signature verification
* Allow customers to modify name, address, and amount during payment (optional)
* Send notifications to customers via email/SMS (optional)
* Display QR code for ERIP/E-POS payments
* Invoice list and payment status management in admin panel
* Support for multiple payment methods on one site

= Database Tables =

The plugin creates and uses the following WordPress database tables:
* `{$wpdb->prefix}expresspay_options` — payment methods and their parameters
* `{$wpdb->prefix}expresspay_invoices` — created invoices and their statuses

= Notification Endpoint =

For receiving payment notifications, the plugin uses WordPress AJAX endpoint:
`/wp-admin/admin-ajax.php?action=receive_notification&type_id=<METHOD_ID>`

== Installation ==

1. Download the plugin files
2. Copy the plugin directory to `wp-content/plugins/`
3. In WordPress admin panel, go to **Plugins** and activate **Express Pay Payment Module**
4. After activation, the plugin will create necessary database tables
5. Verify installation by checking for **Express Payments** menu in the admin panel

== Configuration ==

1. Go to WordPress admin panel → **Express Payments** → **Settings**
2. Add a new payment method by clicking "Add Payment Method"
3. Configure the following parameters:

= General Settings =
* **Name** — Display name of payment method (e.g., "ExpressPay (ERIP)")
* **Type** — Payment method type: `erip`, `card`, or `epos`
* **Token** — API access token from Express Pay service
* **Service ID** — Service number in Express Pay system
* **Secret Word** — Secret key for signing payment requests
* **Secret Word for Notification** — Secret key for verifying incoming notifications

= API URLs =
* **API URL** — Production API base URL (e.g., `https://api.express-pay.by/v1/`)
* **Sandbox URL** — Sandbox API base URL (e.g., `https://sandbox-api.express-pay.by/v1/`)
* **Test Mode** — Enable/disable sandbox mode for testing

= Payment Options =
* **ERIP Path** — Path in ERIP tree shown to users
* **Show QR Code** — Display QR code for ERIP/E-POS payments
* **Use Signature for Notification** — Verify incoming notification signatures
* **Can Change Name** — Allow customers to modify name during payment
* **Can Change Address** — Allow customers to modify address during payment
* **Can Change Amount** — Allow customers to modify amount during payment
* **Send Email** — Send payment notifications via email
* **Send SMS** — Send payment notifications via SMS

= E-POS Settings =
* **Service Provider Code** — E-POS provider code
* **Service E-POS Code** — E-POS service code

== Usage ==

= Adding Payment Form to Page =

Create or edit a page and add the payment form shortcode:

`[expresspay_payment amount=25.5 edit_amount=true info="Payment description"]`

= Shortcode Parameters =
* **amount** — Payment amount (e.g., `25.5`)
* **edit_amount** — Allow customer to change amount (e.g., `true` or `false`)
* **info** — Payment description/purpose
* **method_id** — Specific payment method ID (optional, shows all if not specified)

= Payment Flow =

1. Customer visits page with payment form
2. Customer selects payment method and enters details
3. Customer submits payment
4. Plugin generates invoice parameters and signature
5. Customer is redirected to Express Pay service for payment
6. After successful payment, customer is returned to the page
7. Plugin verifies payment signature and marks invoice as paid
8. Express Pay server sends additional notification webhook

= Testing in Sandbox Mode =

1. Enable **Test Mode** in payment method settings
2. Verify that **Sandbox URL** is being used
3. Complete test payment through Express Pay sandbox environment

== Requirements ==

* WordPress 4.0 or higher
* PHP 5.6 or higher
* Outgoing HTTPS access to Express Pay API:
  - Production: `https://api.express-pay.by/v1/`
  - Sandbox: `https://sandbox-api.express-pay.by/v1/`
* Public URL accessibility for receiving webhooks (if site is behind firewall or requires authentication, webhooks will not be delivered)

== Support ==

For installation and configuration instructions, visit:
[Express Pay CMS Extensions](https://express-pay.by/cms-extensions/wordpress)

Watch video tutorials on our YouTube channel:
[Express Pay YouTube](https://www.youtube.com/c/express-pay-by)

== Frequently Asked Questions ==

= How do I add the payment form to my page? =

Use the shortcode `[expresspay_payment amount=25.5 info="Payment description"]` on any page or post. You can customize the amount and description.

= How do I test the payment system? =

Enable "Test Mode" in the payment method settings. This will use the sandbox environment for testing. You can complete test payments through the Express Pay sandbox.

= What payment methods are supported? =

The plugin supports:
* ERIP (Belarusian electronic payment system)
* Bank cards (Visa, MasterCard)
* E-POS (electronic payment point of sale)

= Are notifications mandatory? =

No, but they are recommended. Email and SMS notifications can be enabled/disabled separately in payment method settings.

= What do I do if payments aren't being received? =

Check the following:
1. Payment method is enabled in **Express Payments** → **Settings**
2. Correct API Token and Service ID are configured
3. Webhook URL is publicly accessible (not behind firewall or authentication)
4. Secret keys match the Express Pay service configuration

== Screenshots ==

1. Payment methods management page
2. Payment form with method selection
3. Invoice list and status management
4. QR code display during payment

== Changelog ==

= 1.3.0 =
* WordPress plugin for Express Pay service
* Multiple payment methods support (ERIP, cards, E-POS)
* Payment form shortcode
* Invoice management
* Webhook notifications
* Test mode support
* QR code display
* Email and SMS notifications support

== License ==

This plugin code and documentation is proprietary and provided by Express Pay service.
All rights reserved. Please refer to LICENSE file in plugin directory for details.

== Third-Party Services ==

This plugin connects to Express Pay service for payment processing:
* Service URL: https://express-pay.by/
* API Documentation: https://express-pay.by/docs/api/v1
* Privacy Policy: https://express-pay.by/docs

By using this plugin, you agree to Express Pay Terms of Service.
