---
layout: default
title: DNS and Content Delivery
---

## 🌐 DNS and Content Delivery

All servers on the internet are ultimately accessed through IP addresses—unique numbers that identify them on the network. While this is how computers talk to each other, it’s not ideal for humans. That’s why I purchased a domain name, so people could visit my site using something readable instead of a numeric string.

My main domain cost $9 for the first year. I also picked up the `.net` and `.xyz` variants for $6 each. When all three expired, I renewed them for two years, bringing the total to about $120. That might seem like a lot, but for three domains over two years, it’s a reasonable investment—especially if you’re planning to expand or protect your name long term.

Once you own a domain, subdomains like `dev.stationaryreality.com` are completely free to create and behave similarly to full domains. I plan to use this for more technical documentation using NGINX in the future.

When you factor in both the $5/month Linode server and the domain registration cost, my entire setup still averages out to about **$6.41 per month**—not bad for a fully functional website.

### 🧭 How Domain Names Work

A domain name is really just a friendly label that maps to an IP address. When someone types in your URL, their browser asks a DNS (Domain Name System) server what IP that name corresponds to. That’s how it knows where to find your server.

I originally registered my domains through Namecheap. At first, they were the authoritative DNS provider. But since my server is hosted on Linode, I delegated DNS management to Linode by pointing the Namecheap records to Linode’s DNS servers. This let me control everything from Linode’s dashboard.

Later, I moved DNS management to **Cloudflare**, which offers free DNS services and even more powerful features like caching and content delivery. And yes—**it’s free** for small-scale sites like mine.

### 🚀 CDN: Cloudflare as a Worldwide Proxy

Cloudflare doesn’t just handle DNS—it also acts as a **Content Delivery Network (CDN)**. This means users around the world connect to the closest Cloudflare server instead of directly hitting my VPS. That server either serves cached versions of my site or pulls it from the origin server (my Linode VPS) if needed.

This greatly reduces the load on my VPS and speeds up performance for users in different regions. Even better, it acts as a layer of protection. The real IP of my VPS is hidden—only Cloudflare and I can reach it directly. All other traffic has to go through Cloudflare, which I’ve configured with firewall rules and access controls.

I'll talk more about that in my [Security and Hardening](security.md) section.

---
