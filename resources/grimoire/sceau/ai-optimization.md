---
title: AI Optimization
order: 5
icon: heroicon-o-sparkles
---

# AI Optimization

The AI Optimization tab helps your content perform better in AI-powered search tools like ChatGPT, Perplexity, Google AI Overviews, and Bing Copilot. These tools weight content freshness heavily — regularly updated content is more likely to be cited as a source.

![The AI Optimization tab showing Content Freshness fields](/grimoire-asset/sceau/ai-optimization.png)

## Why does this matter?

Traditional search engines primarily rank by relevance and authority. AI search tools also consider **content freshness** — how recently the information was verified or updated. A page with an explicit "last updated" signal is more trustworthy to an AI summariser than one with no timestamp.

## Content Last Updated

Set this to the date and time when the page's content was last meaningfully updated. "Meaningfully" means a substantive content change — not a typo fix or minor formatting edit, but an update to facts, figures, prices, or advice.

This outputs a `dateModified` signal that AI systems and search engines can read directly.

**When to update this:**
- After revising product information or pricing
- After updating factual content (statistics, recommendations, how-to steps)
- After a significant rewrite or expansion of the page

## Update Notes

A brief plain-text description of what changed in the last update. This is not output to the frontend — it is a private note for AI context and your own editorial records.

Example entries:
- "Updated pricing for 2025 season"
- "Added new FAQ section on delivery times"
- "Revised recommendations following product line refresh"

Think of it as a one-line changelog for each page.
