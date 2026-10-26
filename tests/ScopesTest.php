<?php

namespace AndersonScherdovski\Base\Tests;

use AndersonScherdovski\Base\Scopes\FilterScope;
use AndersonScherdovski\Base\Scopes\OrderScope;
use AndersonScherdovski\Base\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ScopesTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Widget::query()->insert([
            ['name' => 'alpha'],
            ['name' => 'beta'],
            ['name' => 'gamma'],
        ]);
    }

    #[Test]
    public function filter_scope_applies_an_equality_filter(): void
    {
        request()->merge(['filters' => ['name = beta']]);

        $names = Widget::query()->withGlobalScope('filter', new FilterScope())->pluck('name');

        $this->assertSame(['beta'], $names->all());
    }

    #[Test]
    public function filter_scope_applies_a_like_filter(): void
    {
        request()->merge(['filters' => ['name LIKE amm']]);

        $names = Widget::query()->withGlobalScope('filter', new FilterScope())->pluck('name');

        $this->assertSame(['gamma'], $names->all());
    }

    #[Test]
    public function filter_scope_skips_malformed_filters(): void
    {
        request()->merge(['filters' => ['name']]);

        $names = Widget::query()->withGlobalScope('filter', new FilterScope())->pluck('name');

        $this->assertCount(3, $names);
    }

    #[Test]
    public function order_scope_sorts_descending_by_default(): void
    {
        $names = Widget::query()->withGlobalScope('order', new OrderScope())->pluck('name');

        $this->assertSame(['gamma', 'beta', 'alpha'], $names->all());
    }

    #[Test]
    public function order_scope_honours_an_explicit_direction(): void
    {
        request()->merge(['sort' => 'name asc']);

        $names = Widget::query()->withGlobalScope('order', new OrderScope())->pluck('name');

        $this->assertSame(['alpha', 'beta', 'gamma'], $names->all());
    }

    #[Test]
    public function order_scope_defaults_the_direction_when_omitted(): void
    {
        request()->merge(['sort' => 'name']);

        $names = Widget::query()->withGlobalScope('order', new OrderScope())->pluck('name');

        $this->assertSame(['alpha', 'beta', 'gamma'], $names->all());
    }
}

class Widget extends Model
{
    protected $table = 'widgets';

    public $timestamps = false;

    protected $guarded = [];
}
