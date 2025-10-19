<?php
namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class StackProbeTest extends TestCase
{
    public function test_route_throws_real_exception_when_handler_is_disabled()
    {
        // rota m?nima, sem view/blade
        Route::middleware("web")->get("/_probe_plain", fn () => response("PING", 200));

        // Desliga o ExceptionHandler para a exce??o "vazar" no teste
        $this->withoutExceptionHandling();

        // Se algo no pipeline estourar, veremos a exception/stack aqui
        $resp = $this->get("/_probe_plain");
        fwrite(STDERR, ">> STATUS=".$resp->getStatusCode().PHP_EOL);

        $resp->assertStatus(200);
    }
}
