<?php

class Cripto
{ //2-(POST)Alta cripto moneda( precio, nombre, foto, nacionalidad)->solo admin/(JWT)
    public $id;
    public $precio;
    public $nombre;
    public $foto;
    public $nacionalidad;
    public $fechaBaja;

    public function crearCripto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO criptos (precio,nombre, foto,nacionalidad) VALUES (:precio,:nombre, :foto, :nacionalidad)");
        $consulta->bindValue(':precio', $this->precio);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio,nombre, foto ,nacionalidad,fechaBaja FROM criptos ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cripto');
    }
    public static function obtenerNacionalidad($nacionalidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio,nombre, foto ,nacionalidad,fechaBaja FROM criptos WHERE nacionalidad =:nacionalidad");
        $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cripto');
    }
    public static function obtenerTodosBaja()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, foto,fechaBaja FROM usuarios WHERE fechaBaja is not null ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cripto');
    }

    public static function obtenerCripto($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,precio, nombre, foto ,nacionalidad ,fechaBajaFROM criptos WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Cripto');
    }
    public static function obtenerId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,precio, nombre, foto ,nacionalidad FROM criptos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Cripto');
    }
    public static function obtenerCriptoNacionalidad($nacionalidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,precio, nombre, foto ,nacionalidad FROM criptos WHERE nacionalidad = :nacionalidad");
        $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Cripto');
    }

    public function modificarCripto()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia(); //precio, nombre, foto ,nacionalidad 
        $consulta = $objAccesoDato->prepararConsulta("UPDATE criptos SET nombre = :nombre, foto = :foto, precio = :precio, nacionalidad = :nacionalidad WHERE id = :id");
        $consulta->bindValue(':foto', $this->foto);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto);
        $consulta->bindValue(':id', $this->id);
        $consulta->bindValue(':precio', $this->precio);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad);
        $consulta->execute();
    }

    public static function borrarCripto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE criptos SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}