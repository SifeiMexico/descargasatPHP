<?php
namespace DHF\Sifei\DescargaSAT\SDK\Models;
/**
 * Modela la respuesta basica. Por facilidad de implmentacion dejo las propiedades publicas
 */
class DescargaProgramada {

    public   $uuid;

	 
	public   $fechaInicial;
 
    
    public   $fechaFinal;
    
    public $estado;

    
	  const ESTADO_ESPERANDO = "ESPERANDO";    
	  const ESTADO_EN_PROCESO = "EN PROCESO"; //aplica cuando esta en proceso la descargaprogramada
	  const ESTADO_COMPLETADO = "COMPLETADO"; //aplica cuando ha sido correcta la descarga programada
	  const ESTADO_ERROR = "ERROR";
	  const ESTADO_INCOMPLETO="INCOMPLETA";
	  const ESTADO_BLOQUEADO="BLOQUEADA";
	  const ESTADO_ALL="*";
	/**
     * isEstadoEsperando
     *
     * @return boolean
     */
	public function isEstadoEsperando() {
		return $this->estado==(self::ESTADO_ESPERANDO);
    }
    /**
     * isEstadoEnProceso
     *
     * @return boolean
     */
	public function isEstadoEnProceso() {
		return $this->estado==(self::ESTADO_EN_PROCESO);
    }
    /**
     * 
     *
     * @return boolean
     */
	public function isEstadoCompletado() {
		return $this->estado==(self::ESTADO_COMPLETADO);
    }
    /**
     * 
     *
     * @return boolean
     */
	public function isEstadoERROR() {
		return $this->estado==(self::ESTADO_ERROR);
    }
    /**
     * 
     *
     * @return boolean
     */
	public function isEstadoIncompleto() {
		return $this->estado==(self::ESTADO_INCOMPLETO);
    }
    /**
     * 
     *
     * @return boolean
     */
	public function isEstadoBloqueado() {
		return $this->estado==(self::ESTADO_BLOQUEADO);
    }
    
    

    /**
     * Get the value of uuid
     */ 
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set the value of uuid
     *
     * @return  self
     */ 
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

	/**
	 * Get the value of fechaInicial
	 */ 
	public function getFechaInicial()
	{
		return $this->fechaInicial;
	}

	/**
	 * Set the value of fechaInicial
	 *
	 * @return  self
	 */ 
	public function setFechaInicial($fechaInicial)
	{
		$this->fechaInicial = $fechaInicial;

		return $this;
	}

    /**
     * Get the value of fechaFinal
     */ 
    public function getFechaFinal()
    {
        return $this->fechaFinal;
    }

    /**
     * Set the value of fechaFinal
     *
     * @return  self
     */ 
    public function setFechaFinal($fechaFinal)
    {
        $this->fechaFinal = $fechaFinal;

        return $this;
    }

    /**
     * Get the value of estado
     */ 
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set the value of estado
     *
     * @return  self
     */ 
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }


    public const PROCESADA_POR_WS=2; //indica que se esta procesando por medio del WS
    public const PROCESADA_POR_PORTAL=1; //indica que se esta procesando a por medio del portal


    /**
     * Indica el tipo recursos a obtener en la  descarga.
     * CFDI
     */
    public const TIPO_DESCARGA_CFDI=1;
    /**
     * Indica el tipo recursos a obtener en la  descarga.
     * META
     */
    public const TIPO_DESCARGA_METADATA=2;
}