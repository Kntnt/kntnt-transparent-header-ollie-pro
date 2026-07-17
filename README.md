# Kntnt Transparent Header for Ollie Pro

[![Requires WordPress: 6.5+](https://img.shields.io/badge/WordPress-6.5+-blue.svg)](https://wordpress.org)
[![Requires PHP: 8.3+](https://img.shields.io/badge/PHP-8.3+-blue.svg)](https://php.net)
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
- **Fixes two Ollie Pro bugs** along the way (see [Why you can't simplify this](#why-you-cant-simplify-this)).
- **No settings page.** One class in the Site Editor is the whole configuration.
- **Updates itself** from GitHub, like any plugin from wordpress.org.

### The problem

Ollie and Ollie Pro give you a sticky header that hides when you scroll down and comes back when you scroll up. What they have no concept of is a *transparent* header — the word appears nowhere in either of them. So the header always carries its background colour, and a hero image can never reach the top of the screen; there is always a band of colour above it.

Doing it by hand is harder than it looks. The header has to leave the document flow or it pushes the hero down; the rule that does that has to beat the theme's own rule at identical specificity; the fade has to be added without touching a property Ollie Pro is already animating, or the header stops sliding and starts snapping. Get any of it slightly wrong and it fails silently.

### How this plugin helps

It does that work once, correctly, as a mechanism with no design opinions. You keep choosing the colours; the plugin only decides *when* the header is transparent and when it is not.

## Requirements

* **WordPress:** 6.5 or later
* **PHP:** 8.3 or later
* **Theme:** Ollie, or any child theme of it
* **Plugin:** Ollie Pro

Both requirements are enforced, and neither nags you:

- **Ollie Pro** is declared in the `Requires Plugins` header, so WordPress will not let you activate this plugin without it, and deactivates this one if you deactivate Ollie Pro. That header is also what sets the WordPress floor: it landed in 6.5, and on anything older it is ignored, so the plugin would activate with no Ollie Pro present. Nothing else here needs a WordPress newer than that.
- **The Ollie theme** is checked when the plugin loads. Under any other theme it loads no styles or scripts and touches nothing on your pages, and says nothing about it either.

## Installation

1. Download [kntnt-transparent-header-ollie-pro.zip](https://github.com/Kntnt/kntnt-transparent-header-ollie-pro/releases/latest/download/kntnt-transparent-header-ollie-pro.zip).
2. In WordPress, go to **Plugins → Add New → Upload Plugin**.
3. Choose the file you downloaded and click **Install Now**.
4. Click **Activate**.

That is the normal WordPress upload route — nothing special. The plugin is not on wordpress.org, but it updates itself from its GitHub releases: new versions appear under **Dashboard → Updates** and install with one click, exactly like any other plugin.

## Usage

Two steps in the Site Editor. No CSS required.

### 1. Build the header template part

1. **Appearance → Editor** → *Patterns* → *Template parts* → **Add new**. Name it `Header`, area **Header**.

2. Add a **Group** block as the outermost block. It must be a Group — Ollie Pro's sticky controls appear on no other block.

3. Select the group → **Position → Sticky**.

4. Ollie Pro's controls now appear under Position:

   1. *Hide in Scroll Down:* Check

   2. *Top Offset:* 0

   3. *Unstick on Mobile*: Your choice (off is recommended)

   4. *Sticky Z-Index*: empty

5. Put the logo, navigation and so on **inside** the group.

6. Give the group a **background colour**. This is the solid state — what you see once scrolled.

> [!NOTE]
> The plugin puts `position: fixed` on the `header` itself, which already lifts it over the hero; Ollie Pro's z-index lands on the group *inside* that header, where it changes nothing.

> [!IMPORTANT]
>
> **Don't set a hover colour on the header group.**
>
> Ollie Pro writes the `transition` shorthand for both `hoverTextColor` and `stickyOnScrollUp` at equal specificity; the hover rule wins on source order and kills the slide. Put hover colours on the links inside the header instead.

### 2. Turn on transparent mode

Do this on **the templates where you want a transparent header**. Those are the only templates that change; all your others keep their normal solid header.

1. Go to **Appearance → Editor** → *Templates* and click the template you want — for example *Front Page*.

2. Click the pencil (**Edit**) to open it.

3. Click **once** on the header at the very top of the template. The little toolbar that pops up must say **Template Part**. If it names another block, you have clicked something *inside* the header — press **Esc** until *Template Part* appears.

4. In the sidebar on the right, scroll down and open **Advanced**.

5. Click into **Additional CSS class(es)** and type `has-transparent-header`. If something is already in the field — Ollie's templates put `site-header` there — leave it and add yours after a space.

6. Click **Save**.

Repeat for each template where you want it.

That's the whole configuration. The hero should be the first block in `main`, with no top margin — the header lays over it.

If your logo or links are hard to read against the hero, that is expected — they keep their normal colour. See [Colouring the header's contents](#colouring-the-headers-contents-against-the-hero) below.

> [!IMPORTANT]
> **The header must come out as a `<header>` element.**
>
> That is what the plugin hangs on: every rule it ships starts with `header.has-transparent-header`, and Ollie's own sticky rule keys on the tag as well. Land the header in a `<div>` instead and nothing happens at all — no error, no effect, just a header that stays solid.
>
> **Using Ollie's ready-made header? Then it is already taken care of, and there is nothing for you to do.** Ollie's templates ship the header part with the tag set explicitly, and the part you built in step 1 carries the **Header** area, which produces `<header>` on its own.

> [!IMPORTANT]
> **Never put the Template Part inside a Group block.**
>
> The Group belongs **inside** the header part, as in step 1 — never around it. Wrap the part in a Group and the sticky header dies silently: no error, no warning, the header just scrolls away with the page like any ordinary block.
>
> The reason is that Ollie makes the header sticky in pure CSS — `body:not(.wp-admin) header:has(>.is-position-sticky) { position: sticky }` — so the `<header>` element itself is the thing that sticks. A sticky element can never leave its parent's box, and a wrapping Group shrinks to exactly the header's height, which leaves it no room at all to travel. Stock Ollie ships no wrapper for precisely this reason.
>
> The usual way people fall into it is adding a Group *"just to have somewhere to put the class"*. Don't. The class goes on the **Template Part** block itself — Advanced → Additional CSS class(es), as in step 2.

> [!WARNING]
> **Ollie's Academy teaches a different structure. Use step 1's instead.**
>
> The lesson [Sticky headers using sticky positioning](https://olliewp.com/lesson/using-sticky-positioning-for-sticky-a/) wraps the header part in a Group and makes *that* sticky. It works — but this plugin reads the opposite structure: the sticky Group **inside** the `<header>`, as step 1 builds it. With the Group outside, nothing matches and the header stays solid, silently.
>
> No trade-off: Ollie's header pattern already has that Group inside, and the theme ships the CSS to make the header sticky from it — `header:has(>.is-position-sticky)`. Step 1 only switches it on. Same sticky, hide-on-scroll header, plugin or not, and the only one that can go transparent.

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
- **PHP 8.3 or later** — check with `php -v`.
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
composer gate      # both of the below, in order
composer phpcs     # WordPress Coding Standards, with the documented deviations
composer phpstan   # static analysis at level max, and the PHP floor
composer phpcbf    # fixes most violations automatically
shellcheck build-release-zip.sh
```

Both gates must be **clean** before you open a pull request.

`composer phpstan` runs at `level: max` and is also the only thing enforcing the `Requires PHP` floor: `phpVersion` in `phpstan.neon.dist` is set to it, so syntax newer than the floor is reported — and that diagnostic is non-ignorable, so a baseline cannot bury it. Keep the two in step. phpcs cannot do this job; its `testVersion` setting belongs to PHPCompatibility, whose last stable release (9.3.5, 2019) knows nothing of PHP 8.0 or later and would pass this codebase in silence at any setting.

`composer phpcs` must be **silent** — no errors and no warnings — before you open a pull request. Warnings fail the gate too, so anything it prints is a real finding, not noise to look past. Suppressing a finding needs a `phpcs:ignore` that names the sniff and gives the reason, at the smallest possible scope; there are currently none in the codebase, and that is the preferred state.

Two rules the coding standard states are **not** enforced by the gate, because no phpcs sniff can express them — a comment on its own line stops at column 80, and `=`/`=>` are never vertically aligned. Both need a custom sniff ([#37](https://github.com/Kntnt/kntnt-code-skills/issues/37), [#38](https://github.com/Kntnt/kntnt-code-skills/issues/38)). Until then they are your responsibility, not the linter's. Code lines have no length limit at all — a long line is preferred over breaking arguments apart to satisfy a column count.

The ruleset in `phpcs.xml.dist` deliberately switches off several sniffs where the WordPress Coding Standards contradict this project's standard. Do not "fix" the code toward upstream WP-CS, and note that **`phpcbf` will not revert those deviations** — they are excluded, not tolerated.

There is no test suite. The plugin's only logic is deciding when to enqueue two files; there is nothing a unit test could meaningfully constrain that the two gates and a browser do not.

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

### Why you can't simplify this

The whole plugin is three CSS rules and one script. Every one of them looks naive or removable, and every one is load-bearing. The obvious cleanup is listed against each, because that cleanup is how each bug gets reintroduced.

| Rule | The tempting "fix" | What it costs |
|---|---|---|
| `--sticky-full-offset: var(--wp-admin--admin-bar--height, 0px)` | Delete it — nothing references it in this codebase. | Ollie Pro hides the header with `translateY(calc(-100% - var(--sticky-full-offset)))` but only sets that variable when a Top Offset is configured, while Ollie pins the header down by the admin bar's height regardless. The header then stops exactly that far short of the top. The admin bar masks the leftover band while it is fixed, but at 600px and below core makes it `absolute` and it scrolls away — leaving a logged-in visitor a 46px strip of header stuck to the top of the screen. Measured, not theorised. |
| `transition-property: transform, background-color, box-shadow` | Collapse it into the `transition` shorthand. | The shorthand resets Ollie Pro's own `transition: transform …` at equal specificity, and the header snaps instead of sliding. The longhand overrides only the property list, so duration and timing stay inherited from Ollie Pro's rule and the fade cannot drift out of step with the slide. |
| `position: fixed` on `.has-transparent-header` | Add `!important` to make it win. | It already wins, and not by specificity: Ollie's sticky rule ties it exactly at (0,2,2). It wins on source order alone — which is why the stylesheet is enqueued at priority 20 with a dependency on the `ollie` handle. Change either and the header goes back to `sticky`, in the flow, pushing the hero down. `!important` would paper over that and hide the real dependency. |
| the `is-scrolled` toggle in `js/header.js` | Have it add `is-transparent` instead — it reads more naturally. | A script-set class cannot exist at first paint, so every page load would render solid, then fade to transparent: a visible flash. Transparency has to be the *default*, i.e. the absence of a class. See [Why the transparent state has no class of its own](#why-the-transparent-state-has-no-class-of-its-own). |

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
