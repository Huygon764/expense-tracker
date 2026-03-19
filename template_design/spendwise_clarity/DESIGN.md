# Design System Specification: Editorial Precision

## 1. Overview & Creative North Star
**The Creative North Star: "The Financial Curator"**

This design system rejects the "SaaS-template" aesthetic in favor of a high-end, editorial experience. While the application handles personal expenses, the visual language should feel like a premium financial journal—spacious, authoritative, and calm. 

We break the traditional rigid grid through **Intentional Asymmetry**. By utilizing significant whitespace (the "Breath" principle) and contrasting a high-scale display typeface (Manrope) against a functional utility face (Inter), we create a hierarchy that feels curated rather than automated. We move away from structural lines, using "Tonal Layering" to define boundaries, ensuring the UI feels like a series of sophisticated, physical surfaces stacked in a well-lit space.

---

## 2. Colors & Surface Philosophy
The palette transitions from utilitarian "expense tracking" to "wealth management" through the use of deep blues and nuanced neutrals.

*   **Primary Core:** `primary` (#0058be) provides an authoritative anchor, while `primary_container` (#2170e4) offers a more vibrant, digital-first energy for active states.
*   **The "No-Line" Rule:** To maintain a premium feel, **1px solid borders are strictly prohibited** for sectioning. Separation must be achieved through background shifts. For example, a `surface_container_lowest` card should sit on a `surface_container_low` section. The contrast is felt, not seen as a "line."
*   **Surface Hierarchy & Nesting:** Use the `surface_container` tiers to create depth. 
    *   **Page Background:** `surface` (#f8f9fa).
    *   **Secondary Content Areas:** `surface_container` (#edeeef).
    *   **Primary Action Cards:** `surface_container_lowest` (#ffffff).
*   **The "Glass & Gradient" Rule:** For floating navigation or modal overlays, use a backdrop-blur (12px–20px) combined with `surface` at 80% opacity. For primary CTAs, apply a subtle linear gradient from `primary` to `primary_container` at a 135-degree angle to add "soul" to the interactive elements.

---

## 3. Typography: The Editorial Scale
We use a dual-font strategy to balance character with readability.

*   **Display & Headlines (Manrope):** Used for data visualization titles and large currency balances. It is a modern, geometric sans-serif that feels expensive.
    *   *Example:* `display-lg` (3.5rem) for the total monthly balance.
*   **Interface & Body (Inter):** Used for all functional data, labels, and paragraph text. Inter provides maximum legibility at small sizes for transaction lists.
    *   *Hierarchy Tip:* Use `label-md` in all caps with `0.05em` letter spacing for category headers (e.g., "GROCERIES") to evoke a refined, archival feel.

---

## 4. Elevation & Depth
In this system, depth is a product of light and layering, not heavy dropshadows.

*   **The Layering Principle:** Depth is achieved by stacking `surface_container` tiers. An inner budget detail card (`surface_container_lowest`) should sit on a dashboard section (`surface_container_low`), which sits on the global background (`surface`).
*   **Ambient Shadows:** For elevated elements (like "Add Expense" FABs or Modals), use a "Long-Tail Shadow":
    *   `box-shadow: 0 20px 40px rgba(25, 28, 29, 0.04), 0 8px 16px rgba(25, 28, 29, 0.02);`
    *   The color must be a tinted version of `on_surface`, never pure black.
*   **The Ghost Border:** If a boundary is required for accessibility in input fields, use `outline_variant` at 15% opacity. High-contrast borders are forbidden as they "trap" the eye and break the flow of whitespace.

---

## 5. Components

### Cards & Transaction Lists
*   **Forbid Divider Lines:** Never use a horizontal rule to separate transactions. Use `spacing-3` (1rem) of vertical whitespace or an alternating background shift between `surface_container_lowest` and `surface_container_low`.
*   **Rounding:** Apply `rounded-md` (0.75rem) to standard cards. Apply `rounded-xl` (1.5rem) to large hero dashboard elements to soften the visual impact.

### Buttons & Interaction
*   **Primary:** A gradient-filled container (`primary` to `primary_container`) with `on_primary` text. No border.
*   **Secondary/Tertiary:** Use `surface_container_high` with `on_surface` text. The interaction state should shift the background to `surface_dim`.
*   **Chips:** Use for transaction categories. They should be `rounded-full` (9999px) using `secondary_fixed` with `on_secondary_fixed_variant` text for a subtle, professional tone.

### Input Fields
*   **Style:** Minimalist. No bottom line, no full box. Use a `surface_container_low` background with a `rounded-sm` (0.25rem) corner. The label (`label-md`) should always sit 4px above the input area, never inside as a placeholder.

### Dashboard Gauges
*   **Success (Under Budget):** Use `tertiary` (#924700) or a soft green if strictly required, but prefer the tertiary amber/gold tones for a more sophisticated "wealth" feel.
*   **Warning (Over Budget):** Use `error` (#ba1a1a) with a `error_container` background for high-visibility alerts.

---

## 6. Do’s and Don’ts

### Do:
*   **Do** use `spacing-12` (4rem) and `spacing-16` (5.5rem) to separate major dashboard sections. Whitespace is a functional element, not "wasted" space.
*   **Do** use `surface_bright` to highlight active navigation states or "New" badges.
*   **Do** ensure all typography maintains a minimum contrast ratio of 4.5:1 against its respective surface container.

### Don't:
*   **Don't** use 1px borders or `hr` tags. If you feel the need for a line, increase the spacing or change the background tone instead.
*   **Don't** use standard "drop shadow" presets. Shadows must feel like ambient light, not a "glow" or a "shelf."
*   **Don't** use pure black (#000000) for text. Always use `on_surface` (#191c1d) to maintain a soft, premium ink-on-paper feel.
*   **Don't** crowd the cards. If a card has more than 3-4 data points, it likely needs to be broken into two nested containers.