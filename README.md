# Craftyvok

Laravel storefront for personalised gifts, Instagram videos/highlights, guest gift enquiries, and Google review links.

## Run locally

```powershell
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

Open `http://127.0.0.1:8000`.

To load the sample gift catalogue for the Love, Birthday, Occasions, Relationship, Corporate, and Greetings menus, run:

```powershell
php artisan db:seed --class=DemoGiftSeeder
```

## Admin and gift catalogue

Set `ADMIN_NAME`, `ADMIN_EMAIL`, and a strong `ADMIN_PASSWORD` in `.env`, then create the admin account:

```powershell
php artisan db:seed --class=AdminUserSeeder
```

Sign in at `/admin/login`. The dashboard lets the admin manage gift items and prices, and reply to guest chat conversations. Visitors do not need an account; their chat is linked to a random browser token.

Shoppers can create an account or sign in at `/register` and `/login`. The header's **My Orders** and bag links open `/orders`, which only shows orders linked to the signed-in customer. Admins can add orders for a registered customer's email and update delivery status from the admin dashboard; customers see those updates in their account.

## Instagram sync

Connect a professional Instagram account to the Meta Graph API and set `INSTAGRAM_USER_ID` and `INSTAGRAM_ACCESS_TOKEN` in `.env`. Then run:

```powershell
php artisan craftyvok:sync-instagram
```

Videos/Reels and story highlights are synced to the website. Scheduled sync is every 30 minutes; keep Laravel's scheduler running locally with `php artisan schedule:work` or configure Laravel's scheduler on the production server.

To load the Craftyvok Instagram post links supplied for the video section, run:

```powershell
php artisan db:seed --class=InstagramVideoSeeder
```

## Google reviews

Set `GOOGLE_REVIEW_URL` to the official Google Business Profile review link. Customers are sent to Google to submit their review; the website does not submit reviews on a customer's behalf.

## Tests

```powershell
php artisan test
```
