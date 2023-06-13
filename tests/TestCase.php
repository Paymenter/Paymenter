<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected bool $seed = true;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }
}
