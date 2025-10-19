<?php
namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AliveMiddlewareIsolationTest extends TestCase
{
    public function test_env_and_route_match_and_stack()
    {
        $this->assertTrue(app()->environment('testing'), 'APP_ENV n?o ? testing');
        $router = app('router');
        $r = $router->getRoutes()->match(request()->create('/_probe_alive', 'GET'));
        $mw = $router->gatherRouteMiddleware($r);
        fwrite(STDERR, ">> MATCHED_URI=".$r->uri().PHP_EOL);
        fwrite(STDERR, ">> MW_STACK=".implode(' | ', array_map('strval',$mw)).PHP_EOL);
        $this->assertTrue(true);
    }

    public function test_probe_alive_without_middleware_is_200()
    {
        $this->withoutMiddleware();
        $resp = $this->get('/_probe_alive');
        fwrite(STDERR, ">> NO_MW_STATUS=".$resp->getStatusCode().PHP_EOL);
        $resp->assertStatus(200);
    }

    public function test_probe_alive_with_middleware_logs_details()
    {
        $resp = $this->get('/_probe_alive');
        fwrite(STDERR, ">> WITH_MW_STATUS=".$resp->getStatusCode().PHP_EOL);
        fwrite(STDERR, ">> WITH_MW_HEADERS=".json_encode($resp->headers->all()).PHP_EOL);
        $body = $resp->getContent();
        fwrite(STDERR, ">> WITH_MW_SNIP=".substr(preg_replace('/\s+/', ' ', strip_tags($body)),0,200).PHP_EOL);
        $this->assertTrue(true);
    }
}
