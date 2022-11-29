###### `Rackspace Publish Files`

Tenemos que editar 2 ficheros pertenenciences a la librería opencloud para que se adapte a rackspace.
*vendor/php-opencloud/openstack/src/Identity/v2/Models/Catalog.php*
al inicio del metodo getServiceUrl añadir
```php
if($serviceType == "object-store"){
            $serviceName = "cloudFiles";
        }
```

Y en el fichero *vendor/php-opencloud/openstack/src/Common/Service/Builder.php*

En las importaciones añadir
```php
use App\Service\Rackspace\Api;
use App\Service\Rackspace\Service;
```
Y en torno a la línea 79 el método *createService* quedaría
```php
public function createService(string $namespace, array $serviceOptions = []): ServiceInterface
    {
        $options = $this->mergeOptions($serviceOptions);

        $this->stockAuthHandler($options);
        $this->stockHttpClient($options, $namespace);

        [$apiClass, $serviceClass] = $this->getClasses($namespace);
        if($apiClass==='OpenStack\Identity\v2\Api'){
            $serviceClass=Service::class;
            $apiClass=Api::class;
        }
        return new $serviceClass($options['httpClient'], new $apiClass());
    }
```

##Assetic Dump a Rackspace
Cambiamos dos ficheros para añadir la cabecera Allow-Origin a las cosas subidas

*vendor/php-opencloud/openstack/src/ObjectStore/v1/Params.php*

Añadir el método al final del fichero

```php
    public function accessControlAllowOrigin(): array
        {
            return [
                'location'    => self::HEADER,
                'sentAs'      => 'Access-Control-Allow-Origin',
                'type'        => self::STRING_TYPE,
                'description' => 'Access Control Allow Origin'
            ];
        }
```

*vendor/php-opencloud/openstack/src/ObjectStore/v1/Api.php*
Substituir el Método existente por este
```php
   public function putObject(): array
    {
        return [
            'method' => 'PUT',
            'path'   => '{containerName}/{+name}',
            'params' => [
                'containerName'      => $this->params->containerName(),
                'name'               => $this->params->objectName(),
                'content'            => $this->params->content(),
                'stream'             => $this->params->stream(),
                'contentType'        => $this->params->contentType(),
                'detectContentType'  => $this->params->detectContentType(),
                'copyFrom'           => $this->params->copyFrom(),
                'ETag'               => $this->params->etag(),
                'contentDisposition' => $this->params->contentDisposition(),
                'contentEncoding'    => $this->params->contentEncoding(),
                'deleteAt'           => $this->params->deleteAt(),
                'deleteAfter'        => $this->params->deleteAfter(),
                'metadata'           => $this->params->metadata('object'),
                'ifNoneMatch'        => $this->params->ifNoneMatch(),
                'objectManifest'     => $this->params->objectManifest(),
                'Access-Control-Allow-Origin'     => $this->params->accessControlAllowOrigin(),
            ],
        ];
    }
```