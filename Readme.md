# ![Sifei](https://www.sifei.com.mx/web/image/res.company/1/logo?unique=38c7250)



# SDK API consumo de descargaSAT
Este repositorio incluye en el SDK en PHP y ejemplos para el consumo de endpoints del API REST de descargaSAT

[DescargaSAT](https://descargasat.sifei.com.mx/)

[Sifei](https://www.sifei.com.mx/) 

[Manual de API](https://www.sifei.com.mx/slides/slide/manual-api-aci-descargasat-128) 


Se requiere como mínimo PHP  5.6 
Testeado en php 5.6 y superior (7+)

- Es necesario tener habilitada la extension CURL para poder ejecutar las peticiones.
- Se incluye un autoloader y un archivo autoload_namespaces.php para la resolucion de espacios de nombre a archivos php por lo que debera importarse el archivo dhf_autoload_.php en en punto de entrada de ejecucion de php.







# Conectate rápidamente

La clase principal es DescargaSATSDK, solo 1) instancia un objeto apartir de ella, 2) establece el token y 3)  coloca la URL (por defecto apunta a producción).

```php
$sdk = new DescargaSATSDK();
//establecemos el token
$sdk->setToken(self::$token);
//establecemos la URL del entorno
$sdk->setURL(DescargaSATSDK::$BASE_PRODUCTION_URL);
#.... Invocamos los metodos disponibles en el SDK.
```
# Ejemplos

## Ejemplo Descargar CFDI por UUID:


```php
 public static function EjemploObtenerCFDIPorUUID()
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
```

## Ejemplo programar Descarga

```php
public static function EjemploProgramarDescarga()
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
```

## Ejemplo Consulta de CFDI (varios parametros)

```php
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

```


## Ejemplo descarga de CFDI

```php
public static function EjemploDescargarCFDIPorUUID()
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

```

## Ejemplo Verificar SI existe CFDI


```php
public static function EjemploExisteCFDI()
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
```