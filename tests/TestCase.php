<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function postWithCsrf($uri, array $data = [], array $headers = [])
    {
        return $this->post($uri, array_merge($data, ['_token' => csrf_token()]), $headers);
    }

    protected function putWithCsrf($uri, array $data = [], array $headers = [])
    {
        return $this->put($uri, array_merge($data, ['_token' => csrf_token()]), $headers);
    }

    protected function deleteWithCsrf($uri, array $data = [], array $headers = [])
    {
        return $this->delete($uri, array_merge($data, ['_token' => csrf_token()]), $headers);
    }
}
