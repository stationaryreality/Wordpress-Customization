---
layout: default
title: Performance & Optimization
---

# ⚙️ Performance

My server runs on Linode’s **Nanode** plan — the entry-level \$5/month tier with 1 GB RAM, 1 virtual CPU, and 25 GB storage. While minimal, this setup works well for small sites, especially with the right performance strategies in place. I can scale up anytime if demand increases.

Even with this setup, **Cloudflare’s CDN** lets me stretch performance by caching content across a worldwide network, reducing direct load on my server.

---

## 🔧 Server Software

I’m currently using **Apache**, but I’m exploring a switch to **NGINX**. NGINX tends to be more efficient and lightweight, which is ideal for low-resource environments like mine.

---

## 🖼️ Image Optimization

I’m in the process of converting all images and animated GIFs to **WebP**, a modern format that offers much smaller file sizes with no noticeable quality loss. This improves load times and reduces bandwidth usage significantly.

---

## 🐌 Lazy Loading & Caching

* **Lazy loading** ensures only images visible in the browser window are loaded, rather than everything all at once.
* I use **Cloudflare’s CDN cache**, and I’m also exploring:

  * WordPress caching plugins
  * Browser-level cache headers
  * Potential server-side cache (e.g., object caching)

---

## 🧪 Code Optimization

Tools like **PageSpeed Insights** and **GTmetrix** provide diagnostics and recommendations — such as:

* Reducing render-blocking scripts
* Minifying assets
* Leveraging browser caching
* Deferring non-critical resources

---

## 🖥️ Mobile vs Desktop Strategy

Dynamic elements like **Cover Blocks** in WordPress look great but are heavier to load. I’m considering:

* Converting cover blocks to optimized **WebP** static images for mobile
* Using **ACF Pro** to group and tag image content
* Applying different layouts for desktop vs. mobile as needed

---

## 🛡️ Security = Performance

Security tools like **Wordfence**, **Cloudflare Firewall**, and **Linode’s Cloud Firewall** actually improve performance by:

* Blocking bots and brute-force attacks
* Reducing junk traffic
* Keeping resource usage stable

Before these tools were in place, my server would often crash under bot load. Now, it runs smoothly with fewer interruptions.

---
