# InkPress — Author Module
## Blog & News Publishing Platform (P10) — Author Role

---

## Quick Setup

### 1. Install XAMPP
Download and install [XAMPP](https://www.apachefriends.org/). Start **Apache** and **MySQL**.

### 2. Copy the project
Place the `author_module` folder inside your XAMPP `htdocs` directory:
```
C:\xampp\htdocs\author_module\   (Windows)
/Applications/XAMPP/htdocs/author_module/   (Mac)
```

### 3. Create the database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click **Import**
3. Select `author_module/config/schema.sql`
4. Click **Go**

This creates all tables and inserts sample data including a demo author account.

### 5. Open the app
Visit: http://localhost/author_module/

**Demo login:**
- Email: `alex@example.com`
- Password: `password123`

---

## File Structure

```
author_module/
├── config/
│   ├── app.php          ← Session, helpers, upload utilities
│   ├── database.php     ← DB connection (mysqli)
│   └── schema.sql       ← Full database + sample data
│
├── controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── ArticleController.php   ← includes AJAX autosave + AJAX filter
│   ├── SeriesController.php    ← includes AJAX add/remove/reorder
│   ├── AnalyticsController.php
│   ├── CommentController.php   ← includes AJAX delete
│   └── ProfileController.php
│
├── models/
│   ├── UserModel.php
│   ├── ArticleModel.php
│   ├── SeriesModel.php
│   └── CommentModel.php
│
├── views/
│   ├── layout/
│   │   ├── header.php   ← Sidebar + nav
│   │   └── footer.php
│   ├── auth/login.php
│   ├── dashboard/index.php
│   ├── articles/
│   │   ├── index.php    ← AJAX filter tabs
│   │   ├── create.php
│   │   └── edit.php     ← AJAX autosave + revision history
│   ├── series/
│   │   ├── index.php
│   │   ├── create.php
│   │   └── edit.php     ← AJAX add/remove/reorder articles
│   ├── analytics/
│   │   ├── index.php    ← Canvas bar charts
│   │   └── article.php
│   ├── comments/index.php  ← AJAX delete
│   └── profile/index.php
│
├── public/
│   ├── css/style.css
│   ├── js/main.js
│   └── uploads/images/  ← uploaded images go here
│
└── index.php            ← Main router (MVC dispatcher)
```

---

## Features Implemented

### Authentication
- [x] Author login with role check + approval check
- [x] PHP sessions with role stored
- [x] Secure logout

### Articles
- [x] Create articles (title, body, excerpt, image, category, tags, series)
- [x] Save as Draft or Submit for Review
- [x] Edit drafts and revision-requested articles
- [x] Submit article for editorial review
- [x] Unpublish a published article
- [x] View revision history
- [x] Restore a previous revision
- [x] AJAX autosave (every 5s of inactivity, every 30s background)
- [x] AJAX article list filter by status

### Series
- [x] Create, edit, delete series
- [x] Upload cover image
- [x] AJAX add published articles to series
- [x] AJAX remove articles from series
- [x] AJAX update article order within series

### Analytics
- [x] Dashboard: total published, total views, followers, likes, comments
- [x] Most read and most liked articles
- [x] Per-article: views, likes, comments
- [x] Daily reads over 30 days (canvas bar chart)
- [x] Follower growth over 30 days (canvas bar chart)

### Comments
- [x] Unified inbox across all articles (newest first)
- [x] Reply to comments (form POST)
- [x] AJAX delete comments on own articles

### Profile
- [x] Edit display name, bio, social links (Twitter, LinkedIn, GitHub)
- [x] Upload profile picture
- [x] Change password (with current password verification)
- [x] View follower list

---

## AJAX Features (XMLHttpRequest + JSON)

| Feature | Method | Endpoint |
|---|---|---|
| Autosave article | POST | `?page=articles&action=autosave` |
| Filter articles by status | GET | `?page=articles&action=ajaxlist&status=X` |
| Add article to series | POST | `?page=series&action=addarticle` |
| Remove article from series | POST | `?page=series&action=removearticle` |
| Update series article order | POST | `?page=series&action=updateorder` |
| Delete comment | POST | `?page=comments&action=delete` |

All AJAX endpoints return `{"success": true/false, ...}` JSON responses.

---

## Database Tables Used by Author Module

| Table | Purpose |
|---|---|
| `users` | Author login and profile |
| `articles` | Article CRUD + status workflow |
| `article_revisions` | Revision history and restore |
| `article_tags` + `tags` | Tag management |
| `categories` | Category assignment |
| `series` | Series creation/management |
| `likes` | Like counts per article |
| `comments` | Comment inbox, reply, delete |
| `follows` | Follower list + growth |
| `reading_history` | Daily read tracking for analytics |
| `editorial_calendar` | Read-only scheduled publish dates |

---

## Security Notes
- All DB queries use `mysqli` **prepared statements** (no SQL injection)
- `htmlspecialchars()` on all output (no XSS)
- `password_hash()` / `password_verify()` for passwords
- Session role checked on every controller via `requireAuthor()`
- File uploads validated by MIME type and size (max 5MB)
- Authors can only edit/delete their own content (author_id checked in every query)
