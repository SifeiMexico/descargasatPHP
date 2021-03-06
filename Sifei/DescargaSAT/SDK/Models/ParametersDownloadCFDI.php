<?php
namespace DHF\Sifei\DescargaSAT\SDK\Models;
use DateTime;
class ParametersDownloadCFDI implements \JsonSerializable
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

    public $efectoCFDI;

    public $emitidoRecibido=self::EMITIDO_RECIBIDO_TODOS;

      const EMITIDO_RECIBIDO_TODOS=-1;
      const EMITIDO_RECIBIDO_EMITIDOS=1;
      const EMITIDO_RECIBIDO_RECIBIDOS=2;

    public function setOrigenEmitidos(){
        $this->emitidoRecibido=self::EMITIDO_RECIBIDO_EMITIDOS;
    }
    public function setOrigenRecibidos(){
        $this->emitidoRecibido=self::EMITIDO_RECIBIDO_RECIBIDOS;
    }
    public function setOrigenTodos(){
        $this->emitidoRecibido=self::EMITIDO_RECIBIDO_TODOS;
    }
    public function jsonSerialize() {
        return [
            'fechaInicial'=>!empty($this->fechaInicial)? $this->fechaInicial->format(self::DEFAULT_TO_STRING_FORMAT):null,
            'fechaFinal'=>!empty($this->fechaFinal)? $this->fechaFinal->format(self::DEFAULT_TO_STRING_FORMAT):null,          
            'efectoCFDI'=>$this->efectoCFDI,
            'emitidoRecibido'=>$this->emitidoRecibido
           
        ];
    }

    /**
     * Get the value of emitidoRecibido
     */ 
    public function getEmitidoRecibido()
    {
        return $this->emitidoRecibido;
    }

    /**
     * Set the value of emitidoRecibido
     *
     * @return  self
     */ 
    public function setEmitidoRecibido($emitidoRecibido)
    {
        $this->emitidoRecibido = $emitidoRecibido;

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
}
     