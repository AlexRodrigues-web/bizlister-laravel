<?php
namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;

class ResponseFactoryProbeTest extends TestCase
{
    public function test_direct_response_object_is_200()
    {
        // Usa Response "puro", sem passar pelo ResponseFactory::view
        $resp = new Response(view("auth.login")->render(), 200);
        fwrite(STDERR, ">> DIRECT_OBJ_STATUS=".$resp->getStatusCode().PHP_EOL);
        $this->assertSame(200, $resp->getStatusCode());
    }

    public function test_helper_response_view_is_404_if_factory_is_overridden()
    {
        // Usa o helper response()->view(...) que passa pelo ResponseFactory (suspeito)
        $resp = response()->view("auth.login");
        fwrite(STDERR, ">> FACTORY_VIEW_STATUS=".$resp->getStatusCode().PHP_EOL);
        $this->assertSame(200, $resp->getStatusCode()); // deve falhar se a macro estiver for?ando 404
    }
}

