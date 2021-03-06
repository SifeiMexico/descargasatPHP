<?php
namespace DHF\Sifei\DescargaSAT\SDK\Models;
use DateTime;
class ParametersCFDIQuery implements \JsonSerializable
{
    const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s';
    /**
     * Fecha inicial
     *
     * @var DateTime
     */
    public $fechaFinal;
    /**
     * Fecha final
     *
     * @var DateTime
     */
    public $fechaInicial;
    /*
    public const ESTADO_CFDI_TODOS = -1;
    public const ESTADO_CFDI_CANCELADO = 0;
    public const ESTADO_CFDI_VIGENTE = 1;

    public const EMITIDO_RECIBIDO_TODOS = -1;
    public const EMITIDO_RECIBIDO_EMITIDOS = 1;
    public const EMITIDO_RECIBIDO_RECIBIDOS = 2;
    */
    private $rfcEmisor;
    private $rfcReceptor;
    private $uuidCFDI;
    private $efectoCFDI;
    /**
     * Cantidad maxima a consultar
     *
     * @var int
     */
    private $limit;
    /**
     * Desplazamiento de busqueda. Es decir , desde que numero de elemento empezar a buscar.
     * 
     *
     * @var int
     */
    private $offset;
    public function jsonSerialize() {
        return [
            'fechaInicial'=>!empty($this->fechaInicial)? $this->fechaInicial->format(self::DEFAULT_TO_STRING_FORMAT):null,
            'fechaFinal'=>!empty($this->fechaFinal)? $this->fechaFinal->format(self::DEFAULT_TO_STRING_FORMAT):null,
            'rfcEmisor'=>$this->rfcEmisor,
            'rfcReceptor'=>$this->rfcReceptor,
            'uuidCFDI'=>$this->uuidCFDI,
            'efectoCFDI'=>$this->efectoCFDI,
            'limit'=>$this->limit,
            'offset'=>$this->offset
        ];
    }

    /**
     * Get fecha inicial
     *
     * @return  DateTime
     */ 
    public function getFechaFinal()
    {
        return $this->fechaFinal;
    }

    /**
     * Set fecha inicial
     *
     * @param  DateTime  $fechaFinal  Fecha inicial
     *
     * @return  self
     */ 
    public function setFechaFinal(DateTime $fechaFinal)
    {
        $this->fechaFinal = $fechaFinal;

        return $this;
    }

    /**
     * Get desplazamiento de busqueda. Es decir , desde que numero de elemento empezar a buscar.
     *
     * @return  int
     */ 
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set desplazamiento de busqueda. Es decir , desde que numero de elemento empezar a buscar.
     *
     * @param  int  $offset  Desplazamiento de busqueda. Es decir , desde que numero de elemento empezar a buscar.
     *
     * @return  self
     */ 
    public function setOffset( $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Get fecha final
     *
     * @return  DateTime
     */ 
    public function getFechaInicial()
    {
        return $this->fechaInicial;
    }

    /**
     * Set fecha final
     *
     * @param  DateTime  $fechaInicial  Fecha final
     *
     * @return  self
     */ 
    public function setFechaInicial(DateTime $fechaInicial)
    {
        $this->fechaInicial = $fechaInicial;

        return $this;
    }

    /**
     * Get the value of rfcEmisor
     */ 
    public function getRfcEmisor()
    {
        return $this->rfcEmisor;
    }

    /**
     * Set the value of rfcEmisor
     *
     * @return  self
     */ 
    public function setRfcEmisor($rfcEmisor)
    {
        $this->rfcEmisor = $rfcEmisor;

        return $this;
    }

    /**
     * Get the value of rfcReceptor
     */ 
    public function getRfcReceptor()
    {
        return $this->rfcReceptor;
    }

    /**
     * Set the value of rfcReceptor
     *
     * @return  self
     */ 
    public function setRfcReceptor($rfcReceptor)
    {
        $this->rfcReceptor = $rfcReceptor;

        return $this;
    }

    /**
     * Get the value of uuidCFDI
     */ 
    public function getUuidCFDI()
    {
        return $this->uuidCFDI;
    }

    /**
     * Set the value of uuidCFDI
     *
     * @return  self
     */ 
    public function setUuidCFDI($uuidCFDI)
    {
        $this->uuidCFDI = $uuidCFDI;

        return $this;
    }

    /**
     * Get cantidad maxima a consultar
     *
     * @return  int
     */ 
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set cantidad maxima a consultar
     *
     * @param  int  $limit  Cantidad maxima a consultar
     *
     * @return  self
     */ 
    public function setLimit( $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the value of efectoCFDI
     */ 
    public function getEfectoCFDI()
    {
        return $this->efectoCFDI;
    }

    /**
     * Set the value of efectoCFDI
     *
     * @return  self
     */ 
    public function setEfectoCFDI($efectoCFDI)
    {
        $this->efectoCFDI = $efectoCFDI;

        return $this;
    }
}
