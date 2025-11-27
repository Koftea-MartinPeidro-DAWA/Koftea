<?php
namespace Koftea;

class Producto
{
    // -------------------------
    // Atributos
    // -------------------------
    private $ID;
    private $Categoria;
    private $Nombre;
    private $Descripcion;
    private $ProcedenciaOrigen;
    private $Intensidad;
    private $Formato;
    private $Precio;
    private $Stock;
    private $DetalleEspecifico;
    private $Imagen;

    // -------------------------
    // Constructor
    // -------------------------
    public function __construct(
        $ID = "",
        $Categoria = "",
        $Nombre = "",
        $Descripcion = "",
        $ProcedenciaOrigen = "",
        $Intensidad = "",
        $Formato = "",
        $Precio = 0.0,
        $Stock = 0,
        $DetalleEspecifico = "",
        $Imagen = null
    ) {
        $this->ID = $ID;
        $this->Categoria = $Categoria;
        $this->Nombre = $Nombre;
        $this->Descripcion = $Descripcion;
        $this->ProcedenciaOrigen = $ProcedenciaOrigen;
        $this->Intensidad = $Intensidad;
        $this->Formato = $Formato;
        $this->Precio = $Precio;
        $this->Stock = $Stock;
        $this->DetalleEspecifico = $DetalleEspecifico;
        $this->Imagen = $Imagen;
    }

    // -------------------------
    // Getters y Setters
    // -------------------------
    public function getID() { return $this->ID; }
    public function setID($ID) { $this->ID = $ID; }

    public function getCategoria() { return $this->Categoria; }
    public function setCategoria($Categoria) { $this->Categoria = $Categoria; }

    public function getNombre() { return $this->Nombre; }
    public function setNombre($Nombre) { $this->Nombre = $Nombre; }

    public function getDescripcion() { return $this->Descripcion; }
    public function setDescripcion($Descripcion) { $this->Descripcion = $Descripcion; }

    public function getProcedenciaOrigen() { return $this->ProcedenciaOrigen; }
    public function setProcedenciaOrigen($ProcedenciaOrigen) { $this->ProcedenciaOrigen = $ProcedenciaOrigen; }

    public function getIntensidad() { return $this->Intensidad; }
    public function setIntensidad($Intensidad) { $this->Intensidad = $Intensidad; }

    public function getFormato() { return $this->Formato; }
    public function setFormato($Formato) { $this->Formato = $Formato; }

    public function getPrecio() { return $this->Precio; }
    public function setPrecio($Precio) { $this->Precio = $Precio; }

    public function getStock() { return $this->Stock; }
    public function setStock($Stock) { $this->Stock = $Stock; }

    public function getDetalleEspecifico() { return $this->DetalleEspecifico; }
    public function setDetalleEspecifico($DetalleEspecifico) { $this->DetalleEspecifico = $DetalleEspecifico; }

    public function getImagen() { return $this->Imagen; }
    public function setImagen($Imagen) { $this->Imagen = $Imagen; }

    // -------------------------
    // Métodos básicos
    // -------------------------

    // Convertir a array (útil para DB, JSON…)
    public function toArray()
    {
        return [
            "ID" => $this->ID,
            "Categoria" => $this->Categoria,
            "Nombre" => $this->Nombre,
            "Descripcion" => $this->Descripcion,
            "ProcedenciaOrigen" => $this->ProcedenciaOrigen,
            "Intensidad" => $this->Intensidad,
            "Formato" => $this->Formato,
            "Precio" => $this->Precio,
            "Stock" => $this->Stock,
            "DetalleEspecifico" => $this->DetalleEspecifico,
            "Imagen" => $this->Imagen
        ];
    }

    // Crear objeto desde array
    public static function fromArray($data)
    {
        return new self(
            $data["ID"] ?? "",
            $data["Categoria"] ?? "",
            $data["Nombre"] ?? "",
            $data["Descripcion"] ?? "",
            $data["ProcedenciaOrigen"] ?? "",
            $data["Intensidad"] ?? "",
            $data["Formato"] ?? "",
            $data["Precio"] ?? 0.0,
            $data["Stock"] ?? 0,
            $data["DetalleEspecifico"] ?? "",
            $data["Imagen"] ?? null
        );
    }

    // Mostrar resumen del producto
    public function mostrarInfo()
    {
        return "Producto: {$this->Nombre}  
Categoría: {$this->Categoria}  
Precio: {$this->Precio} €  
Stock: {$this->Stock}";
    }
}
