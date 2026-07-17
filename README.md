# Kntnt Transparent Header for Ollie Pro

Gives Ollie's sticky header a transparent-over-hero mode: transparent at the top of the page, fading to your own background colour once the visitor scrolls.

Requires the **Ollie** theme and **Ollie Pro**. Sticky positioning and hide-on-scroll-down are theirs — this plugin only adds what they have no concept of, and works around two of their defects.

No settings page. No CSS to write. You add one class in the Site Editor.

## How it works

The plugin owns the *mechanism* and ships **no colours**:

- At the top of the page the header has no background and no shadow.
- Once scrolled past 20px, the plugin adds `is-scrolled` to the header group, its own rule stops matching, and **your** background colour — the one you picked in the editor — simply reappears.
- The same happens while a menu inside the header is open (anything with `aria-expanded="true"`), so a mega menu or mobile menu hanging off the header gets a backdrop instead of floating over the hero.

So the solid colour is whatever you set on the group block. The plugin never guesses it and never overrides it.

## Step by step

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

### 4. Colour the header's contents against the hero

Logo and links keep their normal colour, which may be unreadable over a hero image. The transparent state is the **absence** of `is-scrolled`, so target that:

```css
header.has-transparent-header > .wp-block-group:not(.is-scrolled) :where(a, svg) {
  color: var(--wp--preset--color--base);
  fill: currentColor;
}
```

Put it in **Styles → Additional CSS** in the Site Editor, or in a child theme's `theme.json` under `styles.css`.

`:where()` contributes no specificity, so the selector stays low enough to be easy to override but still beats the theme's generated link-colour rule.

## Why the transparent state has no class of its own

You may expect an `is-transparent` class. There isn't one, deliberately.

A class added by JavaScript cannot exist in the first paint. If transparency depended on one, every page load would render the solid header first, then add the class, then animate it away — a visible flash of colour. Measured on a real site: ~90ms of solid background followed by a 300ms fade.

Making the top state the **default** — the state that needs no class and no script — means it is already correct in the first paint. That is why the selector is `:not(.is-scrolled)` and not `.is-transparent`.

The reverse case (loading a page that is already scrolled) renders transparent for a moment and then fades to solid. That is far less jarring, and scroll restoration happens after paint anyway.

## Advanced: partial opacity

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

## What the plugin actually does

Three CSS rules and one small script.

| | Why |
|---|---|
| `--sticky-full-offset: var(--wp-admin--admin-bar--height, 0px)` | **Ollie Pro defect.** It hides the header with `translateY(calc(-100% - var(--sticky-full-offset)))` but only ever sets that variable when a Top Offset is configured. With the theme pinning the header below the admin bar, a logged-in user is otherwise left with an admin-bar-tall strip of header on screen. |
| `transition-property: transform, background-color, box-shadow` | Ollie Pro transitions only `transform`, so the background would flip in one frame. Only the longhand is overridden, so duration and timing are inherited from Ollie Pro's own rule and the fade stays in step with the slide by construction. |
| `position: fixed` on `.has-transparent-header` | Ollie's rule makes every sticky header `sticky`, i.e. in the flow, which pushes the hero down. Both rules have identical specificity (0,2,2), so this only wins because the stylesheet is enqueued **after** the theme's — hence priority 20 and a dependency on the `ollie` handle. |
| `is-scrolled` toggle | The only thing that genuinely needs JavaScript. |

## Known limits

- **The hide threshold is 100px and hardcoded** in Ollie Pro (`SCROLL_TOP_THRESHOLD`, no filter). Changing it means not using "Hide on Scroll Down" and writing hide/show yourself.
- **The header is in the flow when solid** (`position: sticky`) but **out of the flow when transparent** (`position: fixed`). That is deliberate — it cannot lie over the hero otherwise — and it is why no spacer is needed.
- **Don't set a hover colour on the header group.** Ollie Pro writes the `transition` shorthand for both `hoverTextColor` and `stickyOnScrollUp` at equal specificity; the hover rule wins on source order and kills the slide. Put hover colours on the links instead.

## Troubleshooting

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
