---
title: Basic SEO
order: 1
icon: heroicon-o-document-text
---

# Basic SEO

The Basic SEO tab contains the core meta tags that search engines use to understand and display your content.

![The Basic SEO tab showing Meta Tags fields](/grimoire-asset/sceau/basic-seo.png)

## Meta Title

The title that appears as the blue link in Google search results. This is the single most important SEO field on the page.

- **Optimal length:** 50–65 characters
- **Maximum:** 70 characters
- The character counter in the top-right of the field turns red as you approach the limit

**Tips:**
- Put your primary keyword near the beginning
- Make it descriptive and specific — avoid generic titles like "Home" or "Page"
- Each page should have a unique title

## Meta Description

A brief summary of the page that may appear beneath the title in search results. Google sometimes rewrites this, but a good description improves click-through rates.

- **Optimal length:** 150–160 characters
- **Maximum:** 160 characters

**Tips:**
- Summarise what the visitor will find on the page
- Include a natural call to action where appropriate
- Don't stuff keywords — write for humans

## Focus Keyword

The primary search term you want this page to rank for. You can enter multiple keywords separated by commas.

This field is for your own reference and planning — it is not output in the page's HTML. Use it to keep track of the keyword strategy for each page.

## Canonical URL

The definitive URL for this page. Leave empty to use the page's actual URL (the default and most common case).

Set a canonical URL when:
- The same content is accessible at multiple URLs (e.g. with and without trailing slash, or with query parameters)
- You want to consolidate duplicate content signals to a preferred URL

This outputs a `<link rel="canonical" href="...">` tag in the page's `<head>`.

## Robots Directive

Controls how search engine crawlers index this page and follow its links. The default is **Index, Follow** which is correct for almost all content.

| Option | When to use |
|--------|-------------|
| **Index, Follow** | Normal pages — index the content and follow links |
| **Index, No Follow** | Index the page but don't pass authority through its links |
| **No Index, Follow** | Exclude from search results but follow links (e.g. thank-you pages) |
| **No Index, No Follow** | Exclude entirely — use for admin-only or private pages |

> Use "No Index" sparingly. Once search engines stop indexing a page it can take time to recover.
