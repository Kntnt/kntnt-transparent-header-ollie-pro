# Kntnt Transparent Header for Ollie Pro

[![Requires WordPress: 6.7+](https://img.shields.io/badge/WordPress-6.7+-blue.svg)](https://wordpress.org)
[![Requires PHP: 8.5+](https://img.shields.io/badge/PHP-8.5+-blue.svg)](https://php.net)
[![License: GPL v2+](https://img.shields.io/badge/License-GPLv2+-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
[![Latest release](https://img.shields.io/github/v/release/Kntnt/kntnt-transparent-header-ollie-pro)](https://github.com/Kntnt/kntnt-transparent-header-ollie-pro/releases/latest)

Lets Ollie's sticky header lie transparently over your hero image, then fade to its normal colour as the visitor scrolls.

## Description

You have built a site with the **Ollie** theme and **Ollie Pro**, you have a hero image at the top of the page, and you want the header to float over it — no bar of colour cutting across the picture — and then turn solid once the visitor scrolls down. This plugin does exactly that, and nothing else.

It is for anyone running Ollie and Ollie Pro who wants that effect without writing the CSS themselves. Install it, tick one box in the Site Editor, and you are done.

### Key features

- **Transparent over the hero.** At the top of the page the header has no background and no shadow. Your hero image starts at the very top of the screen.
- **Fades to your own colour.** Scroll past 20 pixels and the header fades back to the background colour *you* chose in the Site Editor. The plugin ships no colours of its own and never overrides your design.
- **Solid behind open menus.** Open a mega menu or the mobile menu and the header turns solid immediately, so the panel has a backdrop instead of floating over the picture.
- **No flash on page load.** The header is transparent from the very first frame, not solid-then-corrected.
- **Fixes two Ollie Pro bugs** along the way (see [What the plugin actually does](#what-the-plugin-actually-does)).
- **No settings page.** One class in the Site Editor is the whole configuration.
- **Updates itself** from GitHub, like any plugin from wordpress.org.

### The problem

Ollie and Ollie Pro give you a sticky header that hides when you scroll down and comes back when you scroll up. What they have no concept of is a *transparent* header — the word appears nowhere in either of them. So the header always carries its background colour, and a hero image can never reach the top of the screen; there is always a band of colour above it.

Doing it by hand is harder than it looks. The header has to leave the document flow or it pushes the hero down; the rule that does that has to beat the theme's own rule at identical specificity; the fade has to be added without touching a property Ollie Pro is already animating, or the header stops sliding and starts snapping. Get any of it slightly wrong and it fails silently.

### How this plugin helps

It does that work once, correctly, as a mechanism with no design opinions. You keep choosing the colours; the plugin only decides *when* the header is transparent and when it is not.

## Requirements

| | |
|---|---|
| WordPress | 6.7 or later |
| PHP | 8.5 or later |
| Theme | **Ollie**, or any child theme of it |
| Plugin | **Ollie Pro** |

Both requirements are enforced, and neither nags you:

- **Ollie Pro** is declared in the `Requires Plugins` header, so WordPress will not let you activate this plugin without it, and deactivates this one if you deactivate Ollie Pro.
- **The Ollie theme** is checked when the plugin loads. Under any other theme it loads no styles or scripts and touches nothing on your pages, and says nothing about it either — Ollie Pro already tells you when the theme is wrong, and there is no point saying it twice. It does keep checking for its own updates, so a site that switches away from Ollie and back is not stranded on an old version.

## Installation

1. Download **`kntnt-transparent-header-ollie-pro.zip`** from the [latest release](https://github.com/Kntnt/kntnt-transparent-header-ollie-pro/releases/latest/download/kntnt-transparent-header-ollie-pro.zip).
2. In WordPress, go to **Plugins → Add New → Upload Plugin**.
3. Choose the file you downloaded and click **Install Now**.
4. Click **Activate**.

That is the normal WordPress upload route — nothing special. The plugin is not on wordpress.org, but it updates itself from its GitHub releases: new versions appear under **Dashboard → Updates** and install with one click, exactly like any other plugin.

## Usage

Three steps in the Site Editor. No CSS required.

### 1. Build the header template part

1. **Appearance → Editor** → *Patterns* → *Template parts* → **Add new**. Name it `Header`, area **Header**.
2. Add a **Group** block as the outermost block. It must be a Group — Ollie Pro's sticky controls appear on no other block.
3. Select the group → **Position → Sticky**.
4. Ollie Pro's controls now appear under Position:
   - **☑ "Hide on Scroll Down"** — this is the hide-on-scroll-down/show-on-scroll-up behaviour. (The block attribute is called `stickyOnScrollUp`; don't go looking for "scroll up" in the UI.)
   - **Top Offset: leave at 0.** Setting it makes Ollie Pro take over offset handling and the plugin's admin-bar fix becomes redundant.
   - *Unstick on Mobile*: off.
   - *Sticky Z-Index*: `9999`.
5. Put the logo, navigation and so on **inside** the group.
6. Give the group a **background colour**. This is the solid state — what you see once scrolled.

### 2. Add the part to your templates

In each template, add the **Template Part** block as a **direct child of the template root**, with `tagName: header` and `className: site-header`.

> **Never wrap the template part in a Group block.**
>
> Ollie makes the header sticky in pure CSS — `body:not(.wp-admin) header:has(>.is-position-sticky) { position: sticky }` — and a sticky element can never leave its parent's box. A wrapper shrinks to exactly the header's height, which leaves zero room to travel, and **sticky dies silently**: no error, the header just scrolls away with the page. Stock Ollie has no wrapper.
>
> The trap is adding a group *"just to put a class on it"*. Don't. Put the class on the **Template Part block** instead (Advanced → Additional CSS class(es)).

### 3. Turn on transparent mode

On the template that has a hero, select the **Template Part** block → **Advanced → Additional CSS class(es)**:

```
site-header has-transparent-header
```

That's the whole configuration. The hero should be the first block in `main`, with no top margin — the header lays over it.

### Two things that will silently break it

- **Don't wrap the template part in a Group**, as above. It is the single most common way to lose the sticky header.
- **Don't set a hover colour on the header group.** Ollie Pro writes the `transition` shorthand for both `hoverTextColor` and `stickyOnScrollUp` at equal specificity; the hover rule wins on source order and kills the slide. Put hover colours on the links inside the header instead.

If your logo or links are hard to read against the hero, that is expected — they keep their normal colour. See [Colouring the header's contents](#colouring-the-headers-contents-against-the-hero) below.

## Questions, bugs, and feature requests

Have a usage question or something to discuss? Please use [Discussions](https://github.com/Kntnt/kntnt-transparent-header-ollie-pro/discussions).

Found a bug or want to request a feature? Please [open an issue](https://github.com/Kntnt/kntnt-transparent-header-ollie-pro/issues). Search the existing issues first to avoid duplicates.

## Extending

Everything below is for developers who want to push the effect further. The plugin has no filters and no API — it is two CSS classes and a stylesheet, so you extend it by writing CSS against those classes, in **Styles → Additional CSS** in the Site Editor or in a child theme's `theme.json` under `styles.css`.

The public contract is exactly two classes:

| Class | Who sets it |
|---|---|
| `has-transparent-header` | You, on the Template Part block |
| `is-scrolled` | The plugin, on the header group, once scrolled past 20px |

### Colouring the header's contents against the hero

Logo and links keep their normal colour, which may be unreadable over a hero image. The transparent state is the **absence** of `is-scrolled`, so target that:

```css
header.has-transparent-header > .wp-block-group:not(.is-scrolled) :where(a, svg) {
  color: var(--wp--preset--color--base);
  fill: currentColor;
}
```

`:where()` contributes no specificity, so the selector stays low enough to be easy to override but still beats the theme's generated link-colour rule.

### Partial opacity

The plugin's default is all-or-nothing: fully transparent at the top, your colour when scrolled. That covers almost every design. If you need something in between, write it yourself — the two states are plain CSS selectors.

**20% at the top, 90% when scrolled:**

```css
/* Top state. Mirror the plugin's own selector, open-menu condition and all, or
   the header will keep your tint while a mega menu hangs open over the hero.
   `!important` is unavoidable: core emits the block's own preset background as
   `.has-<slug>-background-color { background-color: … !important }`. */
header.has-transparent-header > .wp-block-group:not(.is-scrolled):not(:has([aria-expanded="true"])) {
  background-color: color-mix(in srgb, var(--wp--preset--color--primary) 20%, transparent) !important;
}

/* Scrolled state. */
header.has-transparent-header > .wp-block-group.is-scrolled {
  background-color: color-mix(in srgb, var(--wp--preset--color--primary) 90%, transparent) !important;
}
```

Both ends interpolate — colour *and* alpha — over the same 300ms as the slide, because the plugin already transitions `background-color`.

You can equally use literal colours, including 8-digit hex: `#fff20033` at the top, `#fff200e6` when scrolled.

**Never write the `transition` shorthand on the header group.** Ollie Pro sets `transition: transform …` on it at specificity (0,2,0). Any shorthand that also lands there resets the transform transition and the header will **snap instead of slide**. Add properties with `transition-property` only — the plugin already does this, so normally you need not touch it at all.

### What the plugin actually does

Three CSS rules and one small script.

| | Why |
|---|---|
| `--sticky-full-offset: var(--wp-admin--admin-bar--height, 0px)` | **Ollie Pro defect.** It hides the header with `translateY(calc(-100% - var(--sticky-full-offset)))` but only ever sets that variable when a Top Offset is configured. With the theme pinning the header below the admin bar, a logged-in user is otherwise left with an admin-bar-tall strip of header on screen. |
| `transition-property: transform, background-color, box-shadow` | Ollie Pro transitions only `transform`, so the background would flip in one frame. Only the longhand is overridden, so duration and timing are inherited from Ollie Pro's own rule and the fade stays in step with the slide by construction. |
| `position: fixed` on `.has-transparent-header` | Ollie's rule makes every sticky header `sticky`, i.e. in the flow, which pushes the hero down. Both rules have identical specificity (0,2,2), so this only wins because the stylesheet is enqueued **after** the theme's — hence priority 20 and a dependency on the `ollie` handle. |
| `is-scrolled` toggle | The only thing that genuinely needs JavaScript. |

### Why the transparent state has no class of its own

You may expect an `is-transparent` class. There isn't one, deliberately.

A class added by JavaScript cannot exist in the first paint. If transparency depended on one, every page load would render the solid header first, then add the class, then animate it away — a visible flash of colour. Measured on a real site: ~90ms of solid background followed by a 300ms fade.

Making the top state the **default** — the state that needs no class and no script — means it is already correct in the first paint. That is why the selector is `:not(.is-scrolled)` and not `.is-transparent`.

The reverse case (loading a page that is already scrolled) renders transparent for a moment and then fades to solid. That is far less jarring, and scroll restoration happens after paint anyway.

### Known limits

- **The hide threshold is 100px and hardcoded** in Ollie Pro (`SCROLL_TOP_THRESHOLD`, no filter). Changing it means not using "Hide on Scroll Down" and writing hide/show yourself.
- **The solid threshold is 20px and hardcoded** in this plugin (`SOLID_THRESHOLD` in `js/header.js`). There is no filter for it; change it in a fork if you must.
- **The header is in the flow when solid** (`position: sticky`) but **out of the flow when transparent** (`position: fixed`). That is deliberate — it cannot lie over the hero otherwise — and it is why no spacer is needed.

### Troubleshooting

Paste in the browser console on a hero page:

```js
const g = document.querySelector('header > .wp-block-group.is-position-sticky');
console.log({
  wrapper: g.closest('header').parentElement.className,
  headerPos: getComputedStyle(g.closest('header')).position,
  transition: getComputedStyle(g).transition,
});
```

- `wrapper` **must** be `wp-site-blocks`. If it says `wp-block-group` you have a wrapper — see step 2.
- `headerPos` should be `fixed` on a transparent header, `sticky` otherwise.
- `transition` must contain `transform 0.3s …` **plus** `background-color`. If you see only `color 0.2s`, something set a hover colour on the group.

## Development

This section assumes nothing beyond a terminal. If you have never contributed to a GitHub project before, follow it top to bottom.

### What you need

- **Git** — [installation guide](https://git-scm.com/downloads).
- **PHP 8.5 or later** — check with `php -v`.
- **Composer** — [installation guide](https://getcomposer.org/download/). It installs the development tools; the plugin itself has no dependencies and ships none.
- A **GitHub account**, and [`gh`](https://cli.github.com/) if you want to publish releases.

### Getting the code

Click **Fork** at the top of [the repository](https://github.com/Kntnt/kntnt-transparent-header-ollie-pro) to get your own copy, then replace `YOUR-USERNAME` below:

```bash
git clone https://github.com/YOUR-USERNAME/kntnt-transparent-header-ollie-pro.git
cd kntnt-transparent-header-ollie-pro
composer install
```

`composer install` creates a `vendor/` directory with the coding-standard tools. It is not shipped and not committed — the plugin loads its own classes through the hand-written `autoloader.php`, so the plugin works with no `vendor/` at all.

To run your copy on a real site, symlink or copy the directory into `wp-content/plugins/` of a WordPress install using Ollie and Ollie Pro.

### Making a change

```bash
git checkout -b my-change      # never work directly on main
# … edit files …
composer phpcs                 # must report 0 errors
git add -A
git commit -m "Describe what changed and why"
git push origin my-change
```

Then open a pull request: GitHub shows a **Compare & pull request** button after the push, or use `gh pr create`. See [`CONTRIBUTING.md`](CONTRIBUTING.md) for what is likely to be merged.

### Quality gates

```bash
composer phpcs     # WordPress Coding Standards, with the documented deviations
composer phpcbf    # fixes most violations automatically
shellcheck build-release-zip.sh
```

`composer phpcs` must be **silent** — no errors and no warnings — before you open a pull request. Warnings fail the gate too, so anything it prints is a real finding, not noise to look past.

The two lines that genuinely cannot be wrapped — the plugin header's `Description:`, which WordPress parses from a single line, and a translatable string, which breaks extraction if split — carry their own `phpcs:ignore` naming the sniff and the reason. That is the only sanctioned way to silence a finding: name the sniff, give the reason, keep the scope as small as possible.

The ruleset in `phpcs.xml.dist` deliberately switches off several sniffs where the WordPress Coding Standards contradict this project's standard. Do not "fix" the code toward upstream WP-CS, and note that **`phpcbf` will not revert those deviations** — they are excluded, not tolerated.

There is no test suite. The plugin's only logic is deciding when to enqueue two files; there is nothing a unit test could meaningfully constrain that the coding-standard gate and a browser do not.

### Building a release ZIP locally

```bash
./build-release-zip.sh                       # → dist/kntnt-transparent-header-ollie-pro.zip
./build-release-zip.sh --output ~/Desktop    # → somewhere else
./build-release-zip.sh --help                # every option
```

The script stages the tree in a temporary directory, deletes everything that is not a runtime file, and zips the rest. Your working tree is untouched. There is no compile or bundle step — the plugin ships the PHP, CSS and JS exactly as they are in the repository.

If you add a new runtime file or directory, add it to the `KEEP` array in `build-release-zip.sh`, or it will work locally and be missing from the release.

### Releasing

Requires push access and `gh`. The `Version:` header in the main plugin file must already match the tag.

```bash
git tag v0.1.0
git push origin v0.1.0
./build-release-zip.sh --tag v0.1.0 --create   # opens the release and uploads the ZIP
```

`--create` takes the release notes from the matching section of [`CHANGELOG.md`](CHANGELOG.md). Use `--update` to replace the asset on a release that already exists. The asset name deliberately carries no version number, which is what keeps the `latest/download` link above permanent.

### Project layout

```
kntnt-transparent-header-ollie-pro.php   Plugin header, PHP guard, bootstrap
autoloader.php                           PSR-4 → classes/
classes/Plugin.php                       Singleton; theme check and asset wiring
classes/Updater.php                      Self-update from GitHub releases
css/header.css                           The three rules. Ships no colours
js/header.js                             Toggles is-scrolled
languages/                               Translation template
agents.d/coding-standard/                The coding standard, by language
AGENTS.md                                Entry point for AI assistants; also
                                         the fastest orientation for humans
```

[`AGENTS.md`](AGENTS.md) holds the non-obvious facts about this project — the decisions that look like mistakes until you know why. Read it before changing anything; it is written for AI coding assistants but is the best short briefing for a human contributor too.

## How you can contribute

Contributions are welcome, small or large. Before you start, read [`CONTRIBUTING.md`](CONTRIBUTING.md) — it covers which kinds of change are likely to be merged and how inbound licensing works.

## License

Licensed under [GPL-2.0-or-later](LICENSE). The full licence text is in [`LICENSE`](LICENSE).

## Changelog

Release notes for each version live in [`CHANGELOG.md`](CHANGELOG.md).

The project follows [Keep a Changelog](https://keepachangelog.com/) and [Semantic Versioning](https://semver.org/).
