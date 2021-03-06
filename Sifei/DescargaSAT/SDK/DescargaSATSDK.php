<?php
namespace DHF\Sifei\DescargaSAT\SDK;
use InvalidArgumentException;
use DHF\Sifei\DescargaSAT\SDK\Client;
use DHF\Sifei\DescargaSAT\SDK\Response;
use DHF\Sifei\DescargaSAT\SDK\Models\CFDIModelAPI;
use DHF\Sifei\DescargaSAT\SDK\Models\DescargaProgramada;
use DHF\Sifei\DescargaSAT\SDK\Models\ParametersCFDIQuery;
use DHF\Sifei\DescargaSAT\SDK\Models\DataResponseCFDIConsulta;



use DHF\Sifei\DescargaSAT\SDK\Models\DescargaProgramadaRequest;
use DHF\Sifei\DescargaSAT\SDK\Models\DataResponseDescargaProgramada;
/**
 * @author Daniel Hernandez Fco 
 *   <daniel.hernandez.job@gmail.com>
 *   <daniel.hernandez@sifei.com.mx>
 * SDK for ACI DescargaSAT.
 * 
 */
class DescargaSATSDK{
    private $token;

    private $url="";

    static     $BASE_URL_DEVELOPMENT = "http://localhost:8181";
	static     $BASE_URL_DEVELOPMENT_MIDDLE = "http://localhost:8181";
    static public     $BASE_PRODUCTION_URL="https://descargasat.sifei.com.mx";
    static public     $BASE_PRODUCTION_URL_CP="https://descargacp.sifei.com.mx";
    
    public function setURL($url){
        if(empty($url)){
            throw new InvalidArgumentException("URL Invalid:$url");
        }
        $this->url=$url;
    }
    public function __construct(){
        $this->setURL(self::$BASE_PRODUCTION_URL);
    }
   /**
    * client
    *
    * @var Client
    */
    private $client;
    /**
     * setToken
     *
     * @param string $token
     * @return void
     */
    public function setToken( $token){
        if(empty($token)){
            throw new \InvalidArgumentException("Se debe de incluir un token para consumir el ws");
        }
        $this->token=$token;
    }
    public function hasToken(){
        return empty($this->token);
    }

    public function validateToken(){
        if($this->hasToken()){
            throw new InvalidArgumentException("Se debe de incluir un token para consumir el ws");
        }
    }
     
    public function inicializarClient(){
        if(null==$this->client){
            $this->client= new Client();
            $this->client->setToken($this->token);
            $this->client->setDecoder([$this,'stringJSONToWsResponse']);
            $this->client->setURL($this->url);
            $this->client->setUserAgent('sdkphp/1.1');
        }
    }
    /**
     * Maps string response to WsResponse
     *
     * @param string $jsonstring
     * @param callable $mapper
     * @return WsResponse|null
     */
    public function stringJSONToWsResponse( $jsonstring, $mapper=null){
        $jsonBody=json_decode($jsonstring,true);
        if(false===$jsonBody){
            throw new Exception("Error en json, no se pudo deserializar");
        }
        $wsResponse= new WsResponse();
        $wsResponse->setStatus($jsonBody['status']);
        $wsResponse->setData($jsonBody['data']);
        $wsResponse->setCode($jsonBody['code']);
        $wsResponse->setMessage($jsonBody['message']);
        $wsResponse->setDataMapper($mapper);
        return $wsResponse;
    }
    /**
     * AgregarDescargaProgramada
     *
     * @param DescargaProgramada $descargaProgramada
     * @return Response|null
     */
    public  function AgregarDescargaProgramada($descargaProgramada){
        $strBodyResponse=$this->client->post('descargasr1',[
            'json'=>json_encode(($descargaProgramada))
        ]);
        return $strBodyResponse;
    }
    /**
     * ExisteCFDI
     *
     * @param string $uuid
     * @return Response
     */
    public function ExisteCFDI(  $uuid){
        if(empty($uuid)){
            throw new \InvalidArgumentException("UUID de CFDI invalido. Establece un uuid valido");
        }
       $this->inicializarClient();
       $response= $this->client->get("/api/v2/descargasatsifei/cfdi/exist/{$uuid}");
        
       return $response;
      // return $this->stringJSONToWsResponse($strBodyResponse);
    }
    /**
     * ProgramarDescarga
     *
     * @param DescargaProgramadaRequest $descargaProgramada
     * @return Response
     */
    public function ProgramarDescarga(  $descargaProgramada){
        $this->inicializarClient();
        $response= $this->client->post("/api/v2/descargasatsifei/DescargaProgramada",        [
            #'body'=>\json_encode($descargaProgramada)
            'json'=>$descargaProgramada
        ],function($data){
            $dataModel= new DataResponseDescargaProgramada();

            $dataModel->resultado=$data['resultado'];
            $dataModel->exitosos=$data['exitosos'];
            $dataModel->repetidos=$data['repetidos'];
            $dataModel->errores=$data['errores'];
            
            $dataModel->diasTotalesDeDescarga=$data['diasTotalesDeDescarga'];
            $dataModel->diasyaprogramados=$data['diasyaprogramados'];
            $dataModel->totalDiasNuevosProgramados=$data['totalDiasNuevosProgramados'];
            $dataModel->tipoDescargaName=$data['tipoDescargaName'];
            return $dataModel;
        });
         
        return $response;
       // return $this->stringJSONToWsResponse($strBodyResponse);
    }
    /**
     * ConsultarDescarga
     *
     * @param string|null $uuid
     * @return Response
     */
    public function ConsultarDescarga($uuid){
        if(empty($uuid)){
            throw new \InvalidArgumentException("UUID de descarga Programada no debe ser null o empty. Asegurate de establecer un uuid.");
        }
        $this->inicializarClient();
        $response= $this->client->get("/api/v2/descargasatsifei/DescargaProgramada/{$uuid}");         
        return $response;
    }
    /**
     * ReactivarDescarga
     *
     * @param string|null $uuid
     * @return Response
     */
    public function ReactivarDescarga($uuid){
        if(empty($uuid)){
            throw new \InvalidArgumentException("UUID de descarga Programada no debe ser null o empty. Asegurate de establecer un uuid.");
        }
        $this->inicializarClient();
        $response= $this->client->post("/api/v2/descargasatsifei/DescargaProgramada/{$uuid}",[
            'headers'=>[
                'Content-type'=>'application/json' 
            ]
        ]);         
        return $response;
    }
    /**
     * ConsultarLasDescargasDiarias
     *
     * @param array $parameter
     * @return  
     */
    public function ConsultarLasDescargasDiarias($parameter){
        $this->inicializarClient();
        $response= $this->client->post("/api/v2/descargasatsifei/DescargaProgramadas/query",
        [
            'json'=>$parameter
        ],
        function($data){            
            if(null==$data){
                return null;
            }
            if(!\is_array($data)){
                throw new Exception("data debe ser un array");
            }
            $descargas=[];
            foreach ($data as $item) {
                $descarga= new DescargaProgramada();
                $descarga->setUuid($item['uuid']);
                $descarga->setFechaInicial($item['fechaInicial']);
                $descarga->setFechaFinal($item['fechaFinal']);
                $descarga->setEstado($item['estado']);
                $descargas[]=$descarga;
            }           
            return $descargas;
        });        
        /**
         * por aqui debo crear un DateMapper y pasarlo como callback. si el client en caso de no encontrarlo debera de dejarlo tal cual
         */ 
        return $response;
    }
    /**
     * ObtenerCFDIPorUUID
     *
     * @param string $uuid
     * @return Response
     */
    public function ObtenerCFDIPorUUID( $uuid){
        if(empty($uuid)){
            throw new \InvalidArgumentException("UUID de CFDI no debe ser null o empty. Asegurate de establecer un uuid.");
        }
        $this->inicializarClient();
        $response= $this->client->get("/api/v2/descargasatsifei/cfdi/query/{$uuid}",[],function($data){
            //return $data;
            $item=$data;
            $cfdi= new CFDIModelAPI();
            $cfdi->uuid=$item['uuid'];
            $cfdi->rfcEmisor=$item['rfcEmisor'];
            $cfdi->rfcReceptor=$item['rfcReceptor'];
       
            $cfdi->total=$item['total'];
            $cfdi->fechaEmision=$item['fechaEmision'];
            $cfdi->pacquecertifico=$item['pacquecertifico'];
            $cfdi->fechaCertificacion=$item['fechaCertificacion'];
         
            $cfdi->estado=$item['estado'];
            $cfdi->efecto=$item['efecto'];
            #campos adicionales de gestion.
            $cfdi->fechaDescarga=$item['fechaDescarga'];
            $cfdi->disponibleXML=$item['disponibleXML'];
            $cfdi->disponibleMETA=$item['disponibleMETA'];
            $cfdi->solicitadaMetodo=$item['solicitadaMetodo'];
            return $cfdi;
            
        });         
        return $response;
    }
    /**
     * DescargarCFDIPorUUID
     *
     * @param string $uuid
     * @return Response
     */
    public function DescargarCFDIPorUUID( $uuid){
        if(empty($uuid)){
            throw new \InvalidArgumentException("UUID de CFDI no debe ser null o empty. Asegurate de establecer un uuid.");
        }
        $this->inicializarClient();
        $response= $this->client->get("/api/v2/descargasatsifei/cfdi/download/{$uuid}");         
        return $response;
    }
    /**
     * BuscarCFDI
     *
     * @param ParametersCFDIQuery $params
     * @return Response
     */
    public function BuscarCFDI( $params){

        $this->inicializarClient();
        $response=$this->client->post("/api/v2/descargasatsifei/cfdi/query",
        [
            'json'=>$params
        ],function($data){
            $dataResponseCFDI= new DataResponseCFDIConsulta();
            $dataResponseCFDI->count=$data['count'];
            $dataResponseCFDI->total=$data['total'];

            if(\is_array($data['cfdi'])){
                $dataResponseCFDI->cfdi=[];
                foreach ($data['cfdi'] as $item) {
                    $cfdi= new CFDIModelAPI();
                    $cfdi->uuid=$item['uuid'];
                    $cfdi->rfcEmisor=$item['rfcEmisor'];
                    $cfdi->rfcReceptor=$item['rfcReceptor'];
               
                    $cfdi->total=$item['total'];
                    $cfdi->fechaEmision=$item['fechaEmision'];
                    $cfdi->pacquecertifico=$item['pacquecertifico'];
                    $cfdi->fechaCertificacion=$item['fechaCertificacion'];
                 
                    $cfdi->estado=$item['estado'];
                    $cfdi->efecto=$item['efecto'];
                    #campos adicionales de gestion.
                    $cfdi->fechaDescarga=$item['fechaDescarga'];
                    $cfdi->disponibleXML=$item['disponibleXML'];
                    $cfdi->disponibleMETA=$item['disponibleMETA'];
                    $cfdi->solicitadaMetodo=$item['solicitadaMetodo'];


                    $dataResponseCFDI->cfdi[]=$cfdi;
                }
            }else{
                
            }
            return $dataResponseCFDI;
        });
        return $response;
    }
    /**
     * DescargarCFDIConsulta
     *
     * @param [type] $params
     * @return Response
     */
    public function DescargarCFDIConsulta($params){
        if(empty($params)){
            throw new \InvalidArgumentException("parametros de descarga no debe ser null o empty. Asegurate de establecer un uuid.");
        }
        if($params->fechaInicial==null){
            throw new \InvalidArgumentException("fechaInicial es obligatoria");
        }
        if($params->fechaFinal==null){
            throw new \InvalidArgumentException("fechaInicial es obligatoria");
        }
        $this->inicializarClient();
        $response= $this->client->post("/api/v2/descargasatsifei/cfdi/download",
    [
        'json'=>$params
    ]);
        return $response;
    }
    public function SubirEFirma($params){
        $this->inicializarClient();
        $response= $this->client->post("/api/v2/descargasatsifei/certificado",
    [
        'json'=>$params
    ]);
        return $response;
        
    }

    public const DESBLOQUEO_CFDI="CFDI";
    public const DESBLOQUEO_META="META";
    public function DesbloquearCFDI(array $params){
        $this->inicializarClient();
        $response= $this->client->post("/api/v2/descargasatsifei/cfdi/DesbloquearCFDIAPI",
    [
        'json'=>$params
    ]);
        return $response;
    }

    public function EstablecerHorasEspera(array $params){
        $this->inicializarClient();
        $response= $this->client->post("/api/v2/descargasatsifei/configuracion/horasEspera",
    [
        'json'=>$params
    ]);
        return $response;
    }

    public function consultarConfiguracion(array $params=[]){
        $this->inicializarClient();
        $response= $this->client->post("/api/v2/descargasatsifei/configuracion/query",
    [
        'json'=>$params
    ]);
        return $response;
    }
    public function EstablecerMetodoDescarga(array $params=[]){
        $this->inicializarClient();
        $response= $this->client->post("/api/v2/descargasatsifei/configuracion/metodoDescarga",
    [
        'json'=>$params
    ]);
        return $response;
    }
    public const METODO_DESCARGA_WS='WS';
    public const METODO_DESCARGA_PORTAL='PORTAL';
}