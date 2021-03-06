<?php
/**
 * Esta clase contiene los ejemplos para conectarse y consumir el API de DescargaSAT.
 * 
 * Dependiendo de tu estilo de programacion importa esta linea en el punto de entrada(entry point) de tu aplicacion
 * o bien, en cada archivo raiz de ejecucion php donde se requiera las clases ofrecidas por el SDK.
 * 
 * Depending on the estructure of your project you must import this line in the entry point of your php application
 *  The clases can be imported with any of the next two lines:
 *  >require "./../loader/dhf_autoload_.php"; #autoloader, usa esto si no usas composer!. Put this line if you don't use composer on your project.
 *  or:
 *  >require "./../vendor/autoload.php";  #Si ya utilizas composer coloca esta linea.If you already use composer put this line! 
 */

#require "./../loader/dhf_autoload_.php"; #via custom autoloader
require "./../vendor/autoload.php"; #via composer
use DHF\Sifei\DescargaSAT\SDK\DescargaSATSDK;
use DHF\Sifei\DescargaSAT\SDK\Models\DescargaProgramada;
use DHF\Sifei\DescargaSAT\SDK\Models\DescargaProgramadaRequest;
use DHF\Sifei\DescargaSAT\SDK\Models\ParametersCFDIQuery;
use DHF\Sifei\DescargaSAT\SDK\Models\ParametersDownloadCFDI;

class App
{
    public static $CFDIUUID = ''; # UUID de CFDI usado en algunos ejemplos|UUID used in some examples.
    public static $descargaUUID = "20181016.00.00.00-2018101623.59.59";
    public static $token = 'token'; #token a usar en la cabecera de la peticion. This is the token to send in the http header.
    public static $URL = '';
 
    const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s';
    const DEFAULT_TO_STRING_FORMAT_TO_NAME = 'Y-m-d H_i_s';

    public static function TestProgramarDescarga()
    {
        $descarga = new DescargaProgramadaRequest();
        $descarga->setFechaInicial(new \DateTime("2018-12-01"));
        $descarga->setFechaFinal(new \DateTime("2018-12-07"));

        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);
        try {
            $response = $sdk->ProgramarDescarga($descarga);
            if (!$response->isSuccessful()) {
                echo $response->getRawBody();
                return;
            }
            $body = $response->getBody();
            if ($body->isStatusSuccess()) {
                //en este caso data es un arreglo compuestos de 4 propieadades o elementos
                $data = $body->getData();
                #mensaje-descripcion de la operacion (string)
                echo $data->resultado;
                #errores:

                /*
                exitosos: arreglo de uuid creados, estos pueden guardarse para su posterior
                 *verificacion(consumir el servicio de consula detalle para saber si su estado ya esta en completado)
                 */
                print_r($data->exitosos);
                #repetidos: arreglo de uuid de descargas previamente realizadas
                print_r($data->repetidos);
                #diasTotalesDeDescarga: cantidad de dias entre la fecha inicial y final

                echo "diasTotalesDeDescarga:" . $data->diasTotalesDeDescarga . "\n";
                #totalDiasNuevosProgramados: cantidad de dias nuevos(efectivamente añadidos)

                echo "totalDiasNuevosProgramados:" . $data->totalDiasNuevosProgramados . "\n";
                #diasyaprogramados : cantidad de dias ya programados

                echo "diasyaprogramados:" . $data->diasyaprogramados . "\n";
                #tipoDescargaName
                echo "tipoDescargaName:" . $data->tipoDescargaName . "\n";
            } else {
                //consumo invalido o descarga ya existente, codigo y mensaje del API
                echo "codigo: " . $body->getCode() . "\n";
                echo "message: " . $body->getMessage() . "\n";
            }
        } catch (Exception $e) {
            print_r($e);
        }
    }
    public static function TestConsultarDescargaProgramada()
    {
        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);

        try {
            #le asignamos un uuid de la descarga a consultar.
            $response = $sdk->ConsultarDescarga(self::$descargaUUID);
            if ($response->isSuccessful()) {
                $body = $response->getBody();
                #si es succes entonces se encontro y se puede obtener por getData
                if ($body->isStatusSuccess()) {
                    $data = $body->getData();
                    print_r($data);
                } else { #error
                if ($body->isCodeNoEncontrado()) {
                    echo "No existe la descarga con el UUID";
                    echo "codigo: " . $body->getCode() . "\n";
                    echo "message: " . $body->getMessage() . "\n";
                } else { #otro error:
                //consumo invalido, codigo y mensaje del API
                    echo "codigo: " . $body->getCode() . "\n";
                    echo "message: " . $body->getMessage() . "\n";
                }
                }
            } else {
                #imprimomos el error inesperado del servidor.
                echo $response->getRawBody();
            }

        } catch (Exception $e) {
            #en caso de un error de red, de dns, de puerto van, asi mismo reglas de validacion del SDK
            #sin embargo una vez que validez tu logica las excepciones orginanidas por la validacion del SDK dejaran de ocurrir.
            echo "Exception:\n";
            print_r($e);
        }
    }
    public static function TestReactivarDescargaProgramada()
    {
        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);
        $sdk->setURL(DescargaSATSDK::$BASE_PRODUCTION_URL);
        try {
            #le asignamos un uuid de la descarga a consultar.
            #$response = $sdk->ReactivarDescarga("20180823.00.00.00-2018082323.59.59");
            #$response = $sdk->ReactivarDescarga("20181026.00.00.00-2018102623.59.59");
            $response = $sdk->ReactivarDescarga("20180803.00.00.00-2018080323.59.59");
            
            if (!$response->isSuccessful()) {
                
            
                #imprimomos el error inesperado del servidor.
                echo "ERROR:\n";
                echo $response->getRawBody();
                return;
            }
            $body = $response->getBody();
            print_r($body);
            #si es succes entonces se reactivo
            if ($body->isStatusSuccess()) {
                #data en este caso solo es un string conteniendo un mensaje de ok.
                $data = $body->getData();
                echo ($data);
            } else { #error
                if ($body->isCodeNoEncontrado()) {
                    echo "No existe la descarga con el UUID para ser reactivada";
                    echo "codigo: " . $body->getCode() . "\n";
                    echo "message: " . $body->getMessage() . "\n";
                } else { #otro error:
                //consumo invalido, codigo y mensaje del API
                    echo "\n codigo: " . $body->getCode() . "\n";
                    echo "message: " . $body->getMessage() . "\n";
                }
            }
        } catch (Exception $e) {
            #en caso de un error de red, de dns, de puerto van, asi mismo reglas de validacion del SDK
            #sin embargo una vez que validez tu logica las excepciones orginanidas por la validacion del SDK dejaran de ocurrir.
            echo "Exception:\n";
            print_r($e);
        }
    }

    public static function TestConsultarLasDescargasDiarias()
    {
        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);
        #$sdk->setURL(DescargaSATSDK::$BASE_PRODUCTION_URL);
        try {
            #le asignamos un uuid de la descarga a consultar.

            $parameterDescargaDiarias = [
               'estadoDescarga' => DescargaProgramada::ESTADO_ALL,
               #'fechaInicial' => '2019-01-01 00:00:01',
               #'fechaFinal' => '2017-01-05 12:00:00',
               #'estadoDescarga' => DescargaProgramada::PROCESADA_POR_PORTAL,
            ];

            $response = $sdk->ConsultarLasDescargasDiarias($parameterDescargaDiarias);
            if (!$response->isSuccessful()) {
                #imprimomos el error inesperado del servidor.
                echo "ERROR:\n";
                echo $response->getRawBody();
                return;
            }
            $body = $response->getBody();
            #si es succes entonces todo fue bien.
            if (!$body->isStatusSuccess()) {
            #error
            //consumo invalido, codigo y mensaje del API
                echo "codigo: " . $body->getCode() . "\n";
                echo "message: " . $body->getMessage() . "\n";
                return;
            }
            /*
            *data en este caso data puede ser null si no hay descargas(en caso de ser nueva) o contiene un array con
            *los datos basicos de las descargas
            */
            $data = $body->getData();
            if (null == $data) {
                #aun no hay descargas
            } else {
                #print_r($data);
                foreach ($data as $descarga) {
                    echo "uuid {$descarga->uuid}\n";
                    echo " {$descarga->estado}";
                    if ($descarga->isEstadoCompletado()) {
                        echo "Completado\n";
                    }
                }
            }
        } catch (Exception $e) {
            #en caso de un error de red, de dns, de puerto van, asi mismo reglas de validacion del SDK
            #sin embargo una vez que validez tu logica las excepciones orginanidas por la validacion del SDK dejaran de ocurrir.
            echo "Exception:\n";
            print_r($e);
        }
    }
    #--------------------------------CFDI-------------------------------------------------

    public static function TestObtenerCFDIPorUUID()
    {
        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);
        try {
            #le asignamos un uuid de la descarga a consultar.
            $response = $sdk->ObtenerCFDIPorUUID(self::$CFDIUUID);
            if (!$response->isSuccessful()) {
             
            
                #imprimomos el error inesperado del servidor.
                echo "ERROR:\n";
                echo $response->getRawBody();
                return;
            }
            $body = $response->getBody();
            #si es succes entonces todo fue bien.
            if ($body->isStatusSuccess()) {
                #en este caso data es la info de un CFDI
                $data = $body->getData();
                print_r($data);

            } else { #error
                if ($body->isCodeNoEncontrado()) {
                    echo "No existe CFDI con el UUID  ";
                    echo "codigo: " . $body->getCode() . "\n";
                    echo "message: " . $body->getMessage() . "\n";
                } else { #otro error:
                //consumo invalido, codigo y mensaje del API
                    echo "codigo: " . $body->getCode() . "\n";
                    echo "message: " . $body->getMessage() . "\n";
                }
            }
        } catch (Exception $e) {
            #en caso de un error de red, de dns, de puerto van, asi mismo reglas de validacion del SDK
            #sin embargo una vez que validez tu logica las excepciones orginanidas por la validacion del SDK dejaran de ocurrir.
            echo "Exception:\n";
            print_r($e);
        }
    }

    public static function TestDescargarCFDIPorUUID()
    {
        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);
        try {
            #le asignamos un uuid de la descarga a consultar.
            $response = $sdk->DescargarCFDIPorUUID(self::$CFDIUUID);
            if (!$response->isSuccessful()) {
                #imprimomos el error inesperado del servidor.
                echo "ERROR:\n";
                echo $response->getRawBody();
                return;
            }
            $body = $response->getBody();
            #si es succes entonces todo fue bien.
            if ($body->isStatusSuccess()) {
                #en este caso data es el XML codificado en base64
                $data = $body->getData();
                echo $data;
                $xml = base64_decode($data);
                echo $xml;

            } else { #error
                if ($body->isCodeNoEncontrado()) {
                    echo "No existe CFDI con el UUID  ";
                    echo "codigo: " . $body->getCode() . "\n";
                    echo "message: " . $body->getMessage() . "\n";
                } else { #otro error:
                //consumo invalido, codigo y mensaje del API
                    echo "codigo: " . $body->getCode() . "\n";
                    echo "message: " . $body->getMessage() . "\n";
                }
            }
        } catch (Exception $e) {
            #en caso de un error de red, de dns, de puerto van, asi mismo reglas de validacion del SDK
            #sin embargo una vez que validez tu logica las excepciones orginanidas por la validacion del SDK dejaran de ocurrir.
            echo "Exception:\n";
            // Couldn't connect to server
            print_r($e);
        }
    }

    public static function buscarCFDI()
    {
        $rfcEmisor = "";
        $rfcReceptor = "";
        #Este método permite obtener un resumen de todos los CFDI que cumplan un criterio de búsqueda.
        $sdk = new DescargaSATSDK();
        #token
        $sdk->setToken(self::$token);
        #$sdk->setToken();
        #se crea el objeto de consulta:
        $params = new ParametersCFDIQuery();
        #formato valido de fecha(EL SDK se encarga de enviar la fecha en el formato correcto)
        #$params->setFechaFinal(new DateTime("2018-12-01"));

        $params->setRfcReceptor($rfcReceptor);
        #Indica el numero maximo de resultados a devolver
        $params->setLimit(5);
        #offset indica a partir de que elemento se iniciara la busqueda de CFDI
        $params->setOffset(10);
        #optionalmenete le pondemos indicar que sean de tipo nomina
        $sdk->setURL(DescargaSATSDK::$BASE_PRODUCTION_URL);
        $params->setEfectoCFDI("P");

        try {
            #le asignamos un uuid de la descarga a consultar.
            $response = $sdk->BuscarCFDI($params);
            if ($response->isSuccessful()) {
                $body = $response->getBody();
                #si es succes entonces todo fue bien.
                if ($body->isStatusSuccess()) {
                    # data es un ojeto de tipo DataResponseCFDIConsulta
                    $data = $body->getData();
                    #count contiene el numero de elementos devueeltos, el lenght del array de CFDI
                    echo "\nNumero de cfdi devueltos: " . $data->count . "\n";
                    # total es el numero total de CFDi que se encontraron, es un campo informativo
                    echo "Total de resultados encontrados: " . $data->total . "\n";
                    #si el arreglo es mayor a 0 entonnces lo recorremos
                    if ($data->count > 0) {
                        foreach ($data->cfdi as $cfdi) {
                            #cada elemento del arreglo es una clase CFDIModelAPI
                            echo "-------------------------------------------------\n";
                            echo "uuid: " . $cfdi->uuid . "\n";
                            echo "rfcEmisor: " . $cfdi->rfcEmisor . "\n";
                            echo "rfcReceptor: " . $cfdi->rfcReceptor . "\n";
                            echo "fechaDescarga: " . $cfdi->fechaDescarga . "\n";
                            echo "total: " . $cfdi->total . "\n";
                            echo "fechaEmision: " . $cfdi->fechaEmision . "\n";
                            echo "pacquecertifico: " . $cfdi->pacquecertifico . "\n";
                            echo "fechaCertificacion: " . $cfdi->fechaCertificacion . "\n";
                            echo "estado: " . $cfdi->estado . "\n";
                            echo "efecto: " . $cfdi->efecto . "\n";

                            if ($cfdi->isEfectoNomina()) {
                                echo "Es nomina\n";
                            } else if ($cfdi->isEfectoIngreso()) {
                                echo "Es Ingreso\n";
                            } else if ($cfdi->isEfectoPago()) {
                                echo "Es Pago";
                            } else if ($cfdi->isEfectoEgreso()) {
                                echo "Es Egreso" . PHP_EOL;
                            } else if ($cfdi->isEfectoTraslado()) {
                                echo "Es Traslado" . PHP_EOL;
                            }
                        }
                    }

                } else { #error
                if ($body->isCodeNoEncontrado()) {
                    echo "No existe CFDI con el UUID  ";
                    echo "codigo: " . $body->getCode() . "\n";
                    echo "message: " . $body->getMessage() . "\n";
                } else { #otro error:
                //consumo invalido, codigo y mensaje del API
                    echo "codigo: " . $body->getCode() . "\n";
                    echo "message: " . $body->getMessage() . "\n";
                }
                }
            } else {
                #imprimomos el error inesperado del servidor.
                echo "ERROR:\n";
                echo $response->getRawBody();
            }
        } catch (Exception $e) {
            #en caso de un error de red, de dns, de puerto van, asi mismo reglas de validacion del SDK
            #sin embargo una vez que validez tu logica las excepciones orginanidas por la validacion del SDK dejaran de ocurrir.
            echo "Exception:\n";
            // Couldn't connect to server
            print_r($e);
        }
    }
    public static function descargarCFDIPorConsulta()
    {
        $rfcEmisor = "";
        $rfcReceptor = "";

        $sdk = new DescargaSATSDK();
        #token
        $sdk->setToken(self::$token);
        $sdk->setURL(self::$URL);
        #se crea el objeto de consulta:
        $params = new ParametersDownloadCFDI();
        #fecha inical es requerida
        $params->setFechaInicial(new DateTime("2018-01-01"));

        #fecha final es requerida en este metodo.formato valido de fecha(EL SDK se encarga de enviar la fecha en el formato correcto)
        $params->setFechaFinal(new DateTime("2019-01-03"));
        #indicamos que sean emitidos por nuestro rfc
        $params->setOrigenEmitidos();
        #Indica el numero maximo de resultados a devolver

        #optionalmenete le pondemos indicar que sean de tipo nomina
        $params->setEfectoCFDI("P");

        try {
            #le asignamos un uuid de la descarga a consultar.
            $response = $sdk->DescargarCFDIConsulta($params);
            if (!$response->isSuccessful()) {
               
            
                #imprimomos el error inesperado del servidor.
                echo "ERROR:\n";
                echo $response->getRawBody();
                return;
            }
            $body = $response->getBody();
            #si es succes entonces todo fue bien.
            if ($body->isStatusSuccess()) {
                # data es un ojeto de tipo DataResponseCFDIConsulta
                $data = $body->getData();
                #echo $data;
                file_put_contents('archivo' . (new DateTime())->format(self::DEFAULT_TO_STRING_FORMAT_TO_NAME) . '.zip', base64_decode($data));
                return;

            } 
             #error
            if ($body->isCodeNoEncontrado()) {
                echo "No existen CFDI con en la consulta dada  ";
                echo "codigo: " . $body->getCode() . "\n";
                echo "message: " . $body->getMessage() . "\n";
            } else { #otro error:
                //consumo invalido, codigo y mensaje del API
                echo "codigo: " . $body->getCode() . "\n";
                echo "message: " . $body->getMessage() . "\n";
            }
            
        } catch (Exception $e) {
            #en caso de un error de red, de dns, de puerto van, asi mismo reglas de validacion del SDK
            #sin embargo una vez que validez tu logica las excepciones orginanidas por la validacion del SDK dejaran de ocurrir.
            echo "Exception:\n";
            // Couldn't connect to server
            print_r($e);
        }
    }
    public static function TestExisteCFDI()
    {
        //instanciamos el SDK
        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);
        //le pasamos el UUID y ejecutamos el consumo del WS
        $response = $sdk->ExisteCFDI(self::$CFDIUUID);
        //verificamos si la operacion HTTP fue correcta
        if ($response->isSuccessful()) {
            //con get Body se mapea la respuesta a un objeto WsResponse
            $body = $response->getBody();
            //se verifica asi a nivel de API todo fue correcto
            if ($body->isStatusSuccess()) {
                //en esta operacion Data es un boleeano.
                $existe = $body->getData();
                if ($existe === true) {
                    echo "Existe CFDI";
                } else if ($existe === false) {
                    echo "no existe";
                } else {
                    echo "otra opcion";
                }
            } else {
                //consumo invalido
                echo "codigo: " . $body->getCode() . "\n";
                echo "message: " . $body->getMessage() . "\n";
            }
        } else {
            echo $response->getRawBody();
        }
    }
    public static function TestSubirEFirma()
    {
        //instanciamos el SDK
        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);
        $params = [];
        $params['cert'] = base64_encode(file_get_contents(self::$CERT_PATH));
        $params['key'] = base64_encode(file_get_contents(self::$KEY_PATH));
        $params['pass'] = self::$KEY_PASS;

        //le pasamos el UUID y ejecutamos el consumo del WS
        $response = $sdk->SubirEFirma($params);
        //verificamos si la operacion HTTP fue correcta
        if ($response->isSuccessful()) {
            //con get Body se mapea la respuesta a un objeto WsResponse
            $body = $response->getBody();
            //se verifica asi a nivel de API todo fue correcto
            if ($body->isStatusSuccess()) {
                //en esta operacion Data es un objeto compuesto por las propiedades:
                /**
                 * rfc
                 * noSerie
                 * validFrom
                 * validTo
                 */
                $certficado = $body->getData();

            } else {
                //consumo invalido
                echo "codigo: " . $body->getCode() . "\n";
                echo "message: " . $body->getMessage() . "\n";
            }
        } else {
            echo $response->getRawBody();
        }
    }

    public static function TestDesbloquearCFDI()
    {
        //instanciamos el SDK
        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);
        $sdk->setURL(DescargaSATSDK::$BASE_PRODUCTION_URL);
        #unico campo que es obligatorio, sino un error de parametros sera devuelto.
        $params = [
            'TipoDesbloqueo' => DescargaSATSDK::DESBLOQUEO_META,
        ];
        $response = $sdk->DesbloquearCFDI($params);
        if (!$response->isSuccessful()) {
            
            echo $response->getRawBody();
            return;
        }
        $body = $response->getBody();

        if ($body->isStatusSuccess()) {

            $numeroItemsDesbloqueados = $body->getData();
            echo "\n Se desbloquearon: {$numeroItemsDesbloqueados}";
        } else {
            //consumo invalido
            echo "\ncodigo: " . $body->getCode() . "\n";
            echo "message: " . $body->getMessage() . "\n";
        }


    }

    public static $KEY_PATH;
    public static $KEY_PASS;
    public static $CERT_PATH;

    public static function TestEstablecerHoras()
    {
        //instanciamos el SDK
        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);
        
        #Ambos campos son obligatorios, sino un error de parametros sera devuelto.
        $params = [
            'horasEsperaWs' => "36",
            'horasEsperaPortal' => '36',
        ];
        $response = $sdk->EstablecerHorasEspera($params);
        if ($response->isSuccessful()) {
            $body = $response->getBody();
            #si fue success significa que se acepto el cambio de horas de espera.
            if ($body->isStatusSuccess()) {              
                $dataConfiguracion = $body->getData();
                echo "\nMetodo descarga: " . $dataConfiguracion['metodoDescarga'] . "\n";
                echo "Horas espera ws: " . $dataConfiguracion['horasEsperaWs'] . "\n";
                echo "Horas espera Portal: " . $dataConfiguracion['horasEsperaPortal'] . "\n";
                echo "mensaje: " . $dataConfiguracion['mensaje'] . "\n";
            } else {
                //consumo invalido
                echo "\ncodigo: " . $body->getCode() . "\n";
                echo "message: " . $body->getMessage() . "\n";
            }
        } else {
            echo $response->getRawBody();
        }
    }

    public static function TestConsultarConfiguracionAPI()
    {
        //instanciamos el SDK
        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);
        $sdk->setURL(DescargaSATSDK::$BASE_PRODUCTION_URL);

        $response = $sdk->consultarConfiguracion();
        if (!$response->isSuccessful()) {
            echo $response->getRawBody();
            return;
        }
        $body = $response->getBody();
        #si fue success significa que se acepto el cambio de horas de espera.
        if ($body->isStatusSuccess()) {
            #data en este caso es un objeto con 3 campos: con un mensaje de confirmacion.
            $dataConfiguracion = $body->getData();
            echo "\nMetodo descarga: " . $dataConfiguracion['metodoDescarga'] . "\n";
            echo "Horas espera ws: " . $dataConfiguracion['horasEsperaWs'] . "\n";
            echo "Horas espera Portal: " . $dataConfiguracion['horasEsperaPortal'] . "\n";

        } else {
            //consumo invalido
            echo "\ncodigo: " . $body->getCode() . "\n";
            echo "message: " . $body->getMessage() . "\n";
        }

    }
    public static function TestModificarMetodoDescargaConfiguracionAPI()
    {
        //instanciamos el SDK
        $sdk = new DescargaSATSDK();
        //establecemos el token
        $sdk->setToken(self::$token);
        $params = [
            'metodoDescarga' => DescargaSATSDK::METODO_DESCARGA_WS,
            
        ];
        $response = $sdk->EstablecerMetodoDescarga($params);
        if (!$response->isSuccessful()) {
          
        
            echo $response->getRawBody();
            return;
        }
        $body = $response->getBody();
        #si fue success significa que se acepto el cambio de horas de espera.
        if ($body->isStatusSuccess()) {                
            #data en este caso es un objeto con 4 campos
            $dataConfiguracion = $body->getData();
            echo "\nMetodo descarga: " . $dataConfiguracion['metodoDescarga'] . "\n";
            echo "Horas espera ws: " . $dataConfiguracion['horasEsperaWs'] . "\n";
            echo "Horas espera Portal: " . $dataConfiguracion['horasEsperaPortal'] . "\n";
            echo "mensaje: " . $dataConfiguracion['mensaje'] . "\n";
        } else {
            //consumo invalido, no se actualizo.
            echo "\ncodigo: " . $body->getCode() . "\n";
            echo "message: " . $body->getMessage() . "\n";
        }

    }
    /**
     * Esta funcion carga los datos desde un archivo PHP, esto se hace para desacoplar la logica de los datos usados.
     *
     * @return void
     */
    public static function loadConfig(){
        $array = include_once "../config_app_test.php";
        self::$KEY_PATH = $array['key_path']; #ruta del archivo KEY
        self::$KEY_PASS = $array['key_pass'];  #Pass del KEY
        self::$CERT_PATH = $array['cert_path'];       
        self::$token=$array['token']; #token
        self::$URL=$array['URL'];     #URL de entorno
        #print_r($array);
        
    }

    /**
     * Aqui se listan todos los ejemplos (comentar/descomentar segun se quiera probar)
     *
     * @return void
     */
    public static function main()
    {
       self::loadConfig();
        
        # self::TestProgramarDescarga();
        
       # self::TestReactivarDescargaProgramada();
        /*
        self::TestConsultarDescargaProgramada();

        self::TestReactivarDescargaProgramada();
       

       */
        
         
        self::$CFDIUUID=" ";
       // self::TestObtenerCFDIPorUUID();
          
        /*
        self::$CFDIUUID=" ";
        self::TestDescargarCFDIPorUUID();
         */
        # self::buscarCFDI();

         self::descargarCFDIPorConsulta();
        #   self::TestExisteCFDI();
      
      
        #self::TestSubirEFirma();
        
         /*
        self::TestDesbloquearCFDI();
        */
      //  self::TestConsultarConfiguracionAPI();

         
        /*
         self::TestModificarMetodoDescargaConfiguracionAPI();
         self::TestEstablecerHoras();
         */
        
       //  self::TestConsultarLasDescargasDiarias();
    }
}

#We invoke the main static function.
App::main();
