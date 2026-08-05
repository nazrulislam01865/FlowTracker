# Authenticated application CSS split

The authenticated application stylesheet is now delivered as four independent,
hashed Vite assets. The login page continues to use its dedicated small login
stylesheet.

## Design-safety guarantees

- `resources/css/flowtrack.css` remains the canonical stylesheet.
- `resources/css/app.css` remains the final source-order addition.
- `scripts/split-flowtrack-css.mjs` splits only after complete top-level CSS
  blocks, never inside a selector, declaration, media query, keyframe, comment,
  or quoted value.
- The generator fails the build unless joining all chunks reproduces the
  canonical combined CSS byte-for-byte.
- Blade loads the four chunks in their original cascade order.

Vite runs the generator automatically for production builds and local
development. It also regenerates the chunks when either canonical CSS source
changes during Vite development.
