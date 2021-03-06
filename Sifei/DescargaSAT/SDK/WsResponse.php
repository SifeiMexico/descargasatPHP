<?php
namespace DHF\Sifei\DescargaSAT\SDK;
/**
 * Representa el response a nivel API
 */
class WsResponse{
    /**
     * Estado de la solicitud, ver STATUS y docuentacion para saber mas
     *
     * @var string
     */
    private $status;
    /**
     * Codigo de error
     *
     * @var string
     */
    private $code;

    /**
     * Este tipo de dato varia segun el servicio
     *
     * @var any
     */
    private $data;
    /**
     * Mensaje que acompaña code en caso de error
     *
     * @var string
     */    
    private $message;




      const STATUS_SUCCESS="success";
      const STATUS_FAIL="fail";
      const STATUS_ERROR="error";

    /**
     * isStatusSuccess
     *
     * @return boolean
     */
    public function isStatusSuccess(){
        return $this->status==self::STATUS_SUCCESS;
    }
    /**
     * isStatusFail
     *
     * @return boolean
     */
    public function isStatusFail(){
        return $this->status==self::STATUS_FAIL;
    }
    /**
     * isStatusError
     *
     * @return boolean
     */
    public function isStatusError(){
        return $this->status==self::STATUS_ERROR;
    }
    public function getStatus(){
        return $this->status;
    }
    public function getMessage(){
        return $this->message;
    }
    public function getCode(){
        return $this->code;
    }
    public function getData(){
        if($this->dataMapper==null){
           return $this->data;
        }else{
            $dataMapper=$this->dataMapper;
            return  $dataMapper($this->data);
        }
    }


    public function setStatus($v){
        return $this->status=$v;
    }
    public function setMessage($v){
        return $this->message=$v;
    }
    public function setCode($v){
        return $this->code=$v;
    }
    public function setData($v){
        return $this->data=$v;
    }

    /**
     * isCodeNoEncontrado
     *
     * @return boolean
     */
    public function isCodeNoEncontrado(){
       return $this->code==self::NOT_FOUND;
    }

    const NOT_FOUND=404;
    private $dataMapper=null;
    /**
     * setDataMapper
     *
     * @param callable|null $dataMapper
     * @return void
     */
    public function setDataMapper( $dataMapper){
        $this->dataMapper=$dataMapper;
    }
}