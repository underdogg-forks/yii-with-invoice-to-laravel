<?php

namespace Tests\Unit\Enums;

use App\Enums\HttpMethod;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(HttpMethod::class)]
class HttpMethodTest extends TestCase
{
    #[Test]
    public function it_has_correct_get_value(): void
    {
        /* Arrange & Act */
        $method = HttpMethod::GET;

        /* Assert */
        $this->assertEquals('GET', $method->value);
    }

    #[Test]
    public function it_has_correct_post_value(): void
    {
        /* Arrange & Act */
        $method = HttpMethod::POST;

        /* Assert */
        $this->assertEquals('POST', $method->value);
    }

    #[Test]
    public function it_has_correct_put_value(): void
    {
        /* Arrange & Act */
        $method = HttpMethod::PUT;

        /* Assert */
        $this->assertEquals('PUT', $method->value);
    }

    #[Test]
    public function it_has_correct_patch_value(): void
    {
        /* Arrange & Act */
        $method = HttpMethod::PATCH;

        /* Assert */
        $this->assertEquals('PATCH', $method->value);
    }

    #[Test]
    public function it_has_correct_delete_value(): void
    {
        /* Arrange & Act */
        $method = HttpMethod::DELETE;

        /* Assert */
        $this->assertEquals('DELETE', $method->value);
    }

    #[Test]
    public function it_has_correct_head_value(): void
    {
        /* Arrange & Act */
        $method = HttpMethod::HEAD;

        /* Assert */
        $this->assertEquals('HEAD', $method->value);
    }

    #[Test]
    public function it_has_correct_options_value(): void
    {
        /* Arrange & Act */
        $method = HttpMethod::OPTIONS;

        /* Assert */
        $this->assertEquals('OPTIONS', $method->value);
    }

    #[Test]
    public function it_can_be_created_from_string(): void
    {
        /* Arrange & Act */
        $method = HttpMethod::from('GET');

        /* Assert */
        $this->assertSame(HttpMethod::GET, $method);
        $this->assertEquals('GET', $method->value);
    }

    #[Test]
    public function it_has_all_standard_http_methods(): void
    {
        /* Arrange */
        $expectedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

        /* Act */
        $cases = HttpMethod::cases();
        $actualMethods = array_map(fn($case) => $case->value, $cases);

        /* Assert */
        $this->assertEquals($expectedMethods, $actualMethods);
        $this->assertCount(7, $cases);
    }

    #[Test]
    public function it_can_be_used_in_match_expressions(): void
    {
        /* Arrange */
        $method = HttpMethod::POST;

        /* Act */
        $result = match($method) {
            HttpMethod::GET => 'read',
            HttpMethod::POST => 'create',
            HttpMethod::PUT, HttpMethod::PATCH => 'update',
            HttpMethod::DELETE => 'delete',
            default => 'other'
        };

        /* Assert */
        $this->assertEquals('create', $result);
    }
}
