<?php
namespace DHF\Sifei\DescargaSAT\SDK\Models;
class DescargaProgramadaRequest  implements \JsonSerializable{
    const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s';

	 
    
    /**
     * Fecha inicial de la desgarga
     *  pattern = "yyyy-MM-dd hh:mm:ss"
     * @var \DateTime
     */
	private   $fechaInicial;
	 
    /**
     * Fecha inicial de la desgarga
     *  
     * @var DateTime
     */
	private   $fechaFinal;
    /**
     * Tipo de descarga
     *
     * @var string
     */
    private  $tipodeDescargaDescarga=self::TIPO_DESCARGA_CFDI;

      const TIPO_DESCARGA_CFDI='CFDI';

      const TIPO_DESCARGA_META='META';

    

    //serializacion...
    public function jsonSerialize() {
        return [
            'FechaInicial'=>$this->fechaInicial->format(self::DEFAULT_TO_STRING_FORMAT),
            'FechaFinal'=>$this->fechaFinal->format(self::DEFAULT_TO_STRING_FORMAT),
            'TipoDeDescarga'=>$this->tipodeDescargaDescarga
        ];
    }

    /**
     * Get the value of tipodeDescargaDescarga
     */ 
    public function getTipodeDescargaDescarga()
    {
        return $this->tipodeDescargaDescarga;
    }

    /**
     * Set the value of tipodeDescargaDescarga
     *
     * @return  self
     */ 
    public function setTipodeDescargaDescarga($tipodeDescargaDescarga)
    {
        $this->tipodeDescargaDescarga = $tipodeDescargaDescarga;

        return $this;
    }

	/**
	 * Get fecha inicial de la desgarga
	 *
	 * @return  DateTime
	 */ 
	public function getFechaFinal()
	{
		return $this->fechaFinal;
	}

	/**
	 * Set fecha inicial de la desgarga
	 *
	 * @param  DateTime  $fechaFinal  Fecha inicial de la desgarga
	 *
	 * @return  self
	 */ 
	public function setFechaFinal(\DateTime $fechaFinal)
	{
		$this->fechaFinal = $fechaFinal;

		return $this;
	}

	/**
	 * Get pattern = "yyyy-MM-dd hh:mm:ss"
	 *
	 * @return  \DateTime
	 */ 
	public function getFechaInicial()
	{
		return $this->fechaInicial;
	}

	/**
	 * Set pattern = "yyyy-MM-dd hh:mm:ss"
	 *
	 * @param  \DateTime  $fechaInicial  pattern = "yyyy-MM-dd hh:mm:ss"
	 *
	 * @return  self
	 */ 
	public function setFechaInicial(\DateTime $fechaInicial)
	{
		$this->fechaInicial = $fechaInicial;

		return $this;
	}
}
