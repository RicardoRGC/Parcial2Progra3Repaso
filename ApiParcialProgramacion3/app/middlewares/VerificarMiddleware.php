<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class VerificarMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $esValido = false;
        $response = new Response();
        try {
            AutentificadorJWT::verificarToken($token);
            $esValido = true;
            //------------------------------------------------

        } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
            $response->getBody()->write($payload);
        }

        if ($esValido) {
            // $payload = json_encode(array('valid' => $esValido));
            $response = $handler->handle($request);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
