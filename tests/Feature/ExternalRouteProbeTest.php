<?php
namespace Tests\Feature;

use Tests\TestCase;

class ExternalRouteProbeTest extends TestCase
{
    public function test_probe_alive_is_200()
    {
        $resp = $this->get("/_probe_alive");
        fwrite(STDERR, ">> ALIVE_STATUS=".$resp->getStatusCode().PHP_EOL);
        $resp->assertStatus(200);
    }

    public function test_probe_login_view_status()
    {
        $resp = $this->get("/_probe_login_view");
        fwrite(STDERR, ">> LOGIN_VIEW_STATUS=".$resp->getStatusCode().PHP_EOL);
        fwrite(STDERR, ">> LOGIN_VIEW_LEN=".strlen($resp->getContent()).PHP_EOL);
        $this->assertTrue(true); // s? queremos os n?meros
    }
}
