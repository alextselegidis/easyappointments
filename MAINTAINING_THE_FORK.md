# Maintaining this fork

This is a fork of [alextselegidis/easyappointments](https://github.com/alextselegidis/easyappointments),
currently tracking **1.6.0**, with ScheduCal customizations on top.

## Updating to a new upstream release

The repository was reparented onto upstream history on 2026-08-22, so a real
three-way merge works. It previously had no common ancestor with upstream (a
squashed import of 3 commits), which meant every update required diffing release
tarballs and re-applying changes by hand.

```bash
git fetch upstream --tags
git checkout -b chore/upgrade-X.Y.Z
git merge X.Y.Z
```

Conflicts land only in files both sides changed. The 1.6.0 merge conflicted in 9:
`Ics_file.php`, `config.php`, `Appointments.php`, `EA_Controller.php`,
`translations_lang.php`, `installation.php`, `integrations.php`, `ci.yml`,
`CHANGELOG.md`. Everything else merged automatically.

If `upstream` is missing:

```bash
git remote add upstream https://github.com/alextselegidis/easyappointments
git fetch upstream --tags
```

Pre-graft history is preserved on `archive/pre-graft`.

### Opening the PR

`gh pr create` resolves against the **parent** repo by default and fails with
"No commits between main and …". Target this fork explicitly:

```bash
gh pr create -R CalendarUtilityService/easyappointments \
  --base main --head CalendarUtilityService:chore/upgrade-X.Y.Z
```

## Customizations that need defending during a merge

- **`Ics_file.php`** — stable `ics_uid` (RFC 5545 §3.8.4.7) and stored
  `ics_sequence` (§3.8.7.4). Upstream derives SEQUENCE from `update_datetime`;
  ours is deliberate and is the product differentiator. Keep ours.
- **`Ics_calendar.php` and `Ics_provider.php`** — deleted by upstream in 1.6.0,
  **deliberately retained here**. `get_cancel_stream()` calls
  `$calendar->setMethod('CANCEL')` to emit `METHOD:CANCEL` (§3.7.2), and the
  vendored `Jsvrcek\ICS\Model\Calendar` has no `setMethod()`. Removing them makes
  cancellation ICS generation fatal at runtime.
- **`config.php`** — the `BASE_URL` environment override is required on Azure App
  Service. It already wraps upstream's protocol detection.
- **`Appointments.php`** — the redirect target is `booking/reschedule/`, not
  upstream's `booking/`. `Notifications.php` and `Google_sync.php` depend on it.
- **`booking_cancellation_frame.php`** — the manage-appointment button reads
  "Cancel", not upstream's "Delete", to match its own hint text and the
  "Cancel Appointment" dialog it opens.
- **`Graph_mailer.php` / `Email_messages.php`** — mail goes via Microsoft Graph,
  not SMTP, because the tenant blocks legacy auth. Do not reintroduce nodemailer
  or SMTP.

## Migrations

**Deploy the code first, then migrate.** Migrating before the new code is live
applies the *old* numbering and records a version that causes the new migrations
to be skipped. During the 1.6.0 upgrade this would have set version 62 under the
old numbering, skipping upstream's real 061 and 062 and running 063+ against a
schema missing a column rename and the `meeting_link` column.

ScheduCal migrations are numbered from **070** to stay clear of upstream. When
upstream claims those numbers, renumber ours upward again — the class names do
not encode the number, so renaming the file is sufficient.

**Do not trust `ea_migrations.version` alone.** The demo database read 60 while
already having `ics_uid`, `ics_sequence` and the `scheducal_*` settings applied
by hand. Check the actual schema.

Migrations must be idempotent, because they can re-run against a schema that
already has their changes. `071` guards its columns with `field_exists()` and
only backfills rows where `ics_uid` is NULL or empty — an earlier version rewrote
every row, which would regenerate UIDs already issued to calendar clients and
turn the next update into a duplicate event instead of a revision.

## Running console commands on App Service

`php index.php console migrate` is CLI-only. Two approaches that do **not** work:

- Kudu's `/api/command` — the SCM container runs Node, with no PHP.
- `az webapp ssh` with piped input — the interactive shell swallows the command
  and the tunnel times out.

What works is a TCP tunnel plus a real ssh client with a command argument:

```bash
az webapp create-remote-connection -g rg-schedcal-prod-wus2-01 \
  -n app-schedcal-demo-wus2-01 --port 8022 &

# the container's ssh password is literally: Docker!
printf '#!/bin/sh\necho "Docker!"\n' > /tmp/askpass.sh && chmod 700 /tmp/askpass.sh
SSH_ASKPASS=/tmp/askpass.sh SSH_ASKPASS_REQUIRE=force DISPLAY=:0 \
setsid -w ssh -p 8022 -o StrictHostKeyChecking=no -o PreferredAuthentications=password \
  root@127.0.0.1 'cd /home/site/wwwroot && php index.php console migrate'
```

## Database access

`mysql-schedcal-demo-cus-01` allows only Azure services. To query it directly,
add a temporary firewall rule for your IP and remove it afterwards:

```bash
az mysql flexible-server firewall-rule create -g rg-schedcal-prod-wus2-01 \
  -n mysql-schedcal-demo-cus-01 --rule-name temp --start-ip-address <ip> --end-ip-address <ip>
# ... work ...
az mysql flexible-server firewall-rule delete -g rg-schedcal-prod-wus2-01 \
  -n mysql-schedcal-demo-cus-01 --rule-name temp --yes
```

Always take a dump before migrating:

```bash
mysqldump --host=... --ssl-mode=REQUIRED --single-transaction easyappointments > backup.sql
```
