# Error Service & Frontend Error Toasts

This project includes a backend Error Service API and a frontend error reporter with DaisyUI toasts.

## Backend: Errors API

Endpoints
- POST `/api/errors` (public) — Record a client/frontend error.
- GET `/api/errors` (admin) — List error logs with filters.
- GET `/api/errors/{id}` (admin) — Show one error log.
- DELETE `/api/errors/{id}` (admin) — Delete a log.
- POST `/api/errors/cleanup` (admin) — Cleanup old logs, body: `{ "days": 30 }`.

Payload (POST /api/errors)
```json
{
  "message": "Error message",
  "level": "error|warning|info",
  "url": "https://...",
  "user_agent": "...",
  "context": { "stack": "...", "extra": "..." }
}
```

Storage
- Model: `IamLab\Model\ErrorLog`
- Table: `error_logs` (see migration `IamLab/Migrations/1.0.3/error_logs.php`)

Logger
- DI service `logger` writes to file path configured by `LOG_PATH`.
- Defaults defined in `IamLab/config/config.php` under `logger`.

## Frontend: Global Error Handler

The frontend automatically captures `window.error` and `unhandledrejection` events and:
- Shows a DaisyUI toast at the top center of the page.
- Sends a report to `/api/errors`.

Message formatting and preservation
- The UI never displays raw objects (avoids showing `[object Object]`).
- Error messages are formatted via a central formatter in `assets/js/lib/errorHandler.js` that:
  - Preserves any backend-provided top-level `message` verbatim when present.
  - Falls back to common fields like `error`, `error_description`, `detail`, or `title`.
  - For `fetch`/Response-like rejections, it attempts to parse JSON/text; otherwise uses a status-line fallback like `Request failed: 500 Internal Server Error`.
  - Truncates messages to ~2000 characters for UI stability (full details are still posted to the backend).

Files
- `assets/js/lib/errorHandler.js` — toast UI + reporter
- Imported and initialized in `assets/js/bootstrap.js`

Manual Toasts
```js
// From any component
window.showToast('Something happened', 'warning');

// Example: Handling errors in a service call catch block
// This is the recommended approach for caught errors
someService.update(id, data)
    .then(response => {
        window.showToast("Success!", "success");
    })
    .catch(err => {
        window.showToast(err.response, "error");
    });
```

Tip: You can pass any value to `window.showToast(value)`, not only strings. The formatter will extract a human-readable message from `Error` objects, API responses, or generic objects. In `.catch` blocks of `m.request` calls, it is common to pass `err.response`.

## Admin Error Logs Viewer (UI)

An admin-only page is provided to browse and manage error logs recorded by the backend.

- Route: `/admin/error-logs` (requires admin role; guarded by frontend `adminGuard` and backend API guards)
- Features:
  - Filters: `level`, free-text search `q` (message/URL), `since` date
  - Pagination with total count
  - View details modal (shows message, URL, user agent, and JSON context)
  - Delete individual logs
  - Cleanup old logs by days (POST `/api/errors/cleanup`)

How it works
- Frontend component: `assets/js/components/AdminErrorLogs.js`
- Calls existing endpoints: `GET /api/errors`, `GET /api/errors/{id}`, `DELETE /api/errors/{id}`, `POST /api/errors/cleanup`
- Uses DaisyUI table, modal, inputs; all errors surfaced via `window.showToast`

## Test Page

A public Test Page demonstrates the error toasts and error reporting flow.

- Route: `/test`
- Frontend component: `assets/js/components/TestPage.js`
- Actions available:
  - Trigger success/info/warning/error toasts
  - Throw a synchronous error (captured by global handler)
  - Trigger an unhandled promise rejection (captured by global handler)
  - Call a failing endpoint and show a Response-derived toast message
  - Send a sample error payload to `POST /api/errors`

## Setup & Build

1. Run migrations
```bash
./phalcons migrate
```

2. Build assets
```bash
./phalcons npm run dev
```

3. Test endpoint
```bash
curl -X POST http://localhost:8080/api/errors \
  -H "Content-Type: application/json" \
  -d '{"message":"Test error from curl","level":"error","context":{"foo":"bar"}}'
```

## Env Vars (optional)
- `LOG_ENABLED=true`
- `LOG_LEVEL=debug`
- `LOG_PATH=/var/www/html/files/logs/app.log`
- `LOG_FORMAT=[%date%][%level%] %message%`

## Backend Error Envelope

All API error responses are normalized by `aAPI::dispatchError(...)` to ensure the frontend always has a clear `message`:

```json
{
  "success": false,
  "message": "Human readable error message",
  "errors": { "original": "shape as provided to dispatchError" },
  "code": "optional",
  "status": "optional",
  "error": "optional"
}
```

Important: Populate the top-level `message` with a user-facing string; put extra details in `errors`.
