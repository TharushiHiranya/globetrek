# Design system

GlobeTrek uses a hand-built design system in `styles/style.css` (global) and `styles/home.css` (homepage only). There are no utility frameworks. Every component has a named CSS class.

## Fonts

Loaded from Google Fonts via `@import` in `style.css`.

| Font | Weight | Use |
|---|---|---|
| Poppins | 300, 400, 500, 600, 700 | All body text, buttons, and labels |
| Playfair Display | 400, 600, 700 | Headings only |

## Color palette

| Name | Hex | Role |
|---|---|---|
| Trail Green | `#4a8b3f` | Primary brand color, main CTA buttons |
| Fern | `#427239` | Nav links, focus rings, and icon accents |
| Canopy | `#3A6332` | Form card headings, price text, and page titles |
| Deep Forest | `#1a2e16` | Section titles, footer headings, and darkest text |
| Amber Dusk | `#e6a34d` | Secondary accent, "Browse Destinations" button |
| Amber Deep | `#d4892e` | Amber hover state |
| Sage Mist | `#f5f8f5` | Page background |
| Pale Fern | `#e8ede7` | Input borders and subtle card borders |
| White | `#ffffff` | Cards, forms, and footer background |
| Error Red | `#c0392b` | Error messages |
| Success Green | `#27713a` | Success messages |

Trail Green is the main brand color. Amber Dusk is only used for the highest-priority call to action on a given page.

## Button system

All buttons share the `.btn` base class plus a variant.

```css
.btn              /* Base shape, font, transition, and shimmer effect */
.btn-register     /* Trail Green gradient, primary action */
.btn-login        /* White with Fern border, secondary action */
.btn-amber        /* Amber Dusk gradient, hero CTA */
.btn-outline      /* Transparent with Fern border, fills on hover */
.btn-cta-footer   /* Trail Green, footer CTA */
.btn-block        /* Full-width modifier */
```

The shimmer sweep (`::before`) is built into `.btn` and triggers on hover for all variants. Do not recreate it per button.

## Header

Fixed floating pill at top of page. It uses glassmorphism with a white background and blur filter. It is positioned 24px from the top with `calc(100% - 48px)` width to give breathing room on each side.

It drops to a 12px offset and tighter padding on mobile devices.

```text
.site-header         The pill container
.site-logo-link      Logo wrapper
.site-nav            Navigation links
.header-auth         Login and register buttons on the right
```

Nav links have a 3px underline bar that scales in from center on hover using `scaleX()`.

## Footer

Light-themed card floating slightly above the page bottom. It uses a white background and soft shadow.

Structure inside the footer:
1. `.footer-cta` (centered CTA block with logo, headline, and button)
2. `.footer-main` (four-column grid for links)
3. `.footer-bottom` (copyright and legal links)

The footer renders via `includes/footer.php` and is included on every page. Do not add page-specific content into it.

## Forms

All forms use `.form-card` as the container and `.form-group` for each field.

```css
.form-card          White card, 24px border-radius, max-width 480px by default
.form-group         Label and input wrapper, 20px bottom margin
.form-group input   2px border, 14px border-radius, Sage Mist background
                    Focuses to Fern border with green glow ring
```

Error and success messages use `.error` and `.success` classes. These are styled with colored backgrounds and subtle borders.

## Cards

Package cards (`.package-card`) live in a CSS grid (`.package-grid`). The grid auto-fills with a 320px minimum column. Cards lift 8px on hover with an expanded shadow.

Feature cards (`.why-us-card`) are a four-column grid on desktop, two-column on tablet, and single-column on mobile. The icon box gets a Trail Green gradient on hover.

Destination cards (`.featured-card`) are full-image cards with an overlay gradient. The image zooms in slightly on hover. These are used on the homepage featured section.

## Tables

Data tables have rounded corners, Trail Green gradient headers, and a subtle green background on row hover. These are used in the dashboard for bookings and queries.

## Animations

One keyframe exists called `fadeInUp`. Hero content uses it with staggered delays. The `.animate-in` utility class applies it for general use.

Cards use `translateY` on hover via CSS transitions.

## Responsive breakpoints

| Breakpoint | Changes |
|---|---|
| `max-width: 1024px` | Featured grid, why-us, and stats become two columns |
| `max-width: 768px` | Grids become single column, header shrinks, and footer stacks |
| `max-width: 480px` | Why-us and stats become single column |
