<?php

namespace OpenStack\ObjectStore\v1;

use OpenStack\Common\Api\AbstractApi;

class Api extends AbstractApi
{
    public function __construct()
    {
        $this->params = new Params();
    }

    public function getAccount()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => [
                'limit'     => $this->params->limit,
                'marker'    => $this->params->marker,
                'endMarker' => $this->params->endMarker,
                'prefix'    => $this->params->prefix,
                'delimiter' => $this->params->delimiter,
                'newest'    => $this->params->newest,
            ],
        ];
    }

    public function postAccount()
    {
        return [
            'method' => 'POST',
            'path'   => '',
            'params' => [
                'tempUrlKey'  => $this->params->tempUrlKey,
                'tempUrlKey2' => $this->params->tempUrlKey2,
                'metadata'    => $this->params->metadataParam('account'),
            ],
        ];
    }

    public function headAccount()
    {
        return [
            'method' => 'HEAD',
            'path'   => '',
            'params' => [],
        ];
    }

    public function getContainer()
    {
        return [
            'method' => 'GET',
            'path'   => '{name}',
            'params' => [
                'name'      => $this->params->containerName,
                'limit'     => $this->params->limit,
                'marker'    => $this->params->marker,
                'endMarker' => $this->params->endMarker,
                'prefix'    => $this->params->prefix,
                'path'      => $this->params->path,
                'delimiter' => $this->params->delimiter,
                'newest'    => $this->params->newest,
            ],
        ];
    }

    public function putContainer()
    {
        return [
            'method' => 'PUT',
            'path'   => '{name}',
            'params' => [
                'name'              => $this->params->containerName,
                'readAccess'        => $this->params->readAccessParam('container'),
                'writeAccess'       => $this->params->writeAccessParam('container'),
                'metadata'          => $this->params->metadataParam('container'),
                'syncTo'            => $this->params->syncToParam('container'),
                'syncKey'           => $this->params->syncKeyParam('container'),
                'versionsLocation'  => $this->params->versionsLocation,
                'bytesQuota'        => $this->params->bytesQuota,
                'countQuota'        => $this->params->countQuota,
                'webDirectoryType'  => $this->params->webDirType,
                'detectContentType' => $this->params->detectContentType,
            ],
        ];
    }

    public function deleteContainer()
    {
        return [
            'method' => 'DELETE',
            'path'   => '{name}',
            'params' => [
                'name' => $this->params->containerName,
            ],
        ];
    }

    public function postContainer()
    {
        return [
            'method' => 'POST',
            'path'   => '{name}',
            'params' => [
                'name'                   => $this->params->containerName,
                'readAccess'             => $this->params->readAccessParam('container'),
                'readAccess'             => $this->params->readAccessParam('container'),
                'writeAccess'            => $this->params->writeAccessParam('container'),
                'metadata'               => $this->params->metadataParam('container'),
                'removeMetadata'         => $this->params->metadataParam('container', true),
                'syncTo'                 => $this->params->syncToParam('container'),
                'syncKey'                => $this->params->syncKeyParam('container'),
                'versionsLocation'       => $this->params->versionsLocation,
                'removeVersionsLocation' => $this->params->removeVersionsLocation,
                'bytesQuota'             => $this->params->bytesQuota,
                'countQuota'             => $this->params->countQuota,
                'webDirectoryType'       => $this->params->webDirType,
                'detectContentType'      => $this->params->detectContentType,
            ],
        ];
    }

    public function headContainer()
    {
        return [
            'method' => 'HEAD',
            'path'   => '{name}',
            'params' => [
                'name' => $this->params->containerName,
            ],
        ];
    }

    public function getObject()
    {
        return [
            'method' => 'GET',
            'path'   => '{container}/{name}',
            'params' => [
                'container'         => $this->params->containerName,
                'name'              => $this->params->objectName,
                'range'             => $this->params->range,
                'ifMatch'           => $this->params->ifMatch,
                'ifNoneMatch'       => $this->params->ifNoneMatch,
                'ifModifiedSince'   => $this->params->ifModifiedSince,
                'ifUnmodifiedSince' => $this->params->ifUnmodifiedSince,
            ],
        ];
    }

    public function putObject()
    {
        return [
            'method' => 'PUT',
            'path'   => '{container}/{name}',
            'params' => [
                'container'          => $this->params->containerName,
                'name'               => $this->params->objectName,
                'contentType'        => $this->params->contentType,
                'detectContentType'  => $this->params->detectContentType,
                'copyFrom'           => $this->params->copyFrom,
                'ETag'               => $this->params->etag,
                'contentDisposition' => $this->params->contentDisposition,
                'contentEncoding'    => $this->params->contentEncoding,
                'deleteAt'           => $this->params->deleteAt,
                'deleteAfter'        => $this->params->deleteAfter,
                'metadata'           => $this->params->metadataParam('object'),
                'ifNoneMatch'        => $this->params->ifNoneMatchParam,
            ],
        ];
    }

    public function copyObject()
    {
        return [
            'method' => 'COPY',
            'path'   => '{container}/{name}',
            'params' => [
                'container'          => $this->params->containerName,
                'name'               => $this->params->objectName,
                'destination'        => $this->params->destination,
                'contentType'        => $this->params->contentType,
                'contentDisposition' => $this->params->contentDisposition,
                'contentEncoding'    => $this->params->contentEncoding,
                'metadata'           => $this->params->metadataParam('object'),
                'freshMetadata'      => $this->params->freshMetadata,
            ],
        ];
    }

    public function deleteObject()
    {
        return [
            'method' => 'DELETE',
            'path'   => '{container}/{name}',
            'params' => [
                'container' => $this->params->containerName,
                'name'      => $this->params->objectName,
            ],
        ];
    }

    public function headObject()
    {
        return [
            'method' => 'HEAD',
            'path'   => '{container}/{name}',
            'params' => [
                'container' => $this->params->containerName,
                'name'      => $this->params->objectName,
            ],
        ];
    }

    public function postObject()
    {
        return [
            'method' => 'POST',
            'path'   => '{container}/{name}',
            'params' => [
                'container'          => $this->params->containerName,
                'name'               => $this->params->objectName,
                'metadata'           => $this->params->metadataParam('object'),
                'deleteAt'           => $this->params->deleteAt,
                'deleteAfter'        => $this->params->deleteAfter,
                'contentDisposition' => $this->params->contentDisposition,
                'contentEncoding'    => $this->params->contentEncoding,
                'contentType'        => $this->params->contentType,
                'detectContentType'  => $this->params->detectContentType,
            ],
        ];
    }
}