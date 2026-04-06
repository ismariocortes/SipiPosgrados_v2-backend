<?php

namespace Tests\Unit;

use App\Support\ApiResponse;
use PHPUnit\Framework\TestCase;

class ApiResponseClassTest extends TestCase
{
    public function test_api_response_helper_is_available(): void
    {
        $this->assertTrue(class_exists(ApiResponse::class));
    }
}
