<?php
namespace DHF\Sifei\DescargaSAT\SDK;

use Exception;

/**
 * @author Daniel Hernandez Fco
 * <daniel.hernandez.job@gmail.com>
 * Simple Library wrapper for CURL, moreover this includes many functions that helps to consume REST APIs in an easier way.
 * 
 * 
 * 
 * This library
 */
class Client
{

      const POST = 'POST';
      const GET = 'GET';
      const PUT = 'PUT';

      const HEADER_AUTENTICATION = 'Authorization';

    public $httpMethod = self::GET;
    public $timeout;

    private $requestHeaders;
    private $responseHeaders;
    protected $url;
    /**
     * sets the Url
     *
     * @param string $url
     * @return void
     */
    public function setURL(  $url)
    {
        $this->url = $url;
    }

    protected $request = '';
    protected $httpcode;

    protected $curlError = '';
    protected $curlErrorCode;
    protected $curlStringError;
    /**
     * sets the http Code
     *
     * @param integer $code
     * @return void
     */
    protected function setHttpCode(  $code)
    {
        $this->httpcode = $code;
    }
    public function getHttpCode()
    {
        return $this->httpcode;
    }
    /**
     * Constructor
     *
     * @param integer $timeout
     */
    public function __construct( $timeout = 30)
    {
        $this->timeout = $timeout;
        $this->requestHeaders = array();
    }
    /**
     * sets token
     *
     * @param string $token
     * @return void
     */
    public function setToken( $token)
    {
        $this->addHeader(self::HEADER_AUTENTICATION, $token);
    }

    public function setUserAgent($userAgent){
        $this->addHeader(Headers::HEADER_CONTENT_USER_AGENT,$userAgent);
    }

    /**
     * adds a header
     *
     * @param string $headername
     * @param [type] $value
     * @return void
     */
    public function addHeader( $headername,  $value)
    {
        $this->requestHeaders[$headername] = $value;
    }
    /**
     * returns the headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->requestHeaders;
    }
    public function getRequestHeadersWithFormat()
    {
        $formatedArray = array();
        foreach ($this->getHeaders() as $keyHeader => $value) {
            $formatedArray[] = "{$keyHeader}: {$value}";
        }
        return $formatedArray;
    }
    public function hasHeaders()
    {
        return count($this->getHeaders()) > 0;
    }
    /**
     * getHTTPMethod
     *
     * @return string
     */
    public function getHTTPMethod()
    {
        return $this->httpMethod;
    }
    /**
     * setHttpMethod
     *
     * @param string $method
     * @return void
     */
    public function setHttpMethod(  $method)
    {

        $this->httpMethod = $method;
    }
    /**
     * setContentType
     *
     * @param string $contentType
     * @return void
     */
    public function setContentType(  $contentType)
    {
        $this->addHeader('Content-Type', $contentType);
    }

    protected function setError($error)
    {
        $this->curlError = $error;
    }
    public function getError()
    {
        return $this->curlError;
    }
    protected function setCurlErrorCode($curlErrorCode)
    {
        $this->curlErrorCode = $curlErrorCode;
    }
    protected function getCurlErrorCode()
    {
        return $this->curlErrorCode;
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }
    protected function setResponseHeaders($arrHeaders)
    {
        $this->responseHeaders = $arrHeaders;
    }
    private $decoder=null;
    /**
     * sets the decader callable
     *
     * @param callable $decoder
     * @return void
     */
    public function setDecoder( $decoder){
        $this->decoder=$decoder;
    }
    /**
     * main function , executes the request .
     *
     * @param string $method
     * @param string $url
     * @param array $params
     * @param callable $dataMapper
     * @return Response
     */
    public function doRequest(  $method,   $url,  $params = array(),  $dataMapper=null)
    {
        #options podria incluir tanto queryParams como URL
        $queryParams = '';
        if (isset($params['query'])) {
            if (!is_array($params['query'])) {
                throw new InvalidArgumentException("Invalid query , must be an array");
            }
            //cotninue
            $queryParams = http_build_query($params['query']);
        }
        if(empty($url)){
            throw new \InvalidArgumentException("URL esta vacia");
        }
        //queryParams
        if(StringUtils::endsWith($url,'?')){
            $url.$queryParams;
        }else{
            $url.'?'.$queryParams; #le agrego el query string
        }
        $ch = \curl_init($url);
        echo $url;
        #with this piece of code is posible to retrieve the Response headers
        $responseHeaders = [];
        curl_setopt(
            $ch,
            CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use (&$responseHeaders) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    {
                        return $len;
                    }

                $name = strtolower(trim($header[0]));
                if (!array_key_exists($name, $responseHeaders)) {
                    $responseHeaders[$name] = [trim($header[1])];
                } else {
                    $responseHeaders[$name][] = trim($header[1]);
                }

                return $len;
            }
        );

        $this->setHttpMethod($method);
        switch ($this->getHTTPMethod()) {
            case self::GET:
                break;
            case self::POST:
                curl_setopt($ch, CURLOPT_POST, 1);
                if (!empty($params)) {

                    if (isset($params['form_params'])) {
                        curl_setopt(
                            $ch,
                            CURLOPT_POSTFIELDS,
                            http_build_query(
                                $params['form_params']
                            )
                        );
                    } else if (isset($params['json'])) {
                        $jsonBODY = \json_encode($params['json']);
                        if($jsonBODY===False){
                            throw new \Exception("No se pudo serializar el body");
                        }
                        $this->addHeader('Content-Length', strlen($jsonBODY));
                        $this->setContentType('application/json');
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBODY);
                    }else if(isset($params['body'])){ #directamente el body
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $params['body']);
                    }
                }
                break;
            case self::PUT:
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                throw new Exception("Metodo no soportado");
        }

        if(isset($params['headers'])){
            if(!\is_array($params['headers'])){
                throw new Exception("Headers must be an array");
            }
            foreach ($params['headers'] as $headerName => $headerValue) {
                $this->addHeader($headerName,$headerValue);
            }
        }
        if ($this->hasHeaders()) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getRequestHeadersWithFormat());
            #echo "Agregando cabceras\n";
        }
        #curl_setopt($ch,CURLINFO_HEADER_OUT );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); #if the CURLOPT_RETURNTRANSFER option is set, it will return the result on success, FALSE on failure
        $httpResponse = curl_exec($ch);
        $this->setHttpCode(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        $response= new Response();
        $response->body=$httpResponse;
        $response->code=curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response->headers=$responseHeaders;#le asigno el header
        $response->setDecoder($this->decoder);
        $response->setDataMapper($dataMapper);
        if (false === $httpResponse) {
            $this->setError(curl_error($ch));
            $this->setCurlErrorCode(curl_errno($ch));
            $errorMessage = curl_strerror($this->getCurlErrorCode());
            curl_close($ch); # ensure to close 
            throw new Exception("{$errorMessage}:[" . $this->getError() ."URL: ".$this->url. "]");
        } else {
            $this->request = $httpResponse;
        }
        curl_close($ch);
        $this->setResponseHeaders($responseHeaders);

        #return $httpResponse;
        return $response;
    }
    /**
     * resolves And Executes the request. 
     *
     * @param string $method
     * @param string $url
     * @param array $options
     * @param callable $dataMapper
     * @return Response
     */
    protected function resolveAndExecute($method, $url = '',  $options = [], $dataMapper=null){
        //si inciia con / entonces es relativa a base,sino inicia asi entonces es absiluta
        if(StringUtils::startsWith($url,'/')){
            if(StringUtils::endsWith($this->url,'/')){
                $url=$this->url. \substr($url,1,\strlen($url));
            }else{
                $url=$this->url.$url;
            }             
        }
        return $this->doRequest($method,$url, $options,$dataMapper);
    }
    /**
     * executes a post request
     *
     * @param string $relativePath
     * @param array $options
     * @param callable $dataMapper
     * @return Response
     */
    public function post(  $relativePath,   $options=[],  $dataMapper=null)
    {
        return $this->resolveAndExecute(self::POST,$relativePath, $options,$dataMapper);
    }
    /**
     * executes a get request
     *
     * @param string $relativePath
     * @param array $options
     * @param callable $dataMapper
     * @return Response
     */
    public function get(  $relativePath,   $options=[],  $dataMapper=null)
    {
        //en este caso get acepta optiones ya que no envia body.
     return   $this->resolveAndExecute(self::GET,$relativePath, $options,$dataMapper);

    }

    protected function resolveCurlErrorCode()
    {

        switch ($this->getCurlErrorCode()) {
            case CURLE_OK:
                return 'All fine. Proceed as usual.';

            case CURLE_COULDNT_CONNECT:
                return 'Failed to connect() to host or proxy';

            case CURLE_COULDNT_RESOLVE_HOST:

                return 'CURLE_COULDNT_RESOLVE_HOST ';
            case CURLE_OPERATION_TIMEDOUT:
                return 'Operation timeout. The specified time-out period was reached according to the conditions.';
            case CURLE_OUT_OF_MEMORY:
                return 'A memory allocation request failed. This is serious badness and things are severely screwed up if this ever occurs.';
        }
    }
}
/**
 * Esta clase representa el response a bajo nivel
 */
class Response
{
    /**
     * HttpCode
     *
     * @var int
     */
    public $code;
    /**
     * Undocumented variable
     *
     * @var Headers
     */
    public $headers;

    public $body;

    public $curlErrorCode;

    public $curlStringError;

    private $callbackDecoder;

    private $dataMapper;
    /**
     * sets decoder
     *
     * @param callable|null $decoder
     * @return void
     */
    public function setDecoder($decoder){
        $this->callbackDecoder=$decoder;
    }
    public function setDataMapper($dataMapper){
        $this->dataMapper=$dataMapper;
    }
    /**
     * Devuelve el body mapeado.
     * Invocarlo solo cuando la operacion haya sido correcta (isSuccessful)
     * ya que en otro caso devolveria otra respuesta que no es propia del API.
     * 
     * Para obtener la respuesta del server sin mapear ver @see getRawBody() 
     * 
     * En caso de no poder deserializar el JSON se arroja una Excepcion
     * @return WsResponse
     */
    public function getBody(){
        //aqui debo invocar lo que DescargaSATSDK tiene como metodo para deserializar, buscar la manera de hacerlo,quiza un callback baste
        if(empty($this->body)){
            return $this->body;
        }
        //nota, debo de ver si la cabera de respiesta inckuye un content-type de json, si lo contiene entonces hacer la deserializacion, por sencillez lo hare asi directo
        //pero debo de validar
        if(null==$this->callbackDecoder){
            return json_decode($body,true);
        }else{
            $decoder=$this->callbackDecoder;
            return $decoder($this->body,$this->dataMapper);
        }
    }
    public function getRawBody(){
        return $this->body;
    }
    public function getContenType(){
        return $this->headers->getHeaderValue('Content-Type');
    }
    /**
     * Indica si fue exitoso basado en el codigo de estato del Protocolo HTTP
     * Successful 2XX 
     * @return boolean
     */
    public function isSuccessful(){
        return $this->code>=200&& $this->code <300;
    }
    /**
   * Indica si fue un error a nivel cliente basado en el codigo de estato del Protocolo HTTP
    * Client Error 4XX
     * @return boolean
     */
    public function isClientError(){
        return $this->code>=400&& $this->code <500;
    }
      
    /**
    * Indica si fue un error a nivel cliente basado en el codigo de estato del Protocolo HTTP
    * Server Error 5XX
     * @return boolean
     */
    public function isServerError(){
        return $this->code>=500&& $this->code <600;
    }

}
/**
 * Abstaacion de array headers, ya que sera comun que busque o agregue acabceras, es mejor tenerlo asi
 */
class Headers{

     const HEADER_Authorization='Authorization';
     const HEADER_CONTENT_TYPE='Content-Type';
     const HEADER_Server='Server';
     const HEADER_CONTENT_LENGTH='Content-Length';
     const HEADER_CONTENT_DISPOSITION='Content-Disposition';
     const HEADER_CONTENT_ENCODING='Content-Encoding';
     const HEADER_CONTENT_CONNECTION='Connection';

     const HEADER_CONTENT_CACHE_CONTROL='Cache-Control';
     const HEADER_CONTENT_DATE='Date';

     const HEADER_CONTENT_HOST='Host';
     const HEADER_CONTENT_ORIGIN='Origin';
     const HEADER_CONTENT_PRAGMA='Pragma';

     const HEADER_CONTENT_USER_AGENT='User-Agent';

     const HEADER_CONTENT_ALLOW='Allow';

     const HEADER_CONTENT_ACCESS_CONTROL_ALLOW_ORIGIN='Access-Control-Allow-Origin';
     const HEADER_CONTENT_ACCESS_CONTROL_ALLOW_METHODS='Access-Control-Allow-Methods';
     const HEADER_CONTENT_ACCESS_CONTROL_ALLOW_HEADERS='Access-Control-Allow-Headers';


    
    /**
     * Array de objetos Header
     *
     * @var Header[]
     */
    private $headers;
    /**
     * hasHeader
     *
     * @param string $headerName
     * @return boolean
     */
    public function hasHeader($headerName){
        if(empty($this->headers)){
            return false;
        }
        foreach ($this->headers as $header){
            if($header->name==$headerName){
                return true;
            }
        }
        return false;
    }
    /**
     * getHeaderValue
     *
     * @param string $headerName
     * @return string
     */
    public function getHeaderValue( $headerName){
        foreach ($this->headers as $header){
            if($header->name==$headerName){
                return $header->value;
            }
        }
        return false;
    }


    
}
class Header{
    private $name;

    private $value;
}

class StringUtils
{
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static   function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}
