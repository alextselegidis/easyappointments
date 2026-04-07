# ALTCHA Integration

ALTCHA is a spam-protection tool that replaces traditional image CAPTCHAs (those "pick the traffic lights" puzzles). Instead of making users solve visual puzzles, it runs a quick background calculation in the browser to verify the user is human.

**Why use it?**

- No annoying image puzzles for your users
- No data sent to third-party services (privacy-friendly and GDPR-compliant)
- Works automatically — users just see a simple checkbox

## Setup

### 1. Install the PHP Package

Run this command in your Easy!Appointments folder:

```bash
composer require altcha-org/altcha
```

### 2. Enable CAPTCHA

Go to **Settings** → **General** and turn on **Require CAPTCHA**.

### 3. Configure ALTCHA

Go to **Settings** → **Integrations** → **ALTCHA**:

1. Click **Generate** to create a secret key.
2. Turn on **Enable ALTCHA**.
3. Click **Save**.

That's it! The image CAPTCHA will now be replaced by ALTCHA on all public pages.

### Settings Explained

| Setting | What It Does | Default |
|---------|-------------|---------|
| **Enable ALTCHA** | Turns ALTCHA on (replaces image CAPTCHA) | Off |
| **HMAC Key** | Secret key used to create challenges (click Generate) | Empty |
| **Max Number** | How hard the challenge is (higher = harder) | 100,000 |
| **Expires** | How long a challenge stays valid | 300 seconds (5 min) |

## Where It Appears

When enabled, ALTCHA replaces the image CAPTCHA on:

- The booking page (final confirmation step)
- The login page
- The password recovery page
- The password reset page

## Troubleshooting

**"ALTCHA verification failed"**

- Make sure you clicked **Generate** and **saved** the HMAC key.
- The challenge may have expired — try increasing the expiry time.
- Verify the PHP package is installed: `composer require altcha-org/altcha`

**Challenge not loading**

- Open your browser's developer tools (F12 → Console) and look for errors.
- Check that ALTCHA is enabled in Settings.

**Package not found error**

Run:
```bash
composer require altcha-org/altcha
composer dump-autoload
```

## Security Tips

- **Keep the HMAC key secret.** If you think it's been exposed, click **Generate** to create a new one.
- **Don't lower Max Number too much.** The default (100,000) gives good protection. Lower values are faster but less secure.
- **5 minutes expiry is usually fine.** Only increase it if users have very slow connections.

## Further Reading

- [ALTCHA Official Website](https://altcha.org/)
- [ALTCHA PHP Library](https://github.com/altcha-org/altcha-lib-php)

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
