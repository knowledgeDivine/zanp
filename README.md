# ZANP: 3D shoe store (XAMPP, PHP + MySQL)

Requires XAMPP with PHP 8.1 or newer.

## Setup (5 steps)
1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Copy this folder to `C:\xampp\htdocs\` and keep the name **zanp** (so you get `C:\xampp\htdocs\zanp`).
3. Open http://localhost/phpmyadmin, click **SQL**, paste all of `database.sql`, click **Go**.
4. Open http://localhost/zanp/
5. Admin: http://localhost/zanp/admin/ with `admin@zanp.com` / `Zanp@2026`. Change this password after you log in (create a new admin user in phpMyAdmin or edit the `users` table).

If you use a different folder name, change `BASE_URL` in `config/db.php`. If MySQL has a password, set `DB_PASS` there too.

## What is inside
- Home: interactive 3D hero (drag, mouse parallax, colour dots) and a scroll-driven 3D story.
- Shop: live AJAX search and category filter. Thumbnails are rendered from the 3D model in the browser.
- Product page: drag-to-rotate 3D viewer, zoom, Side/Front/Top/Back camera buttons, colourways, sizes, add to bag.
- Bag, checkout (demo payment, stock is reduced), order confirmation, accounts, contact form, newsletter.
- Admin: overview, products (add/edit/delete, photo and .glb upload), orders (status), messages, settings (name, tagline, accent colour).
- Security: PDO prepared statements, escaped output, CSRF tokens, hashed passwords, login throttling, upload checks.

## Troubleshooting
- **Blank 3D area:** open the browser console. Make sure Apache is running and `assets/models/shoe.glb` opens at http://localhost/zanp/assets/models/shoe.glb.
- **Database error on every page:** import `database.sql` first, and check the values in `config/db.php`.
- **Upload fails:** raise `upload_max_filesize` and `post_max_size` in `php.ini`, then restart Apache.
- **Port 80 busy:** change Apache's port in XAMPP and use `http://localhost:8080/zanp/`.

## About the products
All products reuse one 3D shoe model in three real colourways plus tints. To show different shoes, upload your own .glb for a product in the admin, or add photos. See CREDITS.md for licences.
