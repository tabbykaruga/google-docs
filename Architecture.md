# 🏗️ Architecture Notes

## Overview

This is a lightweight full-stack document editor built with React on the frontend and PHP on the backend, communicating over a REST-style HTTP API. MySQL handles persistence. There is no separate auth server — identity is simulated with seeded accounts and `localStorage`.

---

## System Diagram

```
┌─────────────────────────────────────────────┐
│                  Browser                     │
│                                              │
│  ┌──────────┐  ┌──────────┐  ┌───────────┐  │
│  │  Login   │  │   Home   │  │  Editor   │  │
│  │ (select  │  │ (doc     │  │ (ReactQ-  │  │
│  │  user)   │  │  list)   │  │  uill)    │  │
│  └────┬─────┘  └────┬─────┘  └─────┬─────┘  │
│       │             │              │         │
│       └─────────────┼──────────────┘         │
│                     │ Axios (HTTP)            │
└─────────────────────┼───────────────────────-┘
                      │
        ┌─────────────▼──────────────┐
        │       PHP REST API         │
        │  (XAMPP / Apache)          │
        │                            │
        │  /documents/create.php     │
        │  /documents/read.php       │
        │  /documents/update.php     │
        │  /documents/delete.php     │
        │  /documents/docList.php    │
        │  /documents/import.php     │
        │  /documents/share.php      │
        │  /documents/unshare.php    │
        │  /documents/sharedWith.php │
        │  /users/list.php           │
        └─────────────┬──────────────┘
                      │ PDO
        ┌─────────────▼──────────────┐
        │          MySQL             │
        │                            │
        │  users                     │
        │  documents                 │
        │  document_shares           │
        └────────────────────────────┘
```

---

## Frontend Architecture

### Component Tree

```
App
├── Login               ← shown when no session in localStorage
└── (authenticated)
    ├── Home            ← document list, split owned / shared
    │   └── ShareModal  ← modal rendered on top when Share clicked
    └── Editor          ← rich text editor, file import, save/update
```

### State Management

No external state library (Redux, Zustand) was used — React's built-in `useState` and `useEffect` are sufficient for this scope.

| State          | Where it lives  | How it's shared             |
|----------------|-----------------|-----------------------------|
| `currentUser`  | `App.jsx`       | Passed as prop to all pages |
| `owned` docs   | `Home.jsx`      | Local component state       |
| `shared` docs  | `Home.jsx`      | Local component state       |
| `content`      | `Editor.jsx`    | Local component state       |
| `sharedWith`   | `ShareModal.jsx`| Local component state       |

### Session / Auth

Auth is simulated — no JWT, no cookies, no server session. On login, the selected user object is written to `localStorage`. On every app load, `App.jsx` reads from `localStorage` to restore the session. Sign out clears the key.

This satisfies the assessment requirement (demonstrate a document owner and sharing logic) without the overhead of a full auth system in a 4-hour scope.

### Routing

React Router v6 with three routes:

| Route          | Component | Notes                        |
|----------------|-----------|------------------------------|
| `/`            | `Home`    | Document list                |
| `/editor`      | `Editor`  | New document (no ID)         |
| `/editor/:id`  | `Editor`  | Edit existing document by ID |

---

## Backend Architecture

### Design Approach

Each PHP file is a single-responsibility endpoint. There is no framework (no Laravel, no Slim) — raw PHP with PDO keeps the setup to XAMPP only, which reduces environment friction for local reviewers.

All endpoints:
- Set `Access-Control-Allow-Origin: *` for local cross-origin requests
- Accept JSON via `php://input`
- Return JSON responses
- Use PDO prepared statements (prevents SQL injection)

### Endpoint Responsibilities

| File                  | Method | Responsibility                              |
|-----------------------|--------|---------------------------------------------|
| `create.php`          | POST   | Insert new document, return new ID          |
| `read.php`            | GET    | Fetch single document by ID                 |
| `update.php`          | POST   | Update title and content                    |
| `delete.php`          | POST   | Delete document by ID                       |
| `docList.php`         | POST   | Return owned + shared docs for a user       |
| `import.php`          | POST   | Create document from uploaded file content  |
| `share.php`           | POST   | Insert share record (duplicate-safe)        |
| `unshare.php`         | POST   | Delete share record                         |
| `sharedWith.php`      | GET    | Return users who have access to a document  |
| `users/list.php`      | GET    | Return all users for the share modal        |

---

## Database Schema

```sql
users
  id          INT PK AUTO_INCREMENT
  name        VARCHAR(100)
  email       VARCHAR(150) UNIQUE
  created_at  TIMESTAMP

documents
  id          INT PK AUTO_INCREMENT
  title       VARCHAR(255)
  content     LONGTEXT          -- stores ReactQuill HTML/delta
  owner_id    INT FK → users.id
  created_at  TIMESTAMP
  updated_at  TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

document_shares
  id           INT PK AUTO_INCREMENT
  document_id  INT FK → documents.id  ON DELETE CASCADE
  user_id      INT FK → users.id      ON DELETE CASCADE
  created_at   TIMESTAMP
  UNIQUE (document_id, user_id)        -- prevents duplicate shares
```

### Key design decisions

- `content` stored as `LONGTEXT` — ReactQuill outputs HTML strings; storing raw HTML keeps the round-trip simple (no serialisation layer needed).
- `UNIQUE KEY` on `(document_id, user_id)` in `document_shares` — enforced at the database level so duplicate shares are impossible even if the API is called twice.
- `ON DELETE CASCADE` — deleting a document automatically removes all its share records, keeping the database clean without extra cleanup queries.

---

## Sharing Model

```
Owner creates document
        │
        ▼
Owner clicks Share → ShareModal opens
        │
        ▼
Modal fetches /users/list.php  (all users except owner)
Modal fetches /sharedWith.php  (users already with access)
        │
        ▼
Owner toggles access per user
  ├── Give access  → POST /share.php    → INSERT into document_shares
  └── Remove access → POST /unshare.php → DELETE from document_shares
        │
        ▼
Target user logs in → /docList.php returns their shared docs
Home screen shows "Shared" badge + sharer's name
```

---

## What Was Prioritised and Why

### 1. Core editing experience first
The editor is the centrepiece of the assessment. ReactQuill was chosen because it delivers all required formatting (bold, italic, underline, headings, lists) in a single install with no custom toolbar code needed.

### 2. Sharing as working logic, not just UI
The sharing model uses real database records with a unique constraint, real-time toggle state in the modal, and a visible owned/shared distinction on the home screen. This demonstrates product intent beyond a hardcoded button.

### 3. Auth lightweight by design
A full JWT or session-based auth system would consume 45–60 minutes. Seeded accounts with `localStorage` takes 20 minutes and satisfies the requirement: there is a concept of identity, a document owner, and access control based on that identity.

### 4. No backend framework
Using raw PHP keeps the setup to XAMPP only. A reviewer can clone the repo, import one SQL file, and run `npm start` — no Composer, no artisan, no environment files beyond the DB config.

---

## Known Limitations

| Limitation                        | Next step with more time                        |
|-----------------------------------|-------------------------------------------------|
| No password-based auth            | Add bcrypt hashing + session tokens             |
| No real-time collaboration        | WebSockets or Supabase Realtime                 |
| `.docx` import not supported      | Add `mammoth.js` for Word file parsing          |
| No permission levels (view/edit)  | Add `permission` column to `document_shares`    |
| No pagination on document list    | Add limit/offset to `docList.php`               |
