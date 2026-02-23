---
title: Open Graph
order: 2
icon: heroicon-o-share
---

# Open Graph

The Open Graph tab controls how your content appears when shared on social platforms that use the OG protocol — primarily Facebook and LinkedIn, but also WhatsApp, Slack, and many others.

![The Open Graph tab showing social sharing fields](/grimoire-asset/sceau/open-graph.png)

## OG Title

The title shown in the social share preview card. If left empty, it falls back to the **Meta Title**.

- **Optimal length:** 40–60 characters for Facebook
- **Maximum:** 95 characters

You might want a different OG title if your meta title is very SEO-focused and you'd prefer something more engaging for social audiences.

## OG Description

The description shown in the social share card. If left empty, it falls back to the **Meta Description**.

- **Optimal length:** 55–200 characters

## Social Image

The image displayed in the share preview. This is one of the most impactful elements — a strong image significantly improves engagement on social platforms.

- **Recommended size:** 1200 × 630 pixels
- **Format:** JPG or PNG
- Used across Facebook, Twitter, LinkedIn, and other platforms

### Use Hero Image

Toggle this on to automatically use the hero block image from the page (if your content uses Atelier page builder blocks with a hero section). This saves you uploading a separate image when the page already has a strong hero visual.

If both "Use Hero Image" and a "Social Image" are set, the uploaded Social Image takes priority.

## OG Type

Describes the type of content being shared. This helps platforms display the preview correctly.

| Type | Use for |
|------|---------|
| **Website** | General pages, landing pages, home page |
| **Article** | Blog posts, news articles, editorial content |
| **Product** | E-commerce product pages |
| **Profile** | Author or person pages |
| **Book** | Book or publication pages |
| **Video** | Pages containing video as primary content |

## Site Name

Your website or brand name. Appears in some share previews as attribution. Falls back to the application name from your Laravel config if left empty.

## Locale

The language and region of the content, in the format `language_REGION` — for example `en_US`, `en_GB`, `fr_FR`, `de_DE`. This helps platforms serve the correct version of your content to users in different regions.
