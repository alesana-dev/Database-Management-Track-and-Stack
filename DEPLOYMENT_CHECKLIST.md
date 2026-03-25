# Deployment Checklist (TRIPAC-FMIS)

Use this list before each production deploy.

## Environment
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` matches the real domain
- [ ] `APP_KEY` is set
- [ ] `SUPER_ADMIN_USERNAME` and `SUPER_ADMIN_PASSWORD` are set

## Database & Migrations
- [ ] Run `php artisan migrate --force`
- [ ] Run `php artisan schema:audit` and confirm OK
- [ ] Verify `audit_logs` has all required columns

## Users & Access
- [ ] Super Admin account exists and can log in
- [ ] Default passwords removed/changed
- [ ] Public registration disabled

## Storage & Cache
- [ ] `storage` and `bootstrap/cache` are writable
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`

## Assets
- [ ] `npm run build` completed
- [ ] `public/build` exists and is deployed

## Security
- [ ] HTTPS enabled
- [ ] Session/cookie settings reviewed
- [ ] Backups configured

## Smoke Tests
- [ ] Login as Super Admin
- [ ] Create Chapter Admin
- [ ] Edit Member
- [ ] Export PDF
