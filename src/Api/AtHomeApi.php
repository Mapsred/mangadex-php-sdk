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
use Mapsred\MangadexSDK\Model\ErrorResponse;
use Mapsred\MangadexSDK\Model\InlineResponse2003;
use Mapsred\MangadexSDK\Model\ModelInterface;
use Mapsred\MangadexSDK\ObjectSerializer;
use RuntimeException;

final class AtHomeApi
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
    private const APPLICATION_JSON = 'application/json';
    /**
     * @var mixed[]
     */
    private const FORM_PARAMS = [];
    /**
     * @var mixed[]
     */
    private const HEADER_PARAMS = [];

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
     * Operation getAtHomeServerChapterId
     *
     * Get MangaDex@Home server URL
     *
     * @param  string $chapter_id Chapter ID (required)
     * @param  bool $force_port443 Force selecting from MangaDex@Home servers that use the standard HTTPS port 443.  While the conventional port for HTTPS traffic is 443 and servers are encouraged to use it, it is not a hard requirement as it technically isn&#39;t anything special.  However, some misbehaving school/office network will at time block traffic to non-standard ports, and setting this flag to &#x60;true&#x60; will ensure selection of a server that uses these. (optional, default to false)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return InlineResponse2003|ErrorResponse
     */
    public function getAtHomeServerChapterId(string $chapter_id, bool $force_port443 = false): ModelInterface
    {
        list($response) = $this->getAtHomeServerChapterIdWithHttpInfo($chapter_id, $force_port443);
        return $response;
    }

    /**
     * Operation getAtHomeServerChapterIdWithHttpInfo
     *
     * Get MangaDex@Home server URL
     *
     * @param  string $chapter_id Chapter ID (required)
     * @param  bool $force_port443 Force selecting from MangaDex@Home servers that use the standard HTTPS port 443.  While the conventional port for HTTPS traffic is 443 and servers are encouraged to use it, it is not a hard requirement as it technically isn&#39;t anything special.  However, some misbehaving school/office network will at time block traffic to non-standard ports, and setting this flag to &#x60;true&#x60; will ensure selection of a server that uses these. (optional, default to false)
     *
     * @throws ApiException on non-2xx response
     * @throws InvalidArgumentException
     * @return array of \Mapsred\MangadexSDK\Model\InlineResponse2003|\Mapsred\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getAtHomeServerChapterIdWithHttpInfo(string $chapter_id, bool $force_port443 = false)
    {
        $request = $this->getAtHomeServerChapterIdRequest($chapter_id, $force_port443);

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
                    if ('\Mapsred\MangadexSDK\Model\InlineResponse2003' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Mapsred\MangadexSDK\Model\InlineResponse2003', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 404:
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

            $returnType = '\Mapsred\MangadexSDK\Model\InlineResponse2003';
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
                        '\Mapsred\MangadexSDK\Model\InlineResponse2003',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
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
     * Operation getAtHomeServerChapterIdAsync
     *
     * Get MangaDex@Home server URL
     *
     * @param  string $chapter_id Chapter ID (required)
     * @param  bool $force_port443 Force selecting from MangaDex@Home servers that use the standard HTTPS port 443.  While the conventional port for HTTPS traffic is 443 and servers are encouraged to use it, it is not a hard requirement as it technically isn&#39;t anything special.  However, some misbehaving school/office network will at time block traffic to non-standard ports, and setting this flag to &#x60;true&#x60; will ensure selection of a server that uses these. (optional, default to false)
     *
     * @throws InvalidArgumentException
     */
    public function getAtHomeServerChapterIdAsync(string $chapter_id, bool $force_port443 = false): PromiseInterface
    {
        return $this->getAtHomeServerChapterIdAsyncWithHttpInfo($chapter_id, $force_port443)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getAtHomeServerChapterIdAsyncWithHttpInfo
     *
     * Get MangaDex@Home server URL
     *
     * @param  string $chapter_id Chapter ID (required)
     * @param  bool $force_port443 Force selecting from MangaDex@Home servers that use the standard HTTPS port 443.  While the conventional port for HTTPS traffic is 443 and servers are encouraged to use it, it is not a hard requirement as it technically isn&#39;t anything special.  However, some misbehaving school/office network will at time block traffic to non-standard ports, and setting this flag to &#x60;true&#x60; will ensure selection of a server that uses these. (optional, default to false)
     *
     * @throws InvalidArgumentException
     */
    public function getAtHomeServerChapterIdAsyncWithHttpInfo(string $chapter_id, bool $force_port443 = false): PromiseInterface
    {
        $returnType = '\Mapsred\MangadexSDK\Model\InlineResponse2003';
        $request = $this->getAtHomeServerChapterIdRequest($chapter_id, $force_port443);

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
     * Create request for operation 'getAtHomeServerChapterId'
     *
     * @param  string $chapter_id Chapter ID (required)
     * @param  bool $force_port443 Force selecting from MangaDex@Home servers that use the standard HTTPS port 443.  While the conventional port for HTTPS traffic is 443 and servers are encouraged to use it, it is not a hard requirement as it technically isn&#39;t anything special.  However, some misbehaving school/office network will at time block traffic to non-standard ports, and setting this flag to &#x60;true&#x60; will ensure selection of a server that uses these. (optional, default to false)
     *
     * @throws InvalidArgumentException
     */
    public function getAtHomeServerChapterIdRequest(string $chapter_id, bool $force_port443 = false): Request
    {
        // verify the required parameter 'chapter_id' is set
        if ($chapter_id === null || (is_array($chapter_id) && count($chapter_id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $chapter_id when calling getAtHomeServerChapterId'
            );
        }

        $resourcePath = '/at-home/server/{chapterId}';
        $queryParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        if ($force_port443 !== null) {
            if('form' === 'form' && is_array($force_port443)) {
                foreach($force_port443 as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams['forcePort443'] = $force_port443;
            }
        }


        // path params
        if ($chapter_id !== null) {
            $resourcePath = str_replace(
                '{chapterId}',
                ObjectSerializer::toPathValue($chapter_id),
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
        if (count(self::FORM_PARAMS) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach (self::FORM_PARAMS as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === self::APPLICATION_JSON) {
                $httpBody = json_encode(self::FORM_PARAMS);

            } else {
                // for HTTP post (form)
                $httpBody = Query::build(self::FORM_PARAMS);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent() !== '') {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            self::HEADER_PARAMS,
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
