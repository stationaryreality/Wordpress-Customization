---
layout: default
title: Security and Hardening
---

## 🔐 Security and Hardening

Something unexpected happened right after I installed Apache and WordPress on my server: my log files lit up with hundreds of strange requests—every single day. These weren’t normal visits; they were automated “hacking” attempts.

I put "hacking" in quotes because these aren’t targeted attacks from individuals. They’re bots scanning the internet for known vulnerabilities—usually old ones—to exploit. It’s unsettling at first, but it’s the reality of running a public-facing server. Your site will be probed constantly.

### 🧰 First Line of Defense

The very first rule of server security is simple: **keep your software up to date**. Most of the scans you’ll see are looking for outdated systems with known vulnerabilities. Keeping WordPress, Apache, PHP, and all your packages updated is the easiest way to avoid common exploits.

Luckily, many Linux distributions come with some basic protections out of the box. For example, **fail2ban** watches your logs and automatically blocks IPs that show suspicious activity.

Later, I experimented with **CrowdSec**, which builds on this idea by sharing data between users. It uses community threat intelligence to help everyone benefit from a larger network of known bad actors. You can also find IP blocklists online and add them manually.

Eventually, I decided to simplify things by installing **Wordfence**, a security plugin built specifically for WordPress. It’s free for most features and adds firewall protection, IP blocking, and scan detection—kind of like antivirus for your site.

### ⚙️ Server-Level Hardening

Beyond WordPress, there are tons of Linux and Apache configuration tweaks that can help harden your server. For example:

- Blocking access to sensitive folders via `.htaccess`
- Restricting or whitelisting IPs
- Disabling directory listings
- Hiding Apache version info

These aren’t silver bullets, but they reduce your exposure. The goal is to make your server a harder target than the next one.

### ☁️ Enter Cloudflare

Once I moved DNS and content delivery to **Cloudflare**, I also took advantage of their security features. Cloudflare sits between visitors and my server, which means they can filter traffic before it even hits my site. The catch is that all traffic seems to come from Cloudflare’s IPs.

This broke Wordfence’s ability to detect malicious visitors, because every visitor looked like a trusted Cloudflare user. Fortunately, there's a fix: you can configure Apache and Wordfence to read the original IP address passed through by Cloudflare. Once that was done, Wordfence could do its job properly again.

Now, Wordfence, Apache, and Cloudflare all work together as layers of defense. Each adds something unique to the stack.

### 🧱 Layered Security Approach

Here's how I break down the different layers of protection:

- **Hardware**: My server runs in Linode’s New Jersey datacenter, which is physically secure. I’m protected against physical loss with a multi-layered backup strategy (which I’ll detail in its own guide).
  
- **Encryption**: One of my future goals is to recreate my server as an **encrypted instance**, a newer feature offered by Linode. It would help protect credentials and sensitive files even in the event of theft or unauthorized access.

- **Operating System**: Linux is naturally secure, especially Debian, thanks to its conservative and reliable update process. It supports:
  - User role separation
  - Audit logs
  - Minimal default services (reduced attack surface)

- **Apache**: Apache adds its own layer of configuration hardening. I’ve blocked sensitive paths and limited access where possible.

- **Firewall**: Linode offers a basic cloud firewall that works great for small projects. It now blocks all traffic **except** Cloudflare and my own IP address, drastically reducing unwanted traffic.

- **WordPress**: Wordfence handles most of the day-to-day application-level security. It also integrates well with the other tools I use.

### 📉 Finding Balance

Security isn’t just about blocking everything. If Wordfence or Cloudflare is too strict, it can lock out **legitimate visitors**—including search engines like Google. That’s why fine-tuning is essential.

### 🔄 Detection and Recovery

Security also means having a plan for when things go wrong. Detection and response matter just as much as prevention. That’s why **backups** are so important—and why they’ll get their own dedicated section soon.

---

Security is never finished. It’s an ongoing process of monitoring, updating, and adapting. But with the right tools and layered strategy, even a small $5/month server can stand up to a surprising amount of abuse.
