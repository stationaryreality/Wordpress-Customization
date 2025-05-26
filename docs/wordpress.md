---
layout: default
title: WordPress Content Management
---

## 📝 Managing WordPress Content, Themes & Plugins

The software I use to manage my website is called **WordPress**. It originally began as a simple blogging platform but has since grown into a powerful content management system (CMS) that supports all kinds of websites—blogs, online stores, membership sites, learning platforms, and more.

As of late 2024, WordPress powers nearly **a quarter** of the top one million websites. It’s **free and open-source**, which means anyone can download and install it—especially if you're running your own server like I do. This is one of the major strengths of using a VPS and Linux: most of the tools you need are free, and WordPress fits right into that ecosystem.

### 🧱 From Blog to Custom Site

A fresh install of WordPress gives you a basic blog layout. It works well out of the box and includes an admin dashboard with clear settings and tools. You can choose from thousands of themes and plugins to customize your site.

I started out using WordPress just like anyone else—writing blog-style posts. But I soon realized that I wanted something more structured. Instead of posts that display in reverse-chronological order (like a normal blog), I wanted narrative **chapters in a specific order**.

To do that, I installed a plugin called **Post Types Order**, which lets me control the post order manually. That change alone helped shape my site into something more like a book or documentation project.

### 🎨 Theme Customization

I use a theme called **Author** by Complete Themes. I chose it because of its clean layout and readability. But I didn’t stop there—I needed to change how navigation worked. By default, it showed "previous post" and "next post," but because I reversed the order of posts using the plugin, I also had to reverse the navigation labels.

At first, I made code changes directly to the Author theme. But I quickly realized this wasn't sustainable. Every time the theme updated, I had to reapply my customizations manually. That’s when I discovered **child themes**.

Child themes are a way to override parts of a WordPress theme without touching the original files. They inherit everything from the parent theme, but any modifications you make in the child theme take priority. This allows me to update the original Author theme safely, while keeping all my changes intact.

### 🧩 Plugins and Extensions

The WordPress ecosystem is full of plugins that let you go far beyond the basics. There are plugins for SEO, image optimization, analytics, security, and layout builders—basically anything you can imagine. And many of them have free tiers that are perfect for small-scale or personal projects.

You can keep things simple or build something extremely custom—and you don't have to write code unless you want to.

### 💻 GitHub and Version Control

Every time I make a change to my theme or WordPress configuration, I push it to my public [GitHub](https://github.com/stationaryreality/Wordpress-Customization) repository. This is both for transparency and version control. If something breaks or I want to undo a change, Git lets me roll back to an earlier version.

In fact, this page you're reading is served as a static HTML file via GitHub Pages, but if you visit my **WordPress Customization** repo, you’ll see all the code and modifications I’ve made to shape my site beyond the defaults.

---

WordPress is a great platform for beginners, but it’s also incredibly powerful once you start exploring what's possible. You can start with a blog—and end up with a fully customized website tailored to exactly what you want.
