---
layout: default
title: Content Structure & Taxonomy
---

## Content Structure & Taxonomy

I’ve touched on this in the [image strategy](/image-strategy), but this document explains the deeper realization behind my current taxonomy and the significance of what I’m trying to build.

### 💡 The Spark: Anchor Tags & Cover Blocks

At one point, I discovered the power of using **HTML anchors** on **cover blocks** to act like semantic tags. This allowed me to write a script to extract covers based on specific names. For example, the first cover block on the site is a quote from **Manly P. Hall**, so I used him as my test case while working with ChatGPT.

This is when I realized that even something as simple as a **file name** could be a source of structure. While `manlyphall.jpg` worked, something like `manly-p-hall` was better. But best of all was something like:

```
author-philosopher-manly-p-hall-secret-teachings-of-all-ages-quote.webp
```

This naming convention is **machine-readable**, **searchable**, and **semantically rich** — a small shift that unlocked much bigger possibilities.

### 🧩 Categorization, Automation & Meta Fields

This is why I abandoned several early pages — once I saw the potential here, I couldn’t go back. My [biographies page](/biographies) uses the base code that extracts and organizes blocks like `bio-[person-name]`.

Then, with ChatGPT’s help, I realized I could **repurpose WordPress tag descriptions** as **metadata containers**. This effectively transformed the tag system into something more powerful — a kind of semantic key/value database. This also explains why the [lexicon](/lexicon) broke: it was no longer enough to just describe a term. Each entry could now carry a **data payload** used in rendering dynamic pages.

That’s when I hit a limit: the system I was building had **outgrown WordPress’s defaults**.

### 🔧 Enter ACF Pro (Advanced Custom Fields)

ACF Pro (\$50/year — easily worth it) lets me build **custom fields** for almost any WordPress object: posts, users, taxonomies, media, etc. This allowed me to migrate my core content to **Custom Post Types (CPTs)** — chapters, quotes, books, bios — all with richer internal data.

Now every element on the site can be tagged, categorized, grouped, and used to generate **dynamic context-aware pages**.

---

### 🧭 What I'm Really Building

Not a blog.
Not a static site.
I’m building a **structured knowledge base**, made of interconnected:

* **People**
* **Ideas**
* **Quotes**
* **Books**
* **Songs**
* **Terms**
* **Themes**
* **Relationships**

This is more like:

* A **mini-Wikipedia**
* A **semantic archive**
* A **visual, navigable database**

As ChatGPT put it:

> You're not just organizing content; you're **building a framework** that scales with meaning. Hubs, quotes, lyrics, books, bios — all interconnected. That’s real.

---

### 🛠 Current Phase: Engineering

Right now, I’m not in creative mode — I’m in engineering mode.

There are nearly 30 unpublished posts in draft status, many of which are intended to become public drafts. I’m only beginning to add **book excerpts**, and I haven’t even scratched the surface of **content threading** or **knowledge gap mapping**.

But I know this system is worth building now, because once it’s in place, the creative phase will scale effortlessly. I’ll be able to:

* Auto-generate quote lists by philosopher
* Link definitions to all mentions across the site
* Pull references from books, lyrics, essays
* Auto-build artist, thinker, or concept **hub pages**

---

### 🔁 Dynamic Tags as Contextual Hubs

Currently, clicking a tag shows all chapters tagged with that topic. But once this system is fully in place, **clicking a tag** like `manly-p-hall` will show:

* His **wiki-style bio**
* **Quotes** he’s cited in
* **Books** he wrote or that mention him
* **Relevant chapters**, essays, or themes

And it doesn’t stop there. Tags like `neoplatonism` will pull:

* A definition (via the philosophical dictionary CPT)
* Quotes from any philosopher tagged with it
* Books discussing it (even if not yet cited — they can be indexed anyway)
* Related music lyrics or thematic connections

Some tags will become **hub pages** — top-level aggregators that act like topic portals. Others will simply provide lightweight, dynamic context. Either way, the user can traverse ideas instead of just reading isolated posts.

---

### 🌐 Extending the Model

This system scales beyond philosophers and schools of thought:

* Bands → group lyrics, genres, influences
* Books → link to every referenced quote
* Themes → interconnect music, essays, and visual symbols

The taxonomy system, combined with ACF and CPTs, means every unit of content can function as both **node and metadata**.

---

### 🙏 ChatGPT’s Role

Without ChatGPT, I wouldn’t have conceptualized this system — at least not as quickly or cleanly. It helped me zoom out, see the architecture, and recognize that what I was building was **not just a site**, but a **framework for knowledge**.

---
