<?php
namespace DHF\Sifei\DescargaSAT\SDK\Models;

class DataResponseDescargaProgramada{
    /**
     * Mensaje descriptivo de la operacion.
     *
     * @var string
     */
    public   $resultado;
    /**
     * arreglo de errores.
     *
     * @var string[] 
     */
	public $errores;
	/**
     * Arreglo de UUID creados.
     *
     * @var string[] 
     */
	public $exitosos;
	/**
     * Arreglo de UUID ya existentes previos
     *
     * @var string[] 
     */
	public $repetidos;
	/**
     * Undocumented variable
     *
     * @var int
     */
	public  $diasyaprogramados;
	
	
    /**
     * Undocumented variable
     *
     * @var int
     */
	public $diasTotalesDeDescarga;
    /**
     * Undocumented variable
     *
     * @var int
     */
	public $totalDiasNuevosProgramados;
	/**
     * 
     *
     * @var string
     */
	public $tipoDescargaName;
	
}
