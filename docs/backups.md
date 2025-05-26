---
layout: default
title: Backup Strategies & Recovery
---

## 🛡️ Backup Methods, Scheduling & Recovery

Backups are the most important part of any security strategy. They cut through idealism and confront the harsh reality: you can’t always prevent disaster, but you *can* always prepare for it. And in modern digital life, disasters can happen at multiple levels—even in a first-world context.

---

## 🧱 Layers of Backup: From Physical to Digital

Let’s break backup strategies down into layers, starting from the physical world.

### 🏠 Physical Backups

All your data originates on your personal computer, and selected content is uploaded to your site. But what happens if your local environment fails?

* What if your house burns down?
* What if your backup drive fails during a restore?
* Is two hard drives enough?

Disaster recovery planning means preparing for the *worst-case* scenario—not the most likely one. This mentality separates casual users from professionals. People who think this way don’t end up with total data loss.

---

## ☁️ VPS & Object Storage

### 🔁 Redundancy With VPS Providers

I use **two VPS providers** (Linode and DigitalOcean), both of which offer object storage:

* **Object Storage**: 250GB for \$5/month
* **Tool**: [`rclone`](https://rclone.org/) syncs files to the cloud using `rsync` logic

These backups include not only the live site data but *everything else*—notes, drafts, raw images, video clips, future content, and ideas. My working folder is just as important as the deployed site itself.

---

## 🔌 WordPress-Specific Backups

There are several useful options for backing up your WordPress environment:

### 🔄 Plugin-Based

* **Plugin**: [UpdraftPlus – Backup/Restore (Free)](https://wordpress.org/plugins/updraftplus/)
* Backup: database, uploads, themes, plugins
* Export and sync these backups to your object storage or cloud account

### 🗂️ XML Export

WordPress also allows a built-in export of your site’s configuration and post content as an `.xml` file. This is a lightweight way to preserve your content structure.

---

## 📤 Additional Free Cloud Backup Options

Even free services offer helpful storage for small sites:

* **Google Drive**: 15GB
* **Koofr**: \~10GB
* Both support integration with `rclone` and have native apps

---

## 🔁 Strategy: Layered Rotation

By rotating between multiple backup approaches:

* 📁 External hard drives (x2 or more)
* ☁️ Cloud object storage (Linode + DigitalOcean)
* 🧩 Plugin and XML-based WordPress backups
* 🗃️ Free services like Google Drive & Koofr

...you significantly reduce the risk of *complete* data loss. Even if one layer fails, others remain intact.

---

This is a cornerstone of digital resiliency. When all else fails, your backups won’t.
