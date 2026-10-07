<?php

namespace Tests\Unit\Helper;

use Tests\TestCase;

class ValidationHelperTest extends TestCase
{
    public function testValidateDateTimeReturnsTrueOnValidValue()
    {
        $this->assertTrue(validate_datetime(date('Y-m-d H:i:s')));
    }

    public function testValidateDateTimeReturnsFalseOnInvalidValue()
    {
        $this->assertFalse(validate_datetime('invalid'));
    }

    public function testValidateImageDataUrlAcceptsBase64Images()
    {
        $this->assertTrue(validate_image_data_url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGNgYGD4DwABBAEAwS2OUAAAAABJRU5ErkJggg=='));
        $this->assertTrue(validate_image_data_url('data:image/jpeg;base64,/9j/4AAQSkZJRg=='));
        $this->assertTrue(validate_image_data_url('data:image/webp;base64,UklGRhIAAABXRUJQ'));
    }

    public function testValidateImageDataUrlRejectsSvgMarkupAndUrls()
    {
        $this->assertFalse(validate_image_data_url('data:image/svg+xml;base64,PHN2Zz48L3N2Zz4='));
        $this->assertFalse(validate_image_data_url('data:image/png;base64,"><script>alert(1)</script>'));
        $this->assertFalse(validate_image_data_url('https://example.org/logo.png'));
        $this->assertFalse(validate_image_data_url(''));
    }
}
