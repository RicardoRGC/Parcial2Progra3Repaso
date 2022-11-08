<?php
require_once './models/Cripto.php';
require_once './interfaces/IApiUsable.php';

class CriptoController extends Cripto implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {

    $parametros = $request->getParsedBody();
    $archivo = $request->getUploadedFiles();

    if ($parametros != null && count($parametros) == 3) {
      try {
        // var_dump($parametros);
        $nombre = $parametros['nombre'];

        $usuario1 = Cripto::obtenerCripto($nombre);

        if (!$usuario1) {

          $precio = $parametros['precio'];
          $nombre = $parametros['nombre'];
          $nacionalidad = $parametros['nacionalidad'];

          try {
            $foto = $archivo['foto'];
            if (is_null($foto) || $foto->getClientMediaType() == "") {
              throw new Exception("No file");
            }
            $ext = $foto->getClientMediaType();
            var_dump($ext);
            $ext = explode("/", $ext)[1];
            $ruta = "./Cryptos/" . $nombre . "." . $ext;
            $foto->moveTo($ruta);
          } catch (Exception $e) {
            echo "no se pudo subir la imagen";
            $ruta = "";
          }
          // Creamos el usuario
          $usr = new Cripto();
          $usr->precio = $precio;
          $usr->nombre = $nombre;
          $usr->foto = $ruta;
          $usr->nacionalidad = $nacionalidad;

          $id = $usr->crearCripto();

          $payload = json_encode(array("mensaje" => "Crypto creado con exito id: $id "));
        } else {
          $payload = json_encode("crypto ya existe");
        }



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
    // Buscamos usuario por nombre
    $usr = $args['usuario'];
    $usuario = Usuario::obtenerUsuario($usr);
    $payload = json_encode($usuario);

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
    $lista = Cripto::obtenerTodos();
    $payload = json_encode(array("listaCripto" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }
  ///MODIFICAR----------------------------------------------------------------------------------
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

        $nombre = $parametros['nombre'];
        $clave = $parametros['clave'];
        $id = $parametros['id'];

        $usr = new Usuario();
        $usr->usuario = $nombre;
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
}