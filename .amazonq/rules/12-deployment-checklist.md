# Deployment Checklist

Always run before going live:

- [ ] `php artisan config:clear`
- [ ] `php artisan cache:clear`
- [ ] `php artisan view:clear`
- [ ] `php artisan route:clear`
- [ ] `php artisan migrate --force`
- [ ] `php artisan storage:link`
- [ ] Verify `.env` values match production (DB credentials, `APP_URL`, mail settings, `APP_ENV=production`, `APP_DEBUG=false`)

If something breaks after deploy, check `10-debugging-guide.md` Step 4 (Environment) and `11-common-errors-log.md` (Deployment section) first — most post-deploy issues are a stale cache or a missing storage symlink.
