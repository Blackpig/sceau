---
title: Schema Markup
order: 4
icon: heroicon-o-code-bracket
---

# Schema Markup

Good news — for most pages, **all you need to do is choose a Schema Type and Sceau does the rest**. It will automatically generate the correct structured data and populate all the required values from your page content, model data, and Sceau settings.

![The Schema Markup tab showing the Schema Type selector](/grimoire-asset/sceau/schema-markup.png)

## What is Schema Markup?

Schema markup is a way of telling search engines not just what your page *says*, but what it *means*. When Google understands your content better, it can display **rich results** — enhanced search listings that stand out and attract more clicks.

For example:
- A recipe page can show cooking time and star ratings directly in Google search results
- An FAQ page can display expandable questions without the user even visiting your site
- An event page can show dates and location at a glance
- A product page can show price and availability

You don't need to understand the technical details — that's Sceau's job.

## Step 1 — Choose a Schema Type

Select the type that best describes what this page is about. That's the only decision you need to make.

| Type | Use for |
|------|---------|
| **Article** | General editorial or informational content |
| **Blog Post** | Blog entries and opinion pieces |
| **News Article** | Time-sensitive news content |
| **Product** | E-commerce product pages |
| **Local Business** | Pages describing a physical business location |
| **Organization** | Company or brand overview pages |
| **Person** | Author profiles or individual people pages |
| **Event** | Events with dates, times, and locations |
| **FAQ Page** | Pages organised as questions and answers |
| **How To** | Step-by-step guides and tutorials |
| **Recipe** | Food and cooking content |
| **Video** | Pages where video is the primary content |

Once you select a type and save, Sceau automatically generates the correct structured data and outputs it in your page's `<head>`. It pulls in all the relevant values — page title, description, images, dates, and more — without any further input from you.

**That's it. You're done.**

---

## Sceau + Atelier Blocks

If your site uses the **Atelier Blocks** package, schema markup gets even smarter. Blocks on your page can contribute additional structured data automatically alongside your page schema.

For example, if a page contains an FAQ block, Sceau and Atelier will together ensure the correct `FAQPage` JSON-LD is added — meaning those questions can appear as expandable results directly in Google, with no extra setup required on your part.

---

## ⚠️ Advanced only: Insert Schema

> **Only use this if you are confident working with JSON-LD and know exactly what you need to change. If you're unsure, leave this section alone — Sceau's automatic output is correct.**

The **Insert Schema** button places a raw JSON template into an editor, giving you direct control over the structured data output. This is an escape hatch for situations where you need to supply values that Sceau cannot determine automatically — for example, highly specific product attributes or custom schema types.

**Be aware:**
- The JSON editor **completely overrides** Sceau's automatic output for this page
- Errors in the JSON can silently break your rich results — Google may stop showing them with no warning
- The `@context` and `@type` fields at the top must not be changed
- If you clear the editor and leave it empty, Sceau will resume generating the schema automatically

If you do edit the JSON, Google's [Rich Results Test](https://search.google.com/test/rich-results) lets you validate your markup and preview how rich results will appear before publishing.
