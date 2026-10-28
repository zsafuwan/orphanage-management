# Tailwind design

The public website and the director, admin and staff Blade pages use Tailwind CSS
v4 components. Their editable styles live in `resources/css/design/`.
The matching files in `public/css/` are compiled output; do not edit those directly.

## Build and preview

```sh
npm run build:design
npm run dev:design
```

`build:design` compiles the design without starting Vite or PHP. Herd serves the
result as ordinary CSS. `dev:design` watches the design sources, Blade templates
and portal JavaScript. Existing local browser refresh then picks up the CSS.
`npm run dev` runs this watcher alongside Vite. `npm run build` builds both.

## Components and utilities

Existing component selectors and JavaScript hooks are preserved. Components use
Tailwind `@apply`, for example:

```css
@import 'tailwindcss/theme.css' reference prefix(tw);

.example-card {
    @apply tw:flex tw:items-center tw:gap-[16px] tw:rounded-[20px];
    background: var(--role-soft);
}
```

Direct utilities in Blade or JavaScript templates also use the `tw:` prefix:

```html
<div class="tw:flex tw:items-center tw:gap-4">...</div>
```

Use complete, literal utility names so the scanner can detect them. Do not build
utility names by concatenating strings.

The prefix isolates Tailwind tokens from the existing pastel role variables.
`resources/css/design-utilities.css` supplies those tokens and direct utilities.
Preflight is omitted because the website already has a base reset. The original
page-specific stylesheet order is retained to preserve layout and overrides.
Custom CSS remains for role variables, complex selectors, animation keyframes,
and visual effects that do not benefit from utility conversion. Existing inline
styles and third-party icon/chart styles are still supported.

Page-level style blocks have been moved to `resources/css/design/embedded/`.
The build writes each output atomically so a refresh does not read partial CSS.
