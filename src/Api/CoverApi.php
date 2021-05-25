<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use function GuzzleHttp\json_encode;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use Mapsred\MangadexSDK\ApiException;
use Mapsred\MangadexSDK\Configuration;
use Mapsred\MangadexSDK\HeaderSelector;
use Mapsred\MangadexSDK\Model\CoverEdit;
use Mapsred\MangadexSDK\Model\CoverList;
use Mapsred\MangadexSDK\Model\CoverResponse;
use Mapsred\MangadexSDK\Model\ErrorResponse;
use Mapsred\MangadexSDK\Model\ModelInterface;
use Mapsred\MangadexSDK\Model\Response;
use Mapsred\MangadexSDK\ObjectSerializer;
use RuntimeException;
use SplFileObject;

final class CoverApi
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var HeaderSelector
     */
    private $headerSelector;

    /**
     * @var int Host index
     */
    private $hostIndex;
    /**
     * @var string
     */
    private const COVER_ID = 'coverId';
    /**
     * @var string
     */
    private const APPLICATION_JSON = 'application/json';
    /**
     * @var string
     */
    private const NAME = 'name';
    /**
     * @var string
     */
    private const CONTENTS = 'contents';
    /**
     * @var string
     */
    private const CONTENT_TYPE = 'Content-Type';
    /**
     * @var string
     */
    private const AUTHORIZATION = 'Authorization';
    /**
     * @var string
     */
    private const USER_AGENT = 'User-Agent';
    /**
     * @var string
     */
    private const FORM = 'form';
    /**
     * @var string
     */
    private const RESOURCE_PATH = '/cover';

    /**
     * @param int             $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null,
        $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex(int $hostIndex): void
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex(): int
    {
        return $this->hostIndex;
    }

    public function getConfig(): Configuration
    {
        return $this->config;
    }

    /**
     * Operation deleteCover
     *
     * Delete Cover
     *
     * @param  string $cover_id cover_id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return Response|ErrorResponse|ErrorResponse
     */
    public function deleteCover(string $cover_id): ModelInterface
    {
        list($response) = $this->deleteCoverWithHttpInfo($cover_id);
        return $response;
    }

    /**
     * Operation deleteCoverWithHttpInfo
     *
     * Delete Cover
     *
     * @param  string $cover_id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return array of \Mapsred\MangadexSDK\Model\Response|\Mapsred\MangadexSDK\Model\ErrorResponse|\Mapsred\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function deleteCoverWithHttpInfo(string $cover_id)
    {
        $request = $this->deleteCoverRequest($cover_id);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() !== null ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() !== null ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\Mapsred\MangadexSDK\Model\Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                case 403:
                    if ('\Mapsred\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Mapsred\MangadexSDK\Model\Response';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Mapsred\MangadexSDK\Model\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Mapsred\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation deleteCoverAsync
     *
     * Delete Cover
     *
     * @param  string $cover_id (required)
     *
     * @throws InvalidArgumentException
     */
    public function deleteCoverAsync(string $cover_id): PromiseInterface
    {
        return $this->deleteCoverAsyncWithHttpInfo($cover_id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation deleteCoverAsyncWithHttpInfo
     *
     * Delete Cover
     *
     * @param  string $cover_id (required)
     *
     * @throws InvalidArgumentException
     */
    public function deleteCoverAsyncWithHttpInfo(string $cover_id): PromiseInterface
    {
        $returnType = '\Mapsred\MangadexSDK\Model\Response';
        $request = $this->deleteCoverRequest($cover_id);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType): array {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception): void {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            )
        ;
    }

    /**
     * Create request for operation 'deleteCover'
     *
     * @param  string $cover_id (required)
     *
     * @throws InvalidArgumentException
     */
    public function deleteCoverRequest(string $cover_id): Request
    {
        // verify the required parameter 'cover_id' is set
        if ($cover_id === null || (is_array($cover_id) && count($cover_id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $cover_id when calling deleteCover'
            );
        }

        $resourcePath = '/cover/{coverId}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($cover_id !== null) {
            $resourcePath = str_replace(
                '{' . self::COVER_ID . '}',
                ObjectSerializer::toPathValue($cover_id),
                $resourcePath
            );
        }


        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                [self::APPLICATION_JSON]
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                [self::APPLICATION_JSON],
                []
            );
        }

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            self::NAME => $formParamName,
                            self::CONTENTS => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers[self::CONTENT_TYPE] === self::APPLICATION_JSON) {
                $httpBody = json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = Query::build($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers[self::AUTHORIZATION] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent() !== '') {
            $defaultHeaders[self::USER_AGENT] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = Query::build($queryParams);
        return new Request(
            'DELETE',
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation editCover
     *
     * Edit Cover
     *
     * @param  string $cover_id cover_id (required)
     * @param CoverEdit $cover_edit The size of the body is limited to 2KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return CoverResponse|ErrorResponse|ErrorResponse
     */
    public function editCover(string $cover_id, CoverEdit $cover_edit = null): ModelInterface
    {
        list($response) = $this->editCoverWithHttpInfo($cover_id, $cover_edit);
        return $response;
    }

    /**
     * Operation editCoverWithHttpInfo
     *
     * Edit Cover
     *
     * @param  string $cover_id (required)
     * @param CoverEdit $cover_edit The size of the body is limited to 2KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return array of \Mapsred\MangadexSDK\Model\CoverResponse|\Mapsred\MangadexSDK\Model\ErrorResponse|\Mapsred\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function editCoverWithHttpInfo(string $cover_id, CoverEdit $cover_edit = null)
    {
        $request = $this->editCoverRequest($cover_id, $cover_edit);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() !== null ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() !== null ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\Mapsred\MangadexSDK\Model\CoverResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\CoverResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                case 403:
                    if ('\Mapsred\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Mapsred\MangadexSDK\Model\CoverResponse';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Mapsred\MangadexSDK\Model\CoverResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Mapsred\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation editCoverAsync
     *
     * Edit Cover
     *
     * @param  string $cover_id (required)
     * @param CoverEdit $cover_edit The size of the body is limited to 2KB. (optional)
     *
     * @throws InvalidArgumentException
     */
    public function editCoverAsync(string $cover_id, CoverEdit $cover_edit = null): PromiseInterface
    {
        return $this->editCoverAsyncWithHttpInfo($cover_id, $cover_edit)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation editCoverAsyncWithHttpInfo
     *
     * Edit Cover
     *
     * @param  string $cover_id (required)
     * @param CoverEdit $cover_edit The size of the body is limited to 2KB. (optional)
     *
     * @throws InvalidArgumentException
     */
    public function editCoverAsyncWithHttpInfo(string $cover_id, CoverEdit $cover_edit = null): PromiseInterface
    {
        $returnType = '\Mapsred\MangadexSDK\Model\CoverResponse';
        $request = $this->editCoverRequest($cover_id, $cover_edit);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType): array {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception): void {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            )
        ;
    }

    /**
     * Create request for operation 'editCover'
     *
     * @param  string $cover_id (required)
     * @param CoverEdit $cover_edit The size of the body is limited to 2KB. (optional)
     *
     * @throws InvalidArgumentException
     */
    public function editCoverRequest(string $cover_id, CoverEdit $cover_edit = null): Request
    {
        // verify the required parameter 'cover_id' is set
        if ($cover_id === null || (is_array($cover_id) && count($cover_id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $cover_id when calling editCover'
            );
        }

        $resourcePath = '/cover/{coverId}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($cover_id !== null) {
            $resourcePath = str_replace(
                '{' . self::COVER_ID . '}',
                ObjectSerializer::toPathValue($cover_id),
                $resourcePath
            );
        }


        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                [self::APPLICATION_JSON]
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                [self::APPLICATION_JSON],
                [self::APPLICATION_JSON]
            );
        }

        // for model (json/xml)
        if (isset($cover_edit)) {
            if ($headers[self::CONTENT_TYPE] === self::APPLICATION_JSON) {
                $httpBody = json_encode(ObjectSerializer::sanitizeForSerialization($cover_edit));
            } else {
                $httpBody = $cover_edit;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            self::NAME => $formParamName,
                            self::CONTENTS => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers[self::CONTENT_TYPE] === self::APPLICATION_JSON) {
                $httpBody = json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = Query::build($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers[self::AUTHORIZATION] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent() !== '') {
            $defaultHeaders[self::USER_AGENT] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = Query::build($queryParams);
        return new Request(
            'PUT',
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getCover
     *
     * CoverArt list
     *
     * @param  int $limit limit (optional, default to 10)
     * @param  int $offset offset (optional)
     * @param  string[] $manga Manga ids (limited to 100 per request) (optional)
     * @param  string[] $ids Covers ids (limited to 100 per request) (optional)
     * @param  string[] $uploaders User ids (limited to 100 per request) (optional)
     * @param  Order4 $order order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return CoverList|ErrorResponse|ErrorResponse
     */
    public function getCover(int $limit = 10, int $offset = null, array $manga = null, array $ids = null, array $uploaders = null, $order = null): ModelInterface
    {
        list($response) = $this->getCoverWithHttpInfo($limit, $offset, $manga, $ids, $uploaders, $order);
        return $response;
    }

    /**
     * Operation getCoverWithHttpInfo
     *
     * CoverArt list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $manga Manga ids (limited to 100 per request) (optional)
     * @param  string[] $ids Covers ids (limited to 100 per request) (optional)
     * @param  string[] $uploaders User ids (limited to 100 per request) (optional)
     * @param  Order4 $order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return array of \Mapsred\MangadexSDK\Model\CoverList|\Mapsred\MangadexSDK\Model\ErrorResponse|\Mapsred\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getCoverWithHttpInfo(int $limit = 10, int $offset = null, array $manga = null, array $ids = null, array $uploaders = null, $order = null)
    {
        $request = $this->getCoverRequest($limit, $offset, $manga, $ids, $uploaders, $order);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() !== null ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() !== null ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\Mapsred\MangadexSDK\Model\CoverList' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\CoverList', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                case 403:
                    if ('\Mapsred\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Mapsred\MangadexSDK\Model\CoverList';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Mapsred\MangadexSDK\Model\CoverList',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Mapsred\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation getCoverAsync
     *
     * CoverArt list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $manga Manga ids (limited to 100 per request) (optional)
     * @param  string[] $ids Covers ids (limited to 100 per request) (optional)
     * @param  string[] $uploaders User ids (limited to 100 per request) (optional)
     * @param  Order4 $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getCoverAsync(int $limit = 10, int $offset = null, array $manga = null, array $ids = null, array $uploaders = null, $order = null): PromiseInterface
    {
        return $this->getCoverAsyncWithHttpInfo($limit, $offset, $manga, $ids, $uploaders, $order)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getCoverAsyncWithHttpInfo
     *
     * CoverArt list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $manga Manga ids (limited to 100 per request) (optional)
     * @param  string[] $ids Covers ids (limited to 100 per request) (optional)
     * @param  string[] $uploaders User ids (limited to 100 per request) (optional)
     * @param  Order4 $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getCoverAsyncWithHttpInfo(int $limit = 10, int $offset = null, array $manga = null, array $ids = null, array $uploaders = null, $order = null): PromiseInterface
    {
        $returnType = '\Mapsred\MangadexSDK\Model\CoverList';
        $request = $this->getCoverRequest($limit, $offset, $manga, $ids, $uploaders, $order);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType): array {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception): void {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            )
        ;
    }

    /**
     * Create request for operation 'getCover'
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $manga Manga ids (limited to 100 per request) (optional)
     * @param  string[] $ids Covers ids (limited to 100 per request) (optional)
     * @param  string[] $uploaders User ids (limited to 100 per request) (optional)
     * @param  Order4 $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getCoverRequest(int $limit = 10, int $offset = null, array $manga = null, array $ids = null, array $uploaders = null, $order = null): Request
    {
        if ($limit !== null && $limit > 100) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling CoverApi.getCover, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling CoverApi.getCover, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new InvalidArgumentException('invalid value for "$offset" when calling CoverApi.getCover, must be bigger than or equal to 0.');
        }
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        if ($limit !== null) {
            if(self::FORM === self::FORM && is_array($limit)) {
                foreach($limit as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['limit'] = $limit;
            }
        }
        // query params
        if ($offset !== null) {
            if(self::FORM === self::FORM && is_array($offset)) {
                foreach($offset as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['offset'] = $offset;
            }
        }
        // query params
        if ($manga !== null) {
            if(self::FORM === self::FORM && is_array($manga)) {
                foreach($manga as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['manga'] = $manga;
            }
        }
        // query params
        if ($ids !== null) {
            if(self::FORM === self::FORM && is_array($ids)) {
                foreach($ids as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['ids'] = $ids;
            }
        }
        // query params
        if ($uploaders !== null) {
            if(self::FORM === self::FORM && is_array($uploaders)) {
                foreach($uploaders as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['uploaders'] = $uploaders;
            }
        }
        // query params
        if ($order !== null) {
            if(self::FORM === self::FORM && is_array($order)) {
                foreach($order as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['order'] = $order;
            }
        }




        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                [self::APPLICATION_JSON]
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                [self::APPLICATION_JSON],
                []
            );
        }

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            self::NAME => $formParamName,
                            self::CONTENTS => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers[self::CONTENT_TYPE] === self::APPLICATION_JSON) {
                $httpBody = json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = Query::build($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent() !== '') {
            $defaultHeaders[self::USER_AGENT] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = Query::build($queryParams);
        return new Request(
            'GET',
            $this->config->getHost() . self::RESOURCE_PATH . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getCoverId
     *
     * Get Cover
     *
     * @param  string $cover_id cover_id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return CoverResponse|ErrorResponse|ErrorResponse
     */
    public function getCoverId(string $cover_id): ModelInterface
    {
        list($response) = $this->getCoverIdWithHttpInfo($cover_id);
        return $response;
    }

    /**
     * Operation getCoverIdWithHttpInfo
     *
     * Get Cover
     *
     * @param  string $cover_id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return array of \Mapsred\MangadexSDK\Model\CoverResponse|\Mapsred\MangadexSDK\Model\ErrorResponse|\Mapsred\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getCoverIdWithHttpInfo(string $cover_id)
    {
        $request = $this->getCoverIdRequest($cover_id);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() !== null ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() !== null ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\Mapsred\MangadexSDK\Model\CoverResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\CoverResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                case 403:
                    if ('\Mapsred\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Mapsred\MangadexSDK\Model\CoverResponse';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Mapsred\MangadexSDK\Model\CoverResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Mapsred\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation getCoverIdAsync
     *
     * Get Cover
     *
     * @param  string $cover_id (required)
     *
     * @throws InvalidArgumentException
     */
    public function getCoverIdAsync(string $cover_id): PromiseInterface
    {
        return $this->getCoverIdAsyncWithHttpInfo($cover_id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getCoverIdAsyncWithHttpInfo
     *
     * Get Cover
     *
     * @param  string $cover_id (required)
     *
     * @throws InvalidArgumentException
     */
    public function getCoverIdAsyncWithHttpInfo(string $cover_id): PromiseInterface
    {
        $returnType = '\Mapsred\MangadexSDK\Model\CoverResponse';
        $request = $this->getCoverIdRequest($cover_id);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType): array {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception): void {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            )
        ;
    }

    /**
     * Create request for operation 'getCoverId'
     *
     * @param  string $cover_id (required)
     *
     * @throws InvalidArgumentException
     */
    public function getCoverIdRequest(string $cover_id): Request
    {
        // verify the required parameter 'cover_id' is set
        if ($cover_id === null || (is_array($cover_id) && count($cover_id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $cover_id when calling getCoverId'
            );
        }

        $resourcePath = '/cover/{coverId}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($cover_id !== null) {
            $resourcePath = str_replace(
                '{' . self::COVER_ID . '}',
                ObjectSerializer::toPathValue($cover_id),
                $resourcePath
            );
        }


        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                [self::APPLICATION_JSON]
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                [self::APPLICATION_JSON],
                []
            );
        }

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            self::NAME => $formParamName,
                            self::CONTENTS => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers[self::CONTENT_TYPE] === self::APPLICATION_JSON) {
                $httpBody = json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = Query::build($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent() !== '') {
            $defaultHeaders[self::USER_AGENT] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = Query::build($queryParams);
        return new Request(
            'GET',
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation uploadCover
     *
     * Upload Cover
     *
     * @param  string $manga_id manga_id (required)
     * @param SplFileObject $file file (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return CoverResponse|ErrorResponse|ErrorResponse
     */
    public function uploadCover(string $manga_id, SplFileObject $file = null): ModelInterface
    {
        list($response) = $this->uploadCoverWithHttpInfo($manga_id, $file);
        return $response;
    }

    /**
     * Operation uploadCoverWithHttpInfo
     *
     * Upload Cover
     *
     * @param  string $manga_id (required)
     * @param SplFileObject $file (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return array of \Mapsred\MangadexSDK\Model\CoverResponse|\Mapsred\MangadexSDK\Model\ErrorResponse|\Mapsred\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function uploadCoverWithHttpInfo(string $manga_id, SplFileObject $file = null)
    {
        $request = $this->uploadCoverRequest($manga_id, $file);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() !== null ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() !== null ? (string) $e->getResponse()->getBody() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\Mapsred\MangadexSDK\Model\CoverResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\CoverResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                case 403:
                    if ('\Mapsred\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Mapsred\MangadexSDK\Model\CoverResponse';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Mapsred\MangadexSDK\Model\CoverResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Mapsred\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation uploadCoverAsync
     *
     * Upload Cover
     *
     * @param  string $manga_id (required)
     * @param SplFileObject $file (optional)
     *
     * @throws InvalidArgumentException
     */
    public function uploadCoverAsync(string $manga_id, SplFileObject $file = null): PromiseInterface
    {
        return $this->uploadCoverAsyncWithHttpInfo($manga_id, $file)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation uploadCoverAsyncWithHttpInfo
     *
     * Upload Cover
     *
     * @param  string $manga_id (required)
     * @param SplFileObject $file (optional)
     *
     * @throws InvalidArgumentException
     */
    public function uploadCoverAsyncWithHttpInfo(string $manga_id, SplFileObject $file = null): PromiseInterface
    {
        $returnType = '\Mapsred\MangadexSDK\Model\CoverResponse';
        $request = $this->uploadCoverRequest($manga_id, $file);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType): array {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception): void {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            )
        ;
    }

    /**
     * Create request for operation 'uploadCover'
     *
     * @param  string $manga_id (required)
     * @param SplFileObject $file (optional)
     *
     * @throws InvalidArgumentException
     */
    public function uploadCoverRequest(string $manga_id, SplFileObject $file = null): Request
    {
        // verify the required parameter 'manga_id' is set
        if ($manga_id === null || (is_array($manga_id) && count($manga_id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $manga_id when calling uploadCover'
            );
        }

        $resourcePath = '/cover/{mangaId}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($manga_id !== null) {
            $resourcePath = str_replace(
                '{mangaId}',
                ObjectSerializer::toPathValue($manga_id),
                $resourcePath
            );
        }

        // form params
        if ($file !== null) {
            $multipart = true;
            $formParams['file'] = [];
            $paramFiles = is_array($file) ? $file : [$file];
            foreach ($paramFiles as $paramFile) {
                $formParams['file'][] = Query::tryFopen(
                    ObjectSerializer::toFormValue($paramFile),
                    'rb'
                );
            }
        }

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                [self::APPLICATION_JSON]
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                [self::APPLICATION_JSON],
                ['multipart/form-data']
            );
        }

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            self::NAME => $formParamName,
                            self::CONTENTS => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers[self::CONTENT_TYPE] === self::APPLICATION_JSON) {
                $httpBody = json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = Query::build($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers[self::AUTHORIZATION] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent() !== '') {
            $defaultHeaders[self::USER_AGENT] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = Query::build($queryParams);
        return new Request(
            'POST',
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption(): array
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
