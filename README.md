# 📄 Google Docs Clone (React + PHP)

A simplified Google Docs-inspired collaborative document editor built with React and PHP.

---

## 🚀 Features

### 1. Authentication
- Lightweight login screen with 3 seeded demo accounts
- No passwords required — select a user to sign in
- Session persists across page refresh via `localStorage`
- Sign out from any screen

### 2. Document Management
- Create, edit, rename, and delete documents
- Auto-saves owner with every document
- Documents remain available after refresh (MySQL persistence)

### 3. Rich Text Editor
- Built with React Quill
- Supports bold, italic, underline, strikethrough
- Headings (H1, H2, H3)
- Ordered and unordered lists

### 4. File Upload
- Upload `.txt` or `.md` files
- File is automatically converted into a new editable document
- Unsupported file types are rejected with a clear UI message

### 5. Sharing System
- Each document has a clear owner
- Owners can share documents with any other user via the Share modal
- Access can be granted or revoked at any time
- Home screen shows a visible distinction:
  - **Owned documents** — green "Owner" badge
  - **Shared documents** — red "Shared" badge with the sharer's name

---

## 🧰 Tech Stack

| Layer     | Technology                        |
|-----------|-----------------------------------|
| Frontend  | React.js, React Router, Axios     |
| Editor    | React Quill                       |
| Backend   | PHP (XAMPP), REST-style endpoints |
| Database  | MySQL                             |

---

## ⚙️ Local Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP)
- [Node.js](https://nodejs.org/) v16+
- npm

---

### 1. Clone the repository

```bash
git clone https://github.com/tabbykaruga/GoogleDocs
cd GoogleDocs
```

---

### 2. Set up the database

1. Start **Apache** and **MySQL** in XAMPP
2. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
3. Create a database called `docs_clone`
4. Import the schema:
   - Run `seed.sql` (found in the project root) to create all tables and seed the 3 demo users

```sql
-- Tables created by seed.sql
users              -- demo accounts (John, Jane, Bob)
documents          -- all documents with owner_id
document_shares    -- sharing relationships
```

---

### 3. Configure the database connection

Edit `api/config/database.php` and update your credentials:

```php
private $host     = "localhost";
private $db_name  = "docs_clone";
private $username = "root";
private $password = "";        // default XAMPP password is empty
```

---

### 4. Place the backend in XAMPP

Copy the `api/` folder into your XAMPP `htdocs` directory:

```
C:/xampp/htdocs/docs-api/
```

Your PHP endpoints will then be available at:

```
http://localhost/docs-api/documents/create.php
http://localhost/docs-api/documents/read.php
...
```

---

### 5. Configure the React API base URL

Edit `src/services/api.js` and set the base URL to match your XAMPP path:

```js
const API = axios.create({
  baseURL: "http://localhost/docs-api/api",
});
```

---

### 6. Install dependencies and start the frontend

```bash
npm install
npm start
```

The app will open at [http://localhost:3000](http://localhost:3000).

---

## 👤 Demo Accounts

| Name       | Email            |
|------------|------------------|
| John Doe   | john@docs.com    |
| Jane Smith | jane@docs.com    |
| Bob Wilson | bob@docs.com     |

> No passwords required. Select any account on the login screen to sign in.

---

## 📁 Project Structure

```
GoogleDocs/
├── public/
├── src/
│   ├── components/
│   │   ├── Login.jsx         # Auth screen with seeded users
│   │   ├── Home.jsx          # Document list (owned + shared)
│   │   ├── Editor.jsx        # Rich text editor
│   │   └── ShareModal.jsx    # Share / revoke access modal
│   ├── services/
│   │   └── api.js            # Axios base config
│   ├── App.jsx
│   └── index.js
├── api/
│   ├── config/
│   │   └── database.php
│   ├── documents/
│   │   ├── create.php
│   │   ├── read.php
│   │   ├── update.php
│   │   ├── delete.php
│   │   ├── docList.php
│   │   ├── import.php
│   │   ├── share.php         # Grant access (duplicate-safe)
│   │   ├── unshare.php       # Revoke access
│   │   └── sharedWith.php    # List users with access
│   └── users/
│       └── list.php          # Fetch all users for share modal
├── seed.sql                  # DB schema + demo user seeds
└── README.md
```

---

## 🔌 API Endpoints

| Method | Endpoint                        | Description                        |
|--------|---------------------------------|------------------------------------|
| POST   | `/documents/create.php`         | Create a new document              |
| GET    | `/documents/read.php?id=`       | Fetch a document by ID             |
| POST   | `/documents/update.php`         | Update title and content           |
| POST   | `/documents/delete.php`         | Delete a document                  |
| POST   | `/documents/docList.php`        | Get owned + shared docs for a user |
| POST   | `/documents/import.php`         | Import a .txt or .md file          |
| POST   | `/documents/share.php`          | Share a document with a user       |
| POST   | `/documents/unshare.php`        | Revoke a user's access             |
| GET    | `/documents/sharedWith.php?document_id=` | List users with access    |
| GET    | `/users/list.php`               | List all users                     |

---

## ✅ Automated Test

Run the included test with:

```bash
npm test
```

The test verifies that the Editor component renders correctly with a document title input and the React Quill editor present.

---

## 🏗️ Architecture Notes

**What was prioritised:**

- **Shipping core value fast** — document creation, editing, and persistence are the backbone; everything else builds on top.
- **Supabase-free simplicity** — PHP + MySQL on XAMPP keeps the backend self-contained with zero external dependencies, making local setup a single `npm install` + XAMPP start.
- **Auth without complexity** — seeded accounts with `localStorage` session satisfy the sharing requirement (you need to know *who* is sharing) without the time cost of JWT or session tokens.
- **Sharing as a first-class feature** — the Share modal shows real-time state (who has access, toggle on/off) rather than a fire-and-forget button, which demonstrates clear product intent.

**Known limitations / next steps with more time:**
- Password-based or token auth
- Real-time collaborative editing (WebSockets)
- `.docx` file import support
- Role-based permissions (view vs edit)
