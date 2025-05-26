---
layout: default
title: Completed Tasks
---

## ✅ Milestones, Fixes & Finished Work

A broad overview of the core technical work behind this site — from the initial server setup to ongoing configuration, optimization, and custom development. This page will remain a high-level reference and summary, with each major area having its own breakdown page over time.

---

## 🔧 Server & Hosting Setup

- Deployed a Debian Linux VPS using Linode.
- Installed and configured the full LAMP stack (Linux, Apache, MariaDB, PHP).
- Hardened server access with:
  - Cloud firewalls allowing SSH only from personal IP.
  - Unattended upgrades enabled.
  - SSH certificate-based authentication.
  - Disabled root login and created dedicated user account.

---

## 🌐 WordPress Installation & Configuration

- Installed WordPress and configured the base setup.
- Installed a WordPress theme and created a child theme for custom edits.
- Integrated security tools:
  - **Wordfence** for firewall, login protection, and monitoring.
  - **Cloudflare** for DNS/CDN with custom firewall rules to restrict access to admin areas.
- Optimized Apache settings for VPS resource constraints.
- Restricted site access to only Cloudflare and personal IP.

---

## 📦 Backups, Storage & File Management

- Created site backups using remote object storage across two VPS providers.
- Wrote sync systems for automated backups.
- Cleaned up temporary files and old kernels to free space.

---

## 📁 Content Development

- Created posts and gradually migrated them into a **chapter-based system** using **Advanced Custom Fields Pro**.
- Built supporting site content:
  - Site index
  - Biographies
  - Lexicon
  - Plans for tag hubs and dynamic pages

---

## 🛠️ Tools & Automation

- Installed and configured **WP-CLI** for advanced WordPress management.
- Started using **Visual Studio Code** as a coding environment.
- Initialized **Git** to track theme and plugin code changes.

---

## 📱 UI / UX Customization

- Created a child theme and began customizing:
  - CSS tweaks
  - Mobile responsiveness improvements
  - Template overrides and conditional rendering
- Ongoing work to replace Cover Blocks with WebP screenshots for consistency and performance.

---
