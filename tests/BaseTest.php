<?php

namespace AndersonScherdovski\Base\Tests;

use AndersonScherdovski\Base\Facades\Base;
use AndersonScherdovski\Base\ServiceProvider;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BaseTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Base' => Base::class,
        ];
    }

    #[Test]
    public function it_resolves_the_base_binding(): void
    {
        $this->assertInstanceOf(\AndersonScherdovski\Base\Base::class, $this->app->make('base'));
    }

    #[Test]
    public function it_merges_the_package_config(): void
    {
        $this->assertIsArray(config('base'));
    }
}
