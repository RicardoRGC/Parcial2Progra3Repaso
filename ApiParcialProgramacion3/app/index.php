<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/AutentificadorJWT.php';
require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';
require_once './middlewares/SalidaMiddlewares.php';
require_once './middlewares/EntradaMiddlewares.php';
require_once './middlewares/VerificarMiddleware.php';
require_once './middlewares/VerificarAdminMiddleware.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/criptoControllers.php';
require_once './controllers/LoginControllers.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group(
  '/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos')->add(new VerificarAdminMiddleware());
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/cargar]', \UsuarioController::class . ':CargarUno'); //cargar
    $group->put('[/modificar]', \UsuarioController::class . ':ModificarUno');
    $group->delete('[/]', \UsuarioController::class . ':BorrarUno');
  }
) /*->add(
 new VerificarMiddleware()
 )*/;


$app->post('/login', \LoginControllers::class . ':Verificar'); //Clave ,usuario(verificar usuario)

//2-(POST)Alta cripto moneda( precio, nombre, foto, nacionalidad)->solo admin/(JWT)
$app->post('/altaCripo', \CriptoController::class . ':CargarUno')->add(new VerificarAdminMiddleware());
//3-(1pt)(GET)listado de todas las cripto monedas -> sin autentificación
$app->get('/criptos', \CriptoController::class . ':TraerTodos');
//4-(GET)listado de todas las cripto de una nacionalidad pasada por parámetro-> sin autentificación


$app->get(
  '[/]', function (Request $request, Response $response) {
    $response->getBody()->write("Pagina RGraf");
    return $response;
  }
);

$app->run();