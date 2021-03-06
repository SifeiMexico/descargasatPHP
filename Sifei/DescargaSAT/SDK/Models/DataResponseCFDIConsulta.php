<?php
namespace DHF\Sifei\DescargaSAT\SDK\Models;
use DHF\Sifei\DescargaSAT\SDK\Models\CFDIModelAPI;

class DataResponseCFDIConsulta{
    /**
     * Lista de elementos CFDI
     *
     * @var CFDIModelAPI[]
     */
    public  $cfdi;
	/**
     * Total de resultados existentes en la busqueda
     *
     * @var int
     */
    public   $total;
    /**
     * Length de consulta de devuelta(es decir, numero de elementos retornados en la peticion actual)
     *
     * @var int
     */
	public   $count;
	 
	 
	 
}