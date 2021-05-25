<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use InvalidArgumentException;
use Mapsred\MangadexSDK\ApiException;
use Mapsred\MangadexSDK\Configuration;
use Mapsred\MangadexSDK\HeaderSelector;
use Mapsred\MangadexSDK\Model\AuthorList;
use Mapsred\MangadexSDK\Model\ChapterList;
use Mapsred\MangadexSDK\Model\CoverList;
use Mapsred\MangadexSDK\Model\ErrorResponse;
use Mapsred\MangadexSDK\Model\MangaList;
use Mapsred\MangadexSDK\Model\ModelInterface;
use Mapsred\MangadexSDK\Model\ScanlationGroupList;
use Mapsred\MangadexSDK\ObjectSerializer;
use RuntimeException;

final class SearchApi
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
    private const FORM = 'form';
    /**
     * @var string
     */
    private const LIMIT = 'limit';
    /**
     * @var string
     */
    private const OFFSET = 'offset';
    /**
     * @var string
     */
    private const IDS = 'ids';
    /**
     * @var string
     */
    private const NAME = 'name';
    /**
     * @var string
     */
    private const ORDER = 'order';
    /**
     * @var string
     */
    private const APPLICATION_JSON = 'application/json';
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
    private const USER_AGENT = 'User-Agent';
    /**
     * @var string
     */
    private const GET = 'GET';

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
     * Operation getAuthor
     *
     * Author list
     *
     * @param  int $limit limit (optional, default to 10)
     * @param  int $offset offset (optional)
     * @param  string[] $ids Author ids (limited to 100 per request) (optional)
     * @param  string $name name (optional)
     * @param  Order5 $order order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return AuthorList|ErrorResponse|ErrorResponse
     */
    public function getAuthor(int $limit = 10, int $offset = null, array $ids = null, string $name = null, $order = null): ModelInterface
    {
        list($response) = $this->getAuthorWithHttpInfo($limit, $offset, $ids, $name, $order);
        return $response;
    }

    /**
     * Operation getAuthorWithHttpInfo
     *
     * Author list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids Author ids (limited to 100 per request) (optional)
     * @param  string $name (optional)
     * @param  Order5 $order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return array of \Mapsred\MangadexSDK\Model\AuthorList|\Mapsred\MangadexSDK\Model\ErrorResponse|\Mapsred\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getAuthorWithHttpInfo(int $limit = 10, int $offset = null, array $ids = null, string $name = null, $order = null)
    {
        $request = $this->getAuthorRequest($limit, $offset, $ids, $name, $order);

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
                    if ('\Mapsred\MangadexSDK\Model\AuthorList' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\AuthorList', []),
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

            $returnType = '\Mapsred\MangadexSDK\Model\AuthorList';
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
                        '\Mapsred\MangadexSDK\Model\AuthorList',
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
     * Operation getAuthorAsync
     *
     * Author list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids Author ids (limited to 100 per request) (optional)
     * @param  string $name (optional)
     * @param  Order5 $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getAuthorAsync(int $limit = 10, int $offset = null, array $ids = null, string $name = null, $order = null): PromiseInterface
    {
        return $this->getAuthorAsyncWithHttpInfo($limit, $offset, $ids, $name, $order)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getAuthorAsyncWithHttpInfo
     *
     * Author list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids Author ids (limited to 100 per request) (optional)
     * @param  string $name (optional)
     * @param  Order5 $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getAuthorAsyncWithHttpInfo(int $limit = 10, int $offset = null, array $ids = null, string $name = null, $order = null): PromiseInterface
    {
        $returnType = '\Mapsred\MangadexSDK\Model\AuthorList';
        $request = $this->getAuthorRequest($limit, $offset, $ids, $name, $order);

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
     * Create request for operation 'getAuthor'
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids Author ids (limited to 100 per request) (optional)
     * @param  string $name (optional)
     * @param  Order5 $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getAuthorRequest(int $limit = 10, int $offset = null, array $ids = null, string $name = null, $order = null): Request
    {
        if ($limit !== null && $limit > 100) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling SearchApi.getAuthor, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling SearchApi.getAuthor, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new InvalidArgumentException('invalid value for "$offset" when calling SearchApi.getAuthor, must be bigger than or equal to 0.');
        }


        $resourcePath = '/author';
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
                $queryParams[self::LIMIT] = $limit;
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
                $queryParams[self::OFFSET] = $offset;
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
                $queryParams[self::IDS] = $ids;
            }
        }
        // query params
        if ($name !== null) {
            if(self::FORM === self::FORM && is_array($name)) {
                foreach($name as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams[self::NAME] = $name;
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
                $queryParams[self::ORDER] = $order;
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
                $httpBody = Utils::jsonEncode($formParams);

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
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getChapter
     *
     * Chapter list
     *
     * @param  int $limit limit (optional, default to 10)
     * @param  int $offset offset (optional)
     * @param  string[] $ids Chapter ids (limited to 100 per request) (optional)
     * @param  string $title title (optional)
     * @param  string[] $groups groups (optional)
     * @param  string $uploader uploader (optional)
     * @param  string $manga manga (optional)
     * @param  string $volume volume (optional)
     * @param  string $chapter chapter (optional)
     * @param  string[] $translated_language translated_language (optional)
     * @param  string $created_at_since created_at_since (optional)
     * @param  string $updated_at_since updated_at_since (optional)
     * @param  string $publish_at_since publish_at_since (optional)
     * @param  Order1 $order order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return ChapterList|ErrorResponse|ErrorResponse
     */
    public function getChapter(int $limit = 10, int $offset = null, array $ids = null, string $title = null, array $groups = null, string $uploader = null, string $manga = null, string $volume = null, string $chapter = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): ModelInterface
    {
        list($response) = $this->getChapterWithHttpInfo($limit, $offset, $ids, $title, $groups, $uploader, $manga, $volume, $chapter, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order);
        return $response;
    }

    /**
     * Operation getChapterWithHttpInfo
     *
     * Chapter list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids Chapter ids (limited to 100 per request) (optional)
     * @param  string $title (optional)
     * @param  string[] $groups (optional)
     * @param  string $uploader (optional)
     * @param  string $manga (optional)
     * @param  string $volume (optional)
     * @param  string $chapter (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order1 $order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return array of \Mapsred\MangadexSDK\Model\ChapterList|\Mapsred\MangadexSDK\Model\ErrorResponse|\Mapsred\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getChapterWithHttpInfo(int $limit = 10, int $offset = null, array $ids = null, string $title = null, array $groups = null, string $uploader = null, string $manga = null, string $volume = null, string $chapter = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null)
    {
        $request = $this->getChapterRequest($limit, $offset, $ids, $title, $groups, $uploader, $manga, $volume, $chapter, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order);

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
                    if ('\Mapsred\MangadexSDK\Model\ChapterList' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\ChapterList', []),
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

            $returnType = '\Mapsred\MangadexSDK\Model\ChapterList';
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
                        '\Mapsred\MangadexSDK\Model\ChapterList',
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
     * Operation getChapterAsync
     *
     * Chapter list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids Chapter ids (limited to 100 per request) (optional)
     * @param  string $title (optional)
     * @param  string[] $groups (optional)
     * @param  string $uploader (optional)
     * @param  string $manga (optional)
     * @param  string $volume (optional)
     * @param  string $chapter (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order1 $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getChapterAsync(int $limit = 10, int $offset = null, array $ids = null, string $title = null, array $groups = null, string $uploader = null, string $manga = null, string $volume = null, string $chapter = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): PromiseInterface
    {
        return $this->getChapterAsyncWithHttpInfo($limit, $offset, $ids, $title, $groups, $uploader, $manga, $volume, $chapter, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getChapterAsyncWithHttpInfo
     *
     * Chapter list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids Chapter ids (limited to 100 per request) (optional)
     * @param  string $title (optional)
     * @param  string[] $groups (optional)
     * @param  string $uploader (optional)
     * @param  string $manga (optional)
     * @param  string $volume (optional)
     * @param  string $chapter (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order1 $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getChapterAsyncWithHttpInfo(int $limit = 10, int $offset = null, array $ids = null, string $title = null, array $groups = null, string $uploader = null, string $manga = null, string $volume = null, string $chapter = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): PromiseInterface
    {
        $returnType = '\Mapsred\MangadexSDK\Model\ChapterList';
        $request = $this->getChapterRequest($limit, $offset, $ids, $title, $groups, $uploader, $manga, $volume, $chapter, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order);

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
     * Create request for operation 'getChapter'
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids Chapter ids (limited to 100 per request) (optional)
     * @param  string $title (optional)
     * @param  string[] $groups (optional)
     * @param  string $uploader (optional)
     * @param  string $manga (optional)
     * @param  string $volume (optional)
     * @param  string $chapter (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order1 $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getChapterRequest(int $limit = 10, int $offset = null, array $ids = null, string $title = null, array $groups = null, string $uploader = null, string $manga = null, string $volume = null, string $chapter = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): Request
    {
        if ($limit !== null && $limit > 100) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling SearchApi.getChapter, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling SearchApi.getChapter, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new InvalidArgumentException('invalid value for "$offset" when calling SearchApi.getChapter, must be bigger than or equal to 0.');
        }

        if ($created_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $created_at_since)) {
            throw new InvalidArgumentException("invalid value for \"created_at_since\" when calling SearchApi.getChapter, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }

        if ($updated_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $updated_at_since)) {
            throw new InvalidArgumentException("invalid value for \"updated_at_since\" when calling SearchApi.getChapter, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }

        if ($publish_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $publish_at_since)) {
            throw new InvalidArgumentException("invalid value for \"publish_at_since\" when calling SearchApi.getChapter, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }


        $resourcePath = '/chapter';
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
                $queryParams[self::LIMIT] = $limit;
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
                $queryParams[self::OFFSET] = $offset;
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
                $queryParams[self::IDS] = $ids;
            }
        }
        // query params
        if ($title !== null) {
            if(self::FORM === self::FORM && is_array($title)) {
                foreach($title as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['title'] = $title;
            }
        }
        // query params
        if ($groups !== null) {
            if(self::FORM === self::FORM && is_array($groups)) {
                foreach($groups as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['groups'] = $groups;
            }
        }
        // query params
        if ($uploader !== null) {
            if(self::FORM === self::FORM && is_array($uploader)) {
                foreach($uploader as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['uploader'] = $uploader;
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
        if ($volume !== null) {
            if(self::FORM === self::FORM && is_array($volume)) {
                foreach($volume as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['volume'] = $volume;
            }
        }
        // query params
        if ($chapter !== null) {
            if(self::FORM === self::FORM && is_array($chapter)) {
                foreach($chapter as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['chapter'] = $chapter;
            }
        }
        // query params
        if ($translated_language !== null) {
            if(self::FORM === self::FORM && is_array($translated_language)) {
                foreach($translated_language as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['translatedLanguage'] = $translated_language;
            }
        }
        // query params
        if ($created_at_since !== null) {
            if(self::FORM === self::FORM && is_array($created_at_since)) {
                foreach($created_at_since as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['createdAtSince'] = $created_at_since;
            }
        }
        // query params
        if ($updated_at_since !== null) {
            if(self::FORM === self::FORM && is_array($updated_at_since)) {
                foreach($updated_at_since as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['updatedAtSince'] = $updated_at_since;
            }
        }
        // query params
        if ($publish_at_since !== null) {
            if(self::FORM === self::FORM && is_array($publish_at_since)) {
                foreach($publish_at_since as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['publishAtSince'] = $publish_at_since;
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
                $queryParams[self::ORDER] = $order;
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
                $httpBody = Utils::jsonEncode($formParams);

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
            self::GET,
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
            throw new InvalidArgumentException('invalid value for "$limit" when calling SearchApi.getCover, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling SearchApi.getCover, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new InvalidArgumentException('invalid value for "$offset" when calling SearchApi.getCover, must be bigger than or equal to 0.');
        }


        $resourcePath = '/cover';
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
                $queryParams[self::LIMIT] = $limit;
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
                $queryParams[self::OFFSET] = $offset;
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
                $queryParams[self::IDS] = $ids;
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
                $queryParams[self::ORDER] = $order;
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
                $httpBody = Utils::jsonEncode($formParams);

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
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getSearchGroup
     *
     * Scanlation Group list
     *
     * @param  int $limit limit (optional, default to 10)
     * @param  int $offset offset (optional)
     * @param  string[] $ids ScanlationGroup ids (limited to 100 per request) (optional)
     * @param  string $name name (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return ScanlationGroupList|ErrorResponse|ErrorResponse
     */
    public function getSearchGroup(int $limit = 10, int $offset = null, array $ids = null, string $name = null): ModelInterface
    {
        list($response) = $this->getSearchGroupWithHttpInfo($limit, $offset, $ids, $name);
        return $response;
    }

    /**
     * Operation getSearchGroupWithHttpInfo
     *
     * Scanlation Group list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids ScanlationGroup ids (limited to 100 per request) (optional)
     * @param  string $name (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return array of \Mapsred\MangadexSDK\Model\ScanlationGroupList|\Mapsred\MangadexSDK\Model\ErrorResponse|\Mapsred\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getSearchGroupWithHttpInfo(int $limit = 10, int $offset = null, array $ids = null, string $name = null)
    {
        $request = $this->getSearchGroupRequest($limit, $offset, $ids, $name);

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
                    if ('\Mapsred\MangadexSDK\Model\ScanlationGroupList' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\ScanlationGroupList', []),
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

            $returnType = '\Mapsred\MangadexSDK\Model\ScanlationGroupList';
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
                        '\Mapsred\MangadexSDK\Model\ScanlationGroupList',
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
     * Operation getSearchGroupAsync
     *
     * Scanlation Group list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids ScanlationGroup ids (limited to 100 per request) (optional)
     * @param  string $name (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getSearchGroupAsync(int $limit = 10, int $offset = null, array $ids = null, string $name = null): PromiseInterface
    {
        return $this->getSearchGroupAsyncWithHttpInfo($limit, $offset, $ids, $name)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getSearchGroupAsyncWithHttpInfo
     *
     * Scanlation Group list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids ScanlationGroup ids (limited to 100 per request) (optional)
     * @param  string $name (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getSearchGroupAsyncWithHttpInfo(int $limit = 10, int $offset = null, array $ids = null, string $name = null): PromiseInterface
    {
        $returnType = '\Mapsred\MangadexSDK\Model\ScanlationGroupList';
        $request = $this->getSearchGroupRequest($limit, $offset, $ids, $name);

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
     * Create request for operation 'getSearchGroup'
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids ScanlationGroup ids (limited to 100 per request) (optional)
     * @param  string $name (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getSearchGroupRequest(int $limit = 10, int $offset = null, array $ids = null, string $name = null): Request
    {
        if ($limit !== null && $limit > 100) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling SearchApi.getSearchGroup, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling SearchApi.getSearchGroup, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new InvalidArgumentException('invalid value for "$offset" when calling SearchApi.getSearchGroup, must be bigger than or equal to 0.');
        }


        $resourcePath = '/group';
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
                $queryParams[self::LIMIT] = $limit;
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
                $queryParams[self::OFFSET] = $offset;
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
                $queryParams[self::IDS] = $ids;
            }
        }
        // query params
        if ($name !== null) {
            if(self::FORM === self::FORM && is_array($name)) {
                foreach($name as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams[self::NAME] = $name;
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
                $httpBody = Utils::jsonEncode($formParams);

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
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getSearchManga
     *
     * Manga list
     *
     * @param  int $limit limit (optional, default to 10)
     * @param  int $offset offset (optional)
     * @param  string $title title (optional)
     * @param  string[] $authors authors (optional)
     * @param  string[] $artists artists (optional)
     * @param  int $year Year of release (optional)
     * @param  string[] $included_tags included_tags (optional)
     * @param  string $included_tags_mode included_tags_mode (optional, default to 'AND')
     * @param  string[] $excluded_tags excluded_tags (optional)
     * @param  string $excluded_tags_mode excluded_tags_mode (optional, default to 'OR')
     * @param  string[] $status status (optional)
     * @param  string[] $original_language original_language (optional)
     * @param  string[] $publication_demographic publication_demographic (optional)
     * @param  string[] $ids Manga ids (limited to 100 per request) (optional)
     * @param  string[] $content_rating content_rating (optional)
     * @param  string $created_at_since created_at_since (optional)
     * @param  string $updated_at_since updated_at_since (optional)
     * @param  Order $order order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return MangaList|ErrorResponse
     */
    public function getSearchManga(int $limit = 10, int $offset = null, string $title = null, array $authors = null, array $artists = null, int $year = null, array $included_tags = null, string $included_tags_mode = 'AND', array $excluded_tags = null, string $excluded_tags_mode = 'OR', array $status = null, array $original_language = null, array $publication_demographic = null, array $ids = null, array $content_rating = null, string $created_at_since = null, string $updated_at_since = null, $order = null): ModelInterface
    {
        list($response) = $this->getSearchMangaWithHttpInfo($limit, $offset, $title, $authors, $artists, $year, $included_tags, $included_tags_mode, $excluded_tags, $excluded_tags_mode, $status, $original_language, $publication_demographic, $ids, $content_rating, $created_at_since, $updated_at_since, $order);
        return $response;
    }

    /**
     * Operation getSearchMangaWithHttpInfo
     *
     * Manga list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string $title (optional)
     * @param  string[] $authors (optional)
     * @param  string[] $artists (optional)
     * @param  int $year Year of release (optional)
     * @param  string[] $included_tags (optional)
     * @param  string $included_tags_mode (optional, default to 'AND')
     * @param  string[] $excluded_tags (optional)
     * @param  string $excluded_tags_mode (optional, default to 'OR')
     * @param  string[] $status (optional)
     * @param  string[] $original_language (optional)
     * @param  string[] $publication_demographic (optional)
     * @param  string[] $ids Manga ids (limited to 100 per request) (optional)
     * @param  string[] $content_rating (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  Order $order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return array of \Mapsred\MangadexSDK\Model\MangaList|\Mapsred\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getSearchMangaWithHttpInfo(int $limit = 10, int $offset = null, string $title = null, array $authors = null, array $artists = null, int $year = null, array $included_tags = null, string $included_tags_mode = 'AND', array $excluded_tags = null, string $excluded_tags_mode = 'OR', array $status = null, array $original_language = null, array $publication_demographic = null, array $ids = null, array $content_rating = null, string $created_at_since = null, string $updated_at_since = null, $order = null)
    {
        $request = $this->getSearchMangaRequest($limit, $offset, $title, $authors, $artists, $year, $included_tags, $included_tags_mode, $excluded_tags, $excluded_tags_mode, $status, $original_language, $publication_demographic, $ids, $content_rating, $created_at_since, $updated_at_since, $order);

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
                    if ('\Mapsred\MangadexSDK\Model\MangaList' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\MangaList', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
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

            $returnType = '\Mapsred\MangadexSDK\Model\MangaList';
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
                        '\Mapsred\MangadexSDK\Model\MangaList',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
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
     * Operation getSearchMangaAsync
     *
     * Manga list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string $title (optional)
     * @param  string[] $authors (optional)
     * @param  string[] $artists (optional)
     * @param  int $year Year of release (optional)
     * @param  string[] $included_tags (optional)
     * @param  string $included_tags_mode (optional, default to 'AND')
     * @param  string[] $excluded_tags (optional)
     * @param  string $excluded_tags_mode (optional, default to 'OR')
     * @param  string[] $status (optional)
     * @param  string[] $original_language (optional)
     * @param  string[] $publication_demographic (optional)
     * @param  string[] $ids Manga ids (limited to 100 per request) (optional)
     * @param  string[] $content_rating (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  Order $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getSearchMangaAsync(int $limit = 10, int $offset = null, string $title = null, array $authors = null, array $artists = null, int $year = null, array $included_tags = null, string $included_tags_mode = 'AND', array $excluded_tags = null, string $excluded_tags_mode = 'OR', array $status = null, array $original_language = null, array $publication_demographic = null, array $ids = null, array $content_rating = null, string $created_at_since = null, string $updated_at_since = null, $order = null): PromiseInterface
    {
        return $this->getSearchMangaAsyncWithHttpInfo($limit, $offset, $title, $authors, $artists, $year, $included_tags, $included_tags_mode, $excluded_tags, $excluded_tags_mode, $status, $original_language, $publication_demographic, $ids, $content_rating, $created_at_since, $updated_at_since, $order)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getSearchMangaAsyncWithHttpInfo
     *
     * Manga list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string $title (optional)
     * @param  string[] $authors (optional)
     * @param  string[] $artists (optional)
     * @param  int $year Year of release (optional)
     * @param  string[] $included_tags (optional)
     * @param  string $included_tags_mode (optional, default to 'AND')
     * @param  string[] $excluded_tags (optional)
     * @param  string $excluded_tags_mode (optional, default to 'OR')
     * @param  string[] $status (optional)
     * @param  string[] $original_language (optional)
     * @param  string[] $publication_demographic (optional)
     * @param  string[] $ids Manga ids (limited to 100 per request) (optional)
     * @param  string[] $content_rating (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  Order $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getSearchMangaAsyncWithHttpInfo(int $limit = 10, int $offset = null, string $title = null, array $authors = null, array $artists = null, int $year = null, array $included_tags = null, string $included_tags_mode = 'AND', array $excluded_tags = null, string $excluded_tags_mode = 'OR', array $status = null, array $original_language = null, array $publication_demographic = null, array $ids = null, array $content_rating = null, string $created_at_since = null, string $updated_at_since = null, $order = null): PromiseInterface
    {
        $returnType = '\Mapsred\MangadexSDK\Model\MangaList';
        $request = $this->getSearchMangaRequest($limit, $offset, $title, $authors, $artists, $year, $included_tags, $included_tags_mode, $excluded_tags, $excluded_tags_mode, $status, $original_language, $publication_demographic, $ids, $content_rating, $created_at_since, $updated_at_since, $order);

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
     * Create request for operation 'getSearchManga'
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string $title (optional)
     * @param  string[] $authors (optional)
     * @param  string[] $artists (optional)
     * @param  int $year Year of release (optional)
     * @param  string[] $included_tags (optional)
     * @param  string $included_tags_mode (optional, default to 'AND')
     * @param  string[] $excluded_tags (optional)
     * @param  string $excluded_tags_mode (optional, default to 'OR')
     * @param  string[] $status (optional)
     * @param  string[] $original_language (optional)
     * @param  string[] $publication_demographic (optional)
     * @param  string[] $ids Manga ids (limited to 100 per request) (optional)
     * @param  string[] $content_rating (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  Order $order (optional)
     *
     * @throws InvalidArgumentException
     */
    public function getSearchMangaRequest(int $limit = 10, int $offset = null, string $title = null, array $authors = null, array $artists = null, int $year = null, array $included_tags = null, string $included_tags_mode = 'AND', array $excluded_tags = null, string $excluded_tags_mode = 'OR', array $status = null, array $original_language = null, array $publication_demographic = null, array $ids = null, array $content_rating = null, string $created_at_since = null, string $updated_at_since = null, $order = null): Request
    {
        if ($limit !== null && $limit > 100) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling SearchApi.getSearchManga, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling SearchApi.getSearchManga, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new InvalidArgumentException('invalid value for "$offset" when calling SearchApi.getSearchManga, must be bigger than or equal to 0.');
        }

        if ($created_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $created_at_since)) {
            throw new InvalidArgumentException("invalid value for \"created_at_since\" when calling SearchApi.getSearchManga, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }

        if ($updated_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $updated_at_since)) {
            throw new InvalidArgumentException("invalid value for \"updated_at_since\" when calling SearchApi.getSearchManga, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }


        $resourcePath = '/manga';
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
                $queryParams[self::LIMIT] = $limit;
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
                $queryParams[self::OFFSET] = $offset;
            }
        }
        // query params
        if ($title !== null) {
            if(self::FORM === self::FORM && is_array($title)) {
                foreach($title as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['title'] = $title;
            }
        }
        // query params
        if ($authors !== null) {
            if(self::FORM === self::FORM && is_array($authors)) {
                foreach($authors as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['authors'] = $authors;
            }
        }
        // query params
        if ($artists !== null) {
            if(self::FORM === self::FORM && is_array($artists)) {
                foreach($artists as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['artists'] = $artists;
            }
        }
        // query params
        if ($year !== null) {
            if(self::FORM === self::FORM && is_array($year)) {
                foreach($year as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['year'] = $year;
            }
        }
        // query params
        if ($included_tags !== null) {
            if(self::FORM === self::FORM && is_array($included_tags)) {
                foreach($included_tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['includedTags'] = $included_tags;
            }
        }
        // query params
        if ($included_tags_mode !== null) {
            if(self::FORM === self::FORM && is_array($included_tags_mode)) {
                foreach($included_tags_mode as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['includedTagsMode'] = $included_tags_mode;
            }
        }
        // query params
        if ($excluded_tags !== null) {
            if(self::FORM === self::FORM && is_array($excluded_tags)) {
                foreach($excluded_tags as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['excludedTags'] = $excluded_tags;
            }
        }
        // query params
        if ($excluded_tags_mode !== null) {
            if(self::FORM === self::FORM && is_array($excluded_tags_mode)) {
                foreach($excluded_tags_mode as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['excludedTagsMode'] = $excluded_tags_mode;
            }
        }
        // query params
        if ($status !== null) {
            if(self::FORM === self::FORM && is_array($status)) {
                foreach($status as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['status'] = $status;
            }
        }
        // query params
        if ($original_language !== null) {
            if(self::FORM === self::FORM && is_array($original_language)) {
                foreach($original_language as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['originalLanguage'] = $original_language;
            }
        }
        // query params
        if ($publication_demographic !== null) {
            if(self::FORM === self::FORM && is_array($publication_demographic)) {
                foreach($publication_demographic as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['publicationDemographic'] = $publication_demographic;
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
                $queryParams[self::IDS] = $ids;
            }
        }
        // query params
        if ($content_rating !== null) {
            if(self::FORM === self::FORM && is_array($content_rating)) {
                foreach($content_rating as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['contentRating'] = $content_rating;
            }
        }
        // query params
        if ($created_at_since !== null) {
            if(self::FORM === self::FORM && is_array($created_at_since)) {
                foreach($created_at_since as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['createdAtSince'] = $created_at_since;
            }
        }
        // query params
        if ($updated_at_since !== null) {
            if(self::FORM === self::FORM && is_array($updated_at_since)) {
                foreach($updated_at_since as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['updatedAtSince'] = $updated_at_since;
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
                $queryParams[self::ORDER] = $order;
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
                $httpBody = Utils::jsonEncode($formParams);

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
            self::GET,
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
