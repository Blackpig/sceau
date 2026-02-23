---
title: SEO Settings
order: 6
icon: heroicon-o-cog-6-tooth
---

# SEO Settings

Global SEO Settings apply across your entire site and are used primarily to populate **LocalBusiness** and **Organization** structured data — the schema that tells search engines who you are, where you are, and how to contact you.

Find it under **Settings → SEO Settings** in the sidebar.

![The SEO Settings page showing site and business information](/grimoire-asset/sceau/seo-settings.png)

## Site Information

**Site Name** — Your website or brand name. Used in Open Graph tags and schema markup across all pages. Leave empty to use the application name from your Laravel configuration.

**Site URL** — Your site's primary URL (e.g. `https://www.yourdomain.com`). Leave empty to use the application URL from config. This is used as the base URL in schema markup.

## Contact Information

**Telephone** — Your main contact phone number. Include the country code for international formatting, e.g. `+44 20 7946 0958`. Used in LocalBusiness schema.

**Email** — Your contact email address. Used in LocalBusiness schema.

## Physical Address

Your business's physical address. Used to generate LocalBusiness schema, which can trigger enhanced local search results and Google Maps integration.

- **Street Address** — House number and street name
- **City** — Town or city
- **State/Region** — County, state, or region
- **Postal Code** — Post code or ZIP code
- **Country Code** — Two-letter ISO country code, e.g. `GB`, `US`, `FR`, `DE`

> If your business doesn't have a physical public address, you can leave these fields empty. Only fill them in if you want local business schema to be generated.

## Business Information

**Price Range** — An indication of your pricing, expressed with `$` symbols (e.g. `$`, `$$`, `$$$`). Used in LocalBusiness schema and may appear in Google search results for local businesses.

**Opening Hours** — Your business opening hours. Add one entry per time period. For each entry, specify:
- **Day(s) of Week** — Comma-separated days, e.g. `Monday, Tuesday, Wednesday` or `Monday, Wednesday, Friday`
- **Opens** — Opening time in 24-hour format, e.g. `09:00`
- **Closes** — Closing time in 24-hour format, e.g. `17:30`

Add multiple entries for businesses with split hours or different hours on different days.
