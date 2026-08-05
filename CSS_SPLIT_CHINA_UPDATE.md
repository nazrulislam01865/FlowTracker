# Login CSS split for China access

The login page now loads a dedicated critical stylesheet instead of the full
authenticated application stylesheet.

- `resources/css/login.css` contains only the existing login, form, validation,
  and responsive rules needed before sign-in.
- `resources/css/app.css` and `resources/css/flowtrack.css` remain the application
  stylesheet, so authenticated pages keep their existing selector order.
- Vite builds both stylesheets as independent hashed assets.

After deployment, verify the login response references `login-*.css`, then keep
gzip and immutable caching enabled in the Nginx HTTPS server block.
