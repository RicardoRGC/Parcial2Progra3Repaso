<?php
require_once './models/VentaCripto.php';
require_once './interfaces/IApiUsable.php';
require_once './fpdf/fpdf.php';
use Slim\Psr7\Response;

class VentaController extends VentaCripto implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {

    $parametros = $request->getParsedBody();

    if ($parametros != null && count($parametros) == 3) {
      try {
        // var_dump($parametros);
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $usuario = AutentificadorJWT::ObtenerData($token);


        $archivo = $request->getUploadedFiles();
        $fecha = $parametros['fecha'];
        $idCripto = $parametros['idCripto'];
        $cantidad = $parametros['cantidad'];
        $cripto = Cripto::obtenerId($idCripto);

        try {

          // var_dump($archivo['foto']);
          $foto = $archivo['foto'];
          if (is_null($foto) || $foto->getClientMediaType() == "") {
            throw new Exception("No file");
          }
          $ext = $foto->getClientMediaType();
          $ext = explode("/", $ext)[1];
          $ruta = "./FotosCripto/" . $cripto->nombre . "-" . $usuario->mail . "-" . $fecha . "." . $ext;
          $foto->moveTo($ruta);
        } catch (Exception $e) {
          echo "no se pudo subir la imagen";
          $ruta = "";
        }
        // Creamos el usuario
        $usr = new VentaCripto();
        $usr->fecha = $fecha;
        $usr->nombreUsuario = $usuario->mail;
        $usr->foto = $ruta;
        $usr->idCripto = $idCripto;
        $usr->cantidad = $cantidad;

        $id = $usr->crearVenta();

        $payload = json_encode(array("mensaje" => "creado con exito id: $id "));




      } catch (Exception $e) {

        $payload = json_encode(array('error' => $e->getMessage()));
      }
    } else {
      $payload = json_encode('error no hay datos');
    }


    $response->getBody()->write($payload);
    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }
  //-----------------------------------------------------------------------------------
  public function TraerUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $usr = $args['idCripto'];

    $usuario = VentaCripto::obtenerCriptoNacionalidad($usr);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }
  //----------------------------------------------------------------------------------------------------------------------------------
  //-----------------------------------------------------------------------------------
  public function TraerNacionalidad($request, $response, $args)
  {
    // $parametros = $request->getParsedBody();
    $parametros = $request->getQueryParams();

    $usr = $parametros['idCripto'];
    var_dump($usr);
    $lista = VentaCripto::obtenerNacionalidad($usr);
    $payload = json_encode(array("listaCripto" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }
  //----------------------------------------------------------------------------------------------------------------------------------
  //-----------------------------------------------------------------------------------
  public function TraerId($request, $response, $args)
  {

    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    $datos = AutentificadorJWT::ObtenerData($token);

    $parametros = $request->getQueryParams();

    $usr = $parametros['id'];

    $cripto = VentaCripto::obtenerId($usr);

    $payload = json_encode(array("listaCripto" => $cripto, "datos" => $datos));
    // $payload = json_encode($cripto);

    $response->getBody()->write($payload);
    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }
  //----------------------------------------------------------------------------------------------------------------------------------
  public function TraerTodos($request, $response, $args)
  {
    $lista = VentaCripto::obtenerTodos();
    $payload = json_encode(array("listaCripto" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }
  ///---------------------------------------------------------------------------------
  //----------------------------------------------------------------------------------------------------------------------------------
  public function TraerAlemanasFecha($request, $response, $args)
  {
    $lista = VentaCripto::obtenerAlemanasFecha();
    $payload = json_encode(array("listaCripto" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }
  ///----------------------------------------------------------------------------------
  //----------------------------------------------------------------------------------------------------------------------------------
  public function TraerUsuariosPorNombreMoneda($request, $response, $args)
  {
    $parametros = $request->getQueryParams();

    $nombreMoneda = $parametros['nombreMoneda'];

    $lista = VentaCripto::obtenerUsuariosPorNombreMoneda($nombreMoneda);

    for ($i = 0; $i < count($lista); $i++) {
      $nombres[$i] = $lista[$i]->nombreUsuario;
    }

    $payload = json_encode(array("listaUsuarios" => $nombres));

    $response->getBody()->write($payload);
    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }
  ///----------------------------------------------------------------------------------
  public function ModificarUno($request, $response, $args)
  {

    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $esValido = false;

    try {
      AutentificadorJWT::verificarToken($token);
      $esValido = true;
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if ($esValido) {
      $parametros = $request->getParsedBody();
      if ($parametros != null) {

        var_dump($parametros);

        $nombreUsuario = $parametros['nombreUsuario'];
        $clave = $parametros['clave'];
        $id = $parametros['id'];

        $usr = new Usuario();
        $usr->usuario = $nombreUsuario;
        $usr->clave = $clave;
        $usr->id = $id;

        $usr->modificarUsuario();

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
      } else {
        $payload = json_encode("error de datos");
      }
    }
    //-----------------------------------------------------------


    $response->getBody()->write($payload);
    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();


    $usuarioId = $parametros['usuarioId'];
    Usuario::borrarUsuario($usuarioId);

    $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }
  public function VentasPdf($request, $response, $args)
  {

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, '¡Hola, Mundo!');
    $pdf->Output();

    $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));


    $response = new Response();
    return $response
      ->withStatus(200);
  }
}