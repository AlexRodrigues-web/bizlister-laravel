<?php
namespace Tests\Feature;

use Tests\TestCase;

class RouteSpyTest extends TestCase
{
    public function test_login_route_spy()
    {
        // 1) Rota resolvida
        $matched = app("router")->getRoutes()->match(request()->create("/login"));
        $name    = $matched ? $matched->getName() : null;
        fwrite(STDERR, ">> ROUTE_NAME=".($name ?? "NULL").PHP_EOL);

        // 2) URL pelo nome
        try {
            $url = route("login", [], false);
            fwrite(STDERR, ">> URL_BY_NAME=".$url.PHP_EOL);
        } catch (\Throwable $e) {
            fwrite(STDERR, ">> URL_BY_NAME=EXCEPTION: ".$e->getMessage().PHP_EOL);
        }

        // 3) GET real e dump parcial do body
        $resp = $this->get("/login");
        $status = $resp->getStatusCode();
        $body   = $resp->getContent();
        fwrite(STDERR, ">> STATUS_GET_LOGIN=".$status.PHP_EOL);
        fwrite(STDERR, ">> BODY_LEN=".strlen((string)$body).PHP_EOL);
        fwrite(STDERR, ">> BODY_SNIPPET=".substr((string)$body,0,500).PHP_EOL);

        $resp->assertStatus(200);
    }
}

