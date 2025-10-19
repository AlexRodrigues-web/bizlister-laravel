<?php
namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LayoutProbeTest extends TestCase
{
    public function test_render_login_view_directly()
    {
        Route::middleware("web")->get("/_probe_login_view", function () {
            return response()->view("auth.login"); // mesma view do controller
        });

        $resp = $this->get("/_probe_login_view");
        fwrite(STDERR, ">> PROBE_STATUS=".$resp->getStatusCode().PHP_EOL);
        $resp->assertStatus(200);
    }
}

