# 🤖 AI Development Workflow Note

This project was developed with the assistance of AI (Claude by Anthropic) as a coding and architectural support tool.

---

## 🧠 How AI was used

### 1. Project Planning
AI assisted in:
- Designing the feature breakdown (Document CRUD, editing, sharing, auth)
- Recommending a realistic tech stack for a 4-hour timebox (React + PHP + MySQL)
- Defining scope boundaries — what to build vs. what to defer
- Structuring the project folder layout for a clean React + PHP separation

---

### 2. Frontend Development (React.js)
AI assisted in:
- Building the full React component structure (`App`, `Login`, `Home`, `Editor`, `ShareModal`)
- Implementing React Router v6 navigation and fixing a duplicate `<BrowserRouter>` error
- Integrating React Quill rich text editor with a custom toolbar
- Fixing `index.js` — missing `ReactDOM.createRoot()` causing a "root not found" error
- Implementing `localStorage`-based session persistence for the auth state
- Building the Share modal with real-time grant/revoke toggle per user

---

### 3. Backend Development (PHP)
AI assisted in:
- Designing REST-style PHP API endpoints for all document and sharing operations
- Writing `share.php` with a duplicate-share guard using a `SELECT` before `INSERT`
- Creating `unshare.php` for access revocation
- Creating `sharedWith.php` to fetch current access list per document
- Creating `users/list.php` to populate the share modal dynamically
- Resolving CORS issues with correct `Access-Control-Allow-*` headers on all endpoints

---

### 4. Database Design
AI helped design:
- The `users` table with seeded demo accounts
- The `document_shares` table with a `UNIQUE KEY` constraint to prevent duplicate shares
- `ON DELETE CASCADE` foreign keys to clean up shares when documents are deleted
- The `seed.sql` file for one-command database setup

---

### 5. Debugging Support
AI resolved:
- `Router` nesting error — `<BrowserRouter>` declared in both `App.jsx` and `index.js`
- `root is not defined` error in `index.js`
- Axios network errors caused by mismatched API base URL
- React state inconsistencies with owned vs shared document lists
- File upload validation (rejecting unsupported file types before API call)

---

### 6. Documentation
AI generated:
- `README.md` — full setup guide, API table, project structure, architecture notes
- `architecture.md` — system design, component breakdown, data flow, design decisions
- `ai_workflow.md` — this file

---

## 🧩 AI Contribution Summary

| Role                    | How it helped                                              |
|-------------------------|------------------------------------------------------------|
| Code assistant          | Generated components, PHP endpoints, SQL schema            |
| Debugging partner       | Identified and fixed runtime and logic errors              |
| System design advisor   | Recommended stack, folder structure, scope priorities      |
| Documentation writer    | Produced README, architecture notes, and this document     |

---

## ⚠️ Human Contribution

All final decisions, integration, testing, and customisation were performed manually, including:
- Project scaffolding and XAMPP configuration
- Database creation and running migrations
- Wiring API base URL to the local PHP server
- UI review and final adjustments
- Evaluating which AI suggestions to accept, modify, or discard
- Deployment and submission

---

## 🎯 Outcome

The result is a functional full-stack document editor demonstrating:
- Lightweight auth with seeded accounts and session persistence
- Full document CRUD with rich text editing
- File import (.txt, .md) converted to editable documents
- A working sharing model with grant/revoke access and owner/shared distinction
- Clean separation between frontend (React) and backend (PHP REST API)
