# dp-10 Implementation Notes

## Slider Accessibility Checklist (dp-10, Apr 2026)

### WCAG Criteria Covered
- **1.3.1 Info and Relationships**: `role="slider"`, `aria-labelledby`, `aria-describedby`
- **1.4.1 Use of Color**: Price/error states use text labels in addition to color cues
- **2.1.1 Keyboard**: Full APG slider keyboard contract (Arrow, PageUp/Down, Home/End)
- **2.4.13 Focus Appearance**: `:focus-visible` ring, 2px outline, 3:1+ contrast ratio
- **2.5.8 Target Size Minimum**: Touch target expanded to 44px via CSS pseudo-element
- **4.1.3 Status Messages**: `aria-live="polite"` for price updates; `aria-live="assertive"` for errors

### Live Region Architecture
- `<output role="status" aria-live="polite">` — sr-only mirror for price/value announcements
- `<span aria-live="assertive">` — error state announcements
- Both are Alpine-driven and outside Livewire's morph scope

### Keyboard Contract

| Key | Action |
|-----|--------|
| Arrow Up/Right | +1 step |
| Arrow Down/Left | -1 step |
| Page Up | +10 steps |
| Page Down | -10 steps |
| Home | minimum value |
| End | maximum value |

### Manual Smoke Tests Required Post-Deploy
1. Tab to slider, confirm focus ring visible
2. PageUp/Down, Home/End function correctly
3. VoiceOver/NVDA announces "N GB, slider, 1 GB to 64 GB" (not raw integer)
4. Slow 3G throttle → "Calculating…" appears
5. Block pricing API → error state shown and announced
6. 320px viewport touch emulation → comfortable drag target
