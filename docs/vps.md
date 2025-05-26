---
layout: default
title: Cloud Server Setup
---

## ☁️ VPS Hosting, DNS & Cloud Configuration

My site runs on a $5/month Virtual Private Server (VPS) provided by Linode, now owned by Akamai. Although the physical server hardware is shared with other users, my VPS gives me full control over a virtual instance — including root access to the Linux operating system. I am responsible for everything from OS installation to application-level configuration. Linode simply manages the physical infrastructure.

When provisioning a VPS, you can select the datacenter. I chose a location in New Jersey. For the operating system, I went with **Debian Linux**, a free, open-source distribution known for its stability and minimalism. Debian does not include a graphical interface by default; everything is managed from the command line, which is ideal for servers.

Although Linode offers prebuilt images for things like WordPress, I avoided those in favor of setting everything up manually. Their prebuilt images use Ubuntu, but I wanted to stay with Debian for better control and familiarity. I manually installed the full **LAMP stack** — Linux, Apache, MariaDB, and PHP — which forms the foundation of my WordPress setup.

Security is a critical part of managing a VPS. During the setup, I generated a strong password for the root user using **KeePassXC**, and I also uploaded my **SSH public key** to Linode. This allows me to connect securely to the server via terminal using a private key stored locally, instead of relying on passwords.

Linode’s **cloud firewall** lets you configure access control based on IP address and port, providing a lower-level layer of protection before traffic even reaches the server. I used this to restrict all access to my own IP and to Cloudflare’s network. Backups can also be enabled through Linode’s dashboard — they’re charged hourly and can be useful in certain situations — though I handle most of my backups manually using rsync and object storage across different providers.

Once the server was up, I connected and began hardening the system. I created a limited daily-use user account, disabled SSH access for root, and set up **certificate-based authentication**. I enabled **unattended upgrades** for security patches and performed additional hardening steps to protect the system from brute-force attacks.

Only after all this did I install WordPress. It runs on top of the LAMP stack and is now the front-end for my entire site. I’ve configured it to run efficiently on minimal resources and integrated it with tools like Cloudflare, Wordfence, and object storage to handle backups, caching, and security.

---

This page isn’t a how-to guide — it’s a snapshot of how I personally run and secure my site. If I end up publishing full step-by-step instructions, those will live on my secondary domain, `dev.stationaryreality.com`. This section, and the rest of this GitHub Pages site, is here to document what I’ve actually done — and what’s still ahead.
