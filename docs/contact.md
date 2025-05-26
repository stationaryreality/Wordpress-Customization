---
layout: default
title: Contact Systems - Email & Forms
---

## 📬 Communication Systems: Email, Forms & Routing

Setting up communication on a self-hosted site isn’t as straightforward as it seems. Especially when using a VPS like Linode, where email ports are disabled by default for spam prevention. You must request manual approval to enable them.

---

## ✉️ Sending Email with MailPoet

For basic newsletter and subscriber-based communication, the best free option is:

* **Plugin**: [MailPoet](https://wordpress.org/plugins/mailpoet/)
* **Limit**: Free for up to 500 subscribers
* **Functionality**: Sends newsletters, automatic updates, and manual campaigns

To use MailPoet reliably:

* You’ll need to configure **DNS records** to authorize MailPoet to send mail on behalf of your domain (SPF, DKIM, etc.)
* You can then send from your custom domain email (e.g. `newsletter@yourdomain.com`)

---

## 🛑 The Catch: No Incoming Email

MailPoet is **outgoing only**. You can't receive replies to that same domain without an additional setup.

### ✅ Workaround: Catch-All Email Forwarding with Cloudflare

Luckily, **Cloudflare Email Routing** provides a zero-cost solution:

* Any email sent to your domain (e.g. `newsletter@yourdomain.com`) is **forwarded** to a chosen inbox (like your free **Tuta** account)
* This allows you to monitor responses and reply from a clean, branded inbox

---

## 📥 Contact Without Email Signup

For direct communication from non-subscribers:

### 📋 Fluent Forms

* **Plugin**: [Fluent Forms](https://wordpress.org/plugins/fluentform/)
* **Features**:

  * Simple contact form builder
  * Bot protection with one-click CAPTCHA
  * Messages stored directly in the WordPress admin panel—no email required

You can respond manually through your branded email account, keeping the loop closed without relying on external mail hosting.

---

## 💡 Zero-Cost Communication Stack

With this setup, you can:

* Send newsletters for free (MailPoet)
* Receive email replies via forwarding (Cloudflare + Tuta)
* Collect direct contact messages without mail (Fluent Forms)

💸 **Total cost: \$0/month**

Even if you grow beyond 500 subscribers, upgrading MailPoet is a small and reasonable step—not a blocker for growth.
