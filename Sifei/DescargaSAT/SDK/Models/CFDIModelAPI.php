<?php
namespace DHF\Sifei\DescargaSAT\SDK\Models;
/**
 * Representa un CFDI. Usado en buscarCFDI
 */
class CFDIModelAPI{
    public $uuid;
    public $rfcEmisor;
    public $rfcReceptor;
    public $fechaDescarga;
    public $total;
    public $fechaEmision;
    public $pacquecertifico;
    public $fechaCertificacion;

    public $solicitadaMetodo;
    public $estado;
    public $efecto;

    public $disponibleXML;
    public $disponibleMETA;

    public function isEstadoVigente(){
        return $this->equalsString($this->estado,'1')||
                $this->equalsString($this->estado,'Vigente');
    }
    public function isEstadoCancelado(){
        return $this->equalsString($this->estado,'0')||
                $this->equalsString($this->estado,'Cancelado');
    }

    public function isEfectoIngreso(){
        return $this->equalsString($this->efecto,'I')||
                $this->equalsString($this->estado,'Ingreso');
    }
    public function isEfectoNomina(){
        return $this->equalsString($this->efecto,'N')||
                $this->equalsString($this->estado,'Nómina');
    }
    public function isEfectoTraslado(){
        return $this->equalsString($this->efecto,'T')||
                $this->equalsString($this->estado,'Traslado');
    }
    public function isEfectoPago(){
        return $this->equalsString($this->efecto,'P')||
                $this->equalsString($this->estado,'Pago');
    }
    public function isEfectoEgreso(){
        return $this->equalsString($this->efecto,'E')||
                $this->equalsString($this->estado,'Egreso');
    }
   
    /**
     * Compara 2 string 
     *
     * @param string $val1
     * @param string $val2
     * @return void
     */
    public function equalsString(  $val1,  $val2){
        return \strcasecmp($val1,$val2)===0;
    }

    public function isSolicitadaPorMeta(){
           return $this->equalsString($this->solicitadaMetodo,'META');
    }
    public function isSolicitadaPorDesc(){
        return $this->equalsString($this->solicitadaMetodo,'DESC');
    }
    public function isSolicitadaPorBOTH(){
        return $this->equalsString($this->solicitadaMetodo,'BOTH');
    }

}