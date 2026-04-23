# DECISIONS.md

### dp-10 (Apr 2026) — Slider accessibility + UX baseline

**Decision**: Use native `<input type="range">` + WAI-ARIA attributes rather than noUiSlider or Ariakit.
- noUiSlider would add a JS bundle dependency; Ariakit is React-only. The native APG attribute set (`role="slider"`, `aria-valuemin/max/now/text`, `aria-labelledby/describedby`) achieves equivalent screen reader support with zero new dependencies.
- Live regions use Alpine-driven `<output>` and `<span aria-live>` elements that are outside Livewire's morph scope, avoiding the "morph spam" flicker recorded in dp-core-01 PROGRESS.md.
- Keyboard enhancement (PageUp/Down/Home/End) is implemented via Alpine `x-on:keydown` rather than a custom JS class, keeping the implementation inline and diffable.
- Touch target expansion uses CSS `::before` pseudo-element trick (same approach as noUiSlider's `.noUi-touch-area`) — pure CSS, zero JS.

**Contract**: The ARIA attributes added in dp-10 are stable. Future commits MUST NOT remove `role="slider"`, `aria-valuemin/max/now/text`, `aria-labelledby`, `aria-describedby`, or the `<output>` live region without updating this DECISIONS entry and the accessibility test suite.
