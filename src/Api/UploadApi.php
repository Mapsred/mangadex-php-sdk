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
use Mapsred\MangadexSDK\Model\CoverResponse;
use Mapsred\MangadexSDK\Model\ErrorResponse;
use Mapsred\MangadexSDK\Model\ModelInterface;
use Mapsred\MangadexSDK\ObjectSerializer;
use RuntimeException;
use SplFileObject;

final class UploadApi
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
    private const QUERY_PARAMS = [];
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
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === self::APPLICATION_JSON) {
                $httpBody = json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = Query::build($formParams);
            }
        }

        // this endpoint requires Bearer authentication (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
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

        $query = Query::build(self::QUERY_PARAMS);
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
