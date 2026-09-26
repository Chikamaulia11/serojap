<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Asset hasil build Vite tidak ikut di-commit, jadi test tidak
        // perlu manifest public/build untuk bisa render halaman.
        $this->withoutVite();
    }
}
