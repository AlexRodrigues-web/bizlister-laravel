<?php
namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class WhichMiddlewareTest extends TestCase
{
    public function test_dump_middleware_stack_for_probe()
    {
        $r = app('router')->getRoutes()->match(request()->create('/_probe_alive'));
        $mw = app('router')->gatherRouteMiddleware($r);
        fwrite(STDERR, ">> STACK=".implode(' | ', array_map('strval',$mw)).PHP_EOL);
        $this->assertTrue(true);
    }
}
