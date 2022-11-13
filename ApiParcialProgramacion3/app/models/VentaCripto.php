<?php

class VentaCripto
{
    public $id;
    public $fecha;
    public $idCripto;
    public $nombreUsuario;
    public $foto;
    public $cantidad;

    public function crearVenta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ventas (fecha,idCripto, foto,cantidad,nombreUsuario) VALUES (:fecha,:idCripto, :foto, :cantidad,:nombreUsuario)");
        $consulta->bindValue(':fecha', $this->fecha);
        $consulta->bindValue(':idCripto', $this->idCripto, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':foto', $this->foto);
        $consulta->bindValue(':nombreUsuario', $this->nombreUsuario);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, fecha,idCripto, foto ,cantidad FROM ventas ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'VentaCripto');
    }
    public static function obtenerAlemanasFecha()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT  ventas.id, ventas.fecha,ventas.idCripto, ventas.foto,ventas.nombreUsuario ,ventas.cantidad 
        from criptos INNER JOIN ventas ON ventas.idCripto = criptos.id 
        where criptos.nacionalidad='alemania' AND ventas.fecha > '2020-06-01' and ventas.fecha < '2025-02-01'   ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'VentaCripto');
    }
    public static function obtenerUsuariosPorNombreMoneda($nombreMoneda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT ventas.nombreUsuario 
        from criptos INNER JOIN ventas ON ventas.idCripto = criptos.id 
        where criptos.nombre= :nombreMoneda ");
        $consulta->bindValue(':nombreMoneda', $nombreMoneda, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'VentaCripto');
    }
    public static function obtenercantidad($cantidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, fecha,idCripto, foto ,cantidad FROM ventas WHERE cantidad =:cantidad");
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'VentaCripto');
    }
    public static function obtenerTodosBaja()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, foto FROM usuarios WHERE fechaBaja is not null ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'VentaCripto');
    }

    public static function obtenerCripto($idCripto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,fecha, idCripto, foto ,cantidad FROM ventas WHERE idCripto = :idCripto");
        $consulta->bindValue(':idCripto', $idCripto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('VentaCripto');
    }
    public static function obtenerId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,fecha, idCripto, foto ,cantidad FROM ventas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('VentaCripto');
    }
    public static function obtenerCriptocantidad($cantidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,fecha, idCripto, foto ,cantidad FROM ventas WHERE cantidad = :cantidad");
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('VentaCripto');
    }

    public function modificarCripto()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, foto = :foto WHERE id = :id");
        $fotoHash = password_hash($this->foto, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $fotoHash);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarCripto($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}