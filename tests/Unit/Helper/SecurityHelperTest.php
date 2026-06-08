<?php

namespace Tests\Unit\Helper;

use Tests\TestCase;

class SecurityHelperTest extends TestCase {
    public function testNormalizeEmbedOriginAcceptsValidHttpsOrigin(): void
    {
        $this->assertSame('https://www.example.com', normalize_embed_origin('https://www.example.com/'));
    }

    public function testNormalizeEmbedOriginAcceptsOriginWithPort(): void
    {
        $this->assertSame('http://localhost:3000', normalize_embed_origin('http://localhost:3000'));
    }

    public function testNormalizeEmbedOriginRejectsPath(): void
    {
        $this->assertNull(normalize_embed_origin('https://www.example.com/booking'));
    }

    public function testNormalizeEmbedOriginRejectsInvalidScheme(): void
    {
        $this->assertNull(normalize_embed_origin('javascript:alert(1)'));
    }

    public function testNormalizeEmbedOriginAcceptsBareHostname(): void
    {
        $this->assertSame('https://embed-demo.us', normalize_embed_origin('embed-demo.us'));
    }

    public function testNormalizeEmbedOriginRejectsBareInvalidHostname(): void
    {
        $this->assertNull(normalize_embed_origin('invalid'));
    }

    public function testSanitizeEmbedAllowedOriginsDeduplicatesAndNormalizes(): void
    {
        $raw = "https://www.example.com/\nhttps://www.example.com\nhttp://localhost:3000\ninvalid";

        $this->assertSame(
            "https://www.example.com\nhttp://localhost:3000",
            sanitize_embed_allowed_origins($raw),
        );
    }

    public function testSanitizeEmbedAllowedOriginsAcceptsCommaSeparatedValues(): void
    {
        $raw = 'https://embed-demo.us, https://partner.example.nl/';

        $this->assertSame(
            "https://embed-demo.us\nhttps://partner.example.nl",
            sanitize_embed_allowed_origins($raw),
        );
    }

    public function testGetEmbedAllowedOriginsReturnsEmptyArrayWhenConstantIsUnset(): void
    {
        $this->assertSame([], get_embed_allowed_origins());
    }

    public function testIssueBookingCsrfTokenVerifiesSuccessfully(): void
    {
        $token = issue_booking_csrf_token();

        $this->assertSame(64, strlen($token));
        $this->assertTrue(verify_booking_csrf_token_string($token));
    }

    public function testVerifyBookingCsrfTokenStringRejectsExpiredToken(): void
    {
        $timestamp = str_pad(dechex(time() - 7201), 8, '0', STR_PAD_LEFT);
        $nonce = bin2hex(random_bytes(12));
        $payload = $timestamp . $nonce;
        $mac = substr(hash_hmac('sha256', $payload, booking_csrf_secret()), 0, 32);

        $this->assertFalse(verify_booking_csrf_token_string($payload . $mac));
    }

    public function testVerifyBookingCsrfTokenStringRejectsTamperedToken(): void
    {
        $token = issue_booking_csrf_token();
        $tampered = substr($token, 0, 63) . ($token[63] === 'a' ? 'b' : 'a');

        $this->assertFalse(verify_booking_csrf_token_string($tampered));
    }

    public function testIsBookingFramingRouteReturnsTrueOnBookingController(): void
    {
        $CI = &get_instance();
        $CI->router->class = 'booking';

        $this->assertTrue(is_booking_framing_route());
    }

    public function testIsBookingFramingRouteReturnsFalseOnBackendController(): void
    {
        $CI = &get_instance();
        $CI->router->class = 'general_settings';

        $this->assertFalse(is_booking_framing_route());
    }
}
