<?php
// Força ambiente de teste só neste processo:
putenv('APP_ENV=testing'); $_ENV['APP_ENV']='testing'; $_SERVER['APP_ENV']='testing';

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

/** @var \Illuminate\Routing\Router $router */
$router = $app['router'];

// Requisição de teste
$req = Illuminate\Http\Request::create('/_probe_alive','GET');

// 1) Matching de rota (sem executar nada)
try {
  $m = $router->getRoutes()->match($req);
  echo "MATCHED=" . ($m ? $m->uri() : 'NULL') . PHP_EOL;
  echo "MW=" . implode(',', $router->gatherRouteMiddleware($m)) . PHP_EOL;
} catch (Throwable $e) {
  echo "MATCH_EX=" . $e->getMessage() . PHP_EOL;
}

// 2) Passa pelo HTTP Kernel (com middleware)
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$res = $kernel->handle($req);
echo "KERNEL_STATUS=" . $res->getStatusCode() . PHP_EOL;
echo "KERNEL_LEN=" . strlen($res->getContent()) . PHP_EOL;

// 3) Bypass do Kernel: despacha direto no Router (sem middleware globais)
$res2 = $router->dispatch($req);
echo "ROUTER_STATUS=" . $res2->getStatusCode() . PHP_EOL;
echo "ROUTER_LEN=" . strlen($res2->getContent()) . PHP_EOL;

// 4) Se veio 404 pelo Kernel, mostra os 120 primeiros chars pra identificar a view/handler
if ($res->getStatusCode() === 404) {
  $snip = substr($res->getContent(), 0, 120);
  $snip = preg_replace('/\s+/', ' ', strip_tags($snip));
  echo "KERNEL_SNIP=" . $snip . PHP_EOL;
}
