# ALTCHA Integration

ALTCHA is a privacy-focused, open-source CAPTCHA alternative that uses proof-of-work challenges to verify human users without tracking. Unlike traditional CAPTCHAs, ALTCHA doesn't rely on third-party services or require users to solve visual puzzles.

## How It Works

1. When a form is loaded, the frontend requests a challenge from the server
2. The user's browser solves the proof-of-work challenge (finding a number that produces a specific hash)
3. The solution is sent back with the form submission
4. The server verifies the solution using HMAC signatures

This approach is:
- **Privacy-friendly**: No external services, no tracking
- **GDPR-compliant**: All processing happens on your server
- **Accessible**: No visual puzzles to solve
- **Bot-resistant**: Requires computational work that's trivial for humans but costly for automated attacks

## Requirements

The ALTCHA integration requires the `altcha-org/altcha` PHP package. Install it via Composer:

```bash
composer require altcha-org/altcha
```

## Configuration

### 1. Access ALTCHA Settings

Navigate to **Settings → Integrations → ALTCHA** in the Easy!Appointments backend.

### 2. Generate HMAC Key

Click the **Generate** button to create a secure random HMAC key. This key is used to sign and verify challenges. Keep this key secret.

### 3. Configure Options

| Setting | Description | Default |
|---------|-------------|---------|
| **Enable ALTCHA** | When enabled (and CAPTCHA is required), ALTCHA replaces the image CAPTCHA | Disabled |
| **HMAC Key** | Secret key for signing challenges (required) | Empty |
| **Max Number** | Maximum number for proof-of-work (higher = harder) | 100,000 |
| **Expires** | Challenge validity in seconds | 300 (5 min) |

### 4. Enable CAPTCHA Requirement

ALTCHA only activates when CAPTCHA is required. Go to **Settings → General** and enable **Require CAPTCHA**.

## Testing Locally

### Using Docker

No additional Docker services are required. ALTCHA runs entirely within the PHP application.

1. Start the development environment:
   ```bash
   docker-compose up -d
   ```

2. Install Composer dependencies (including ALTCHA):
   ```bash
   docker-compose exec php-fpm composer install
   ```

3. Run database migrations:
   ```bash
   docker-compose exec php-fpm php index.php console migrate
   ```

4. Access the application at `http://localhost` and configure ALTCHA in Settings.

### Verify Installation

1. Go to **Settings → Integrations → ALTCHA**
2. Click **Generate** to create an HMAC key
3. Enable ALTCHA
4. Save settings
5. Go to **Settings → General** and enable **Require CAPTCHA**
6. Open the booking page in an incognito window
7. Complete the booking wizard - on the final step, you should see the ALTCHA verification widget instead of the image CAPTCHA

## Affected Pages

When ALTCHA is enabled, it replaces the image CAPTCHA on:

- Booking page (final confirmation step)
- Login page
- Password recovery page
- Password reset page

## Troubleshooting

### "ALTCHA verification failed"

- Ensure the HMAC key is set and saved
- Check that the challenge hasn't expired (increase the expiry time if needed)
- Verify the `altcha-org/altcha` package is installed

### Challenge not loading

- Check browser console for JavaScript errors
- Verify the `/captcha/altcha_challenge` endpoint returns a valid JSON response
- Ensure ALTCHA is enabled in settings

### Package not found

If you see errors about missing `AltchaOrg\Altcha\Altcha` class:

```bash
composer require altcha-org/altcha
composer dump-autoload
```

## Security Considerations

- **Keep the HMAC key secret**: It's used to sign challenges. If compromised, regenerate it immediately.
- **Set appropriate difficulty**: The default max number (100,000) provides good security. Lower values make challenges easier (faster but less secure).
- **Set reasonable expiry**: 5 minutes (300 seconds) is usually sufficient. Shorter times increase security but may frustrate slow users.

## API Endpoint

### GET /captcha/altcha_challenge

Returns a new ALTCHA challenge for client-side solving.

**Response:**
```json
{
  "algorithm": "SHA-256",
  "challenge": "abc123...",
  "maxnumber": 100000,
  "salt": "xyz789...",
  "signature": "sig..."
}
```

## Further Reading

- [ALTCHA Official Website](https://altcha.org/)
- [ALTCHA PHP Library](https://github.com/altcha-org/altcha-lib-php)
- [Proof-of-Work Concept](https://en.wikipedia.org/wiki/Proof_of_work)

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
