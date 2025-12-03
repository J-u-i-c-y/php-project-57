<?php

namespace Tests\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testBasicExample(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
