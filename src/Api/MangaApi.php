<?php declare(strict_types=1);
/**
 * MangaApi
 * PHP version 7.2
 *
 * @category Class
 * @package  MangadexSDK
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * MangaDex API
 *
 * MangaDex is an ad-free manga reader offering high-quality images!  This document details our API as it is right now. It is in no way a promise to never change it, although we will endeavour to publicly notify any major change.  # Authentication  You can login with the `/auth/login` endpoint. On success, it will return a JWT that remains valid for 15 minutes along with a session token that allows refreshing without re-authenticating for 1 month.  # Rate limits  The API enforces rate-limits to protect our servers against malicious and/or mistaken use. The API keeps track of the requests on an IP-by-IP basis. Hence, if you're on a VPN, proxy or a shared network in general, the requests of other users on this network might affect you.  At first, a **global limit of 5 requests per second per IP address** is in effect.  > This limit is enforced across multiple load-balancers, and thus is not an exact value but rather a lower-bound that we guarantee. The exact value will be somewhere in the range `[5, 5*n]` (with `n` being the number of load-balancers currently active). The exact value within this range will depend on the current traffic patterns we are experiencing.  On top of this, **some endpoints are further restricted** as follows:  | Endpoint                           | Requests per time period    | Time period in minutes | |------------------------------------|--------------------------   |------------------------| | `POST   /account/create`           | 1                           | 60                     | | `GET    /account/activate/{code}`  | 30                          | 60                     | | `POST   /account/activate/resend`  | 5                           | 60                     | | `POST   /account/recover`          | 5                           | 60                     | | `POST   /account/recover/{code}`   | 5                           | 60                     | | `POST   /auth/login`               | 30                          | 60                     | | `POST   /auth/refresh`             | 30                          | 60                     | | `POST   /author`                   | 10                          | 60                     | | `PUT    /author`                   | 10                          | 1                      | | `DELETE /author/{id}`              | 10                          | 10                     | | `POST   /captcha/solve`            | 10                          | 10                     | | `POST   /chapter/{id}/read`        | 300                         | 10                     | | `PUT    /chapter/{id}`             | 10                          | 1                      | | `DELETE /chapter/{id}`             | 10                          | 1                      | | `POST   /manga`                    | 10                          | 60                     | | `PUT    /manga/{id}`               | 10                          | 60                     | | `DELETE /manga/{id}`               | 10                          | 10                     | | `POST   /cover`                    | 10                          | 1                      | | `PUT    /cover/{id}`               | 10                          | 1                      | | `DELETE /cover/{id}`               | 10                          | 10                     | | `POST   /group`                    | 10                          | 60                     | | `PUT    /group/{id}`               | 10                          | 1                      | | `DELETE /group/{id}`               | 10                          | 10                     | | `GET    /at-home/server/{id}`      | 60                          | 1                      |  Calling these endpoints will further provide details via the following headers about your remaining quotas:  | Header                    | Description                                                                 | |---------------------------|-----------------------------------------------------------------------------| | `X-RateLimit-Limit`       | Maximal number of requests this endpoint allows per its time period         | | `X-RateLimit-Remaining`   | Remaining number of requests within your quota for the current time period  | | `X-RateLimit-Retry-After` | Timestamp of the end of the current time period, as UNIX timestamp          |  # Captchas  Some endpoints may require captchas to proceed, in order to slow down automated malicious traffic. Legitimate users might also be affected, based on the frequency of write requests or due certain endpoints being particularly sensitive to malicious use, such as user signup.  Once an endpoint decides that a captcha needs to be solved, a 403 Forbidden response will be returned, with the error code `captcha_required_exception`. The sitekey needed for recaptcha to function is provided in both the `X-Captcha-Sitekey` header field, as well as in the error context, specified as `siteKey` parameter.  The captcha result of the client can either be passed into the repeated original request with the `X-Captcha-Result` header or alternatively to the `POST /captcha/solve` endpoint. The time a solved captcha is remembered varies across different endpoints and can also be influenced by individual client behavior.  Authentication is not required for the `POST /captcha/solve` endpoint, captchas are tracked both by client ip and logged in user id. If you are logged in, you want to send the session token along, so you validate the captcha for your client ip and user id at the same time, but it is not required.  # Reading a chapter using the API  ## Retrieving pages from the MangaDex@Home network  A valid [MangaDex@Home network](https://mangadex.network) page URL is in the following format: `{server-specific base url}/{temporary access token}/{quality mode}/{chapter hash}/{filename}`  There are currently 2 quality modes: - `data`: Original upload quality - `data-saver`: Compressed quality  Upon fetching a chapter from the API, you will find 4 fields necessary to compute MangaDex@Home page URLs:  | Field                        | Type     | Description                       | |------------------------------|----------|-----------------------------------| | `.data.id`                   | `string` | API Chapter ID                    | | `.data.attributes.hash`      | `string` | MangaDex@Home Chapter Hash        | | `.data.attributes.data`      | `array`  | data quality mode filenames       | | `.data.attributes.dataSaver` | `array`  | data-saver quality mode filenames |  Example ```json GET /chapter/{id}  {   ...,   \"data\": {     \"id\": \"e46e5118-80ce-4382-a506-f61a24865166\",     ...,     \"attributes\": {       ...,       \"hash\": \"e199c7d73af7a58e8a4d0263f03db660\",       \"data\": [         \"x1-b765e86d5ecbc932cf3f517a8604f6ac6d8a7f379b0277a117dc7c09c53d041e.png\",         ...       ],       \"dataSaver\": [         \"x1-ab2b7c8f30c843aa3a53c29bc8c0e204fba4ab3e75985d761921eb6a52ff6159.jpg\",         ...       ]     }   } } ```  From this point you miss only the base URL to an assigned MangaDex@Home server for your client and chapter. This is retrieved via a `GET` request to `/at-home/server/{ chapter .data.id }`.  Example: ```json GET /at-home/server/e46e5118-80ce-4382-a506-f61a24865166  {   \"baseUrl\": \"https://abcdefg.hijklmn.mangadex.network:12345/some-token\" } ```  The full URL is the constructed as follows ``` { server .baseUrl }/{ quality mode }/{ chapter .data.attributes.hash }/{ chapter .data.attributes.{ quality mode }.[*] }  Examples  data quality: https://abcdefg.hijklmn.mangadex.network:12345/some-token/data/e199c7d73af7a58e8a4d0263f03db660/x1-b765e86d5ecbc932cf3f517a8604f6ac6d8a7f379b0277a117dc7c09c53d041e.png        base url: https://abcdefg.hijklmn.mangadex.network:12345/some-token   quality mode: data   chapter hash: e199c7d73af7a58e8a4d0263f03db660       filename: x1-b765e86d5ecbc932cf3f517a8604f6ac6d8a7f379b0277a117dc7c09c53d041e.png   data-saver quality: https://abcdefg.hijklmn.mangadex.network:12345/some-token/data-saver/e199c7d73af7a58e8a4d0263f03db660/x1-ab2b7c8f30c843aa3a53c29bc8c0e204fba4ab3e75985d761921eb6a52ff6159.jpg        base url: https://abcdefg.hijklmn.mangadex.network:12345/some-token   quality mode: data-saver   chapter hash: e199c7d73af7a58e8a4d0263f03db660       filename: x1-ab2b7c8f30c843aa3a53c29bc8c0e204fba4ab3e75985d761921eb6a52ff6159.jpg ```  If the server you have been assigned fails to serve images, you are allowed to call the `/at-home/server/{ chapter id }` endpoint again to get another server.  Whether successful or not, **please do report the result you encountered as detailed below**. This is so we can pull the faulty server out of the network.  ## Report  In order to keep track of the health of the servers in the network and to improve the quality of service and reliability, we ask that you call the MangaDex@Home report endpoint after each image you retrieve, whether successfully or not.  It is a `POST` request against `https://api.mangadex.network/report` and expects the following payload with our example above:  | Field                       | Type       | Description                                                                         | |-----------------------------|------------|-------------------------------------------------------------------------------------| | `url`                       | `string`   | The full URL of the image                                                           | | `success`                   | `boolean`  | Whether the image was successfully retrieved                                        | | `cached `                   | `boolean`  | `true` iff the server returned an `X-Cache` header with a value starting with `HIT` | | `bytes`                     | `number`   | The size in bytes of the retrieved image                                            | | `duration`                  | `number`   | The time in miliseconds that the complete retrieval (not TTFB) of this image took   |  Examples herafter.  **Success:** ```json POST https://api.mangadex.network/report Content-Type: application/json  {   \"url\": \"https://abcdefg.hijklmn.mangadex.network:12345/some-token/data/e199c7d73af7a58e8a4d0263f03db660/x1-b765e86d5ecbc932cf3f517a8604f6ac6d8a7f379b0277a117dc7c09c53d041e.png\",   \"success\": true,   \"bytes\": 727040,   \"duration\": 235,   \"cached\": true } ```  **Failure:** ```json POST https://api.mangadex.network/report Content-Type: application/json  {  \"url\": \"https://abcdefg.hijklmn.mangadex.network:12345/some-token/data/e199c7d73af7a58e8a4d0263f03db660/x1-b765e86d5ecbc932cf3f517a8604f6ac6d8a7f379b0277a117dc7c09c53d041e.png\",  \"success\": false,  \"bytes\": 25,  \"duration\": 235,  \"cached\": false } ```  While not strictly necessary, this helps us monitor the network's healthiness, and we appreciate your cooperation towards this goal. If no one reports successes and failures, we have no way to know that a given server is slow/broken, which eventually results in broken image retrieval for everyone.  # Static data  ## Manga publication demographic  | Value            | Description               | |------------------|---------------------------| | shounen          | Manga is a Shounen        | | shoujo           | Manga is a Shoujo         | | josei            | Manga is a Josei          | | seinen           | Manga is a Seinen         |  ## Manga status  | Value            | Description               | |------------------|---------------------------| | ongoing          | Manga is still going on   | | completed        | Manga is completed        | | hiatus           | Manga is paused           | | cancelled        | Manga has been cancelled  |  ## Manga reading status  | Value            | |------------------| | reading          | | on_hold          | | plan\\_to\\_read   | | dropped          | | re\\_reading      | | completed        |  ## Manga content rating  | Value            | Description               | |------------------|---------------------------| | safe             | Safe content              | | suggestive       | Suggestive content        | | erotica          | Erotica content           | | pornographic     | Pornographic content      |  ## CustomList visibility  | Value            | Description               | |------------------|---------------------------| | public           | CustomList is public      | | private          | CustomList is private     |  ## Relationship types  | Value            | Description                    | |------------------|--------------------------------| | manga            | Manga resource                 | | chapter          | Chapter resource               | | cover_art        | A Cover Art for a manga `*`    | | author           | Author resource                | | artist           | Author resource (drawers only) | | scanlation_group | ScanlationGroup resource       | | tag              | Tag resource                   | | user             | User resource                  | | custom_list      | CustomList resource            |  `*` Note, that on manga resources you get only one cover_art resource relation marking the primary cover if there are more than one. By default this will be the latest volume's cover art. If you like to see all the covers for a given manga, use the cover search endpoint for your mangaId and select the one you wish to display.  ## Manga links data  In Manga attributes you have the `links` field that is a JSON object with some strange keys, here is how to decode this object:  | Key   | Related site  | URL                                                                                           | URL details                                                    | |-------|---------------|-----------------------------------------------------------------------------------------------|----------------------------------------------------------------| | al    | anilist       | https://anilist.co/manga/`{id}`                                                               | Stored as id                                                   | | ap    | animeplanet   | https://www.anime-planet.com/manga/`{slug}`                                                   | Stored as slug                                                 | | bw    | bookwalker.jp | https://bookwalker.jp/`{slug}`                                                                | Stored has \"series/{id}\"                                       | | mu    | mangaupdates  | https://www.mangaupdates.com/series.html?id=`{id}`                                            | Stored has id                                                  | | nu    | novelupdates  | https://www.novelupdates.com/series/`{slug}`                                                  | Stored has slug                                                | | kt    | kitsu.io      | https://kitsu.io/api/edge/manga/`{id}` or https://kitsu.io/api/edge/manga?filter[slug]={slug} | If integer, use id version of the URL, otherwise use slug one  | | amz   | amazon        | N/A                                                                                           | Stored as full URL                                             | | ebj   | ebookjapan    | N/A                                                                                           | Stored as full URL                                             | | mal   | myanimelist   | https://myanimelist.net/manga/{id}                                                            | Store as id                                                    | | raw   | N/A           | N/A                                                                                           | Stored as full URL, untranslated stuff URL (original language) | | engtl | N/A           | N/A                                                                                           | Stored as full URL, official english licenced URL              |
 *
 * The version of the OpenAPI document: 5.0.13
 * Contact: mangadexstaff@gmail.com
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 5.2.0-SNAPSHOT
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace MangadexSDK\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use function GuzzleHttp\json_encode;
use GuzzleHttp\Promise\PromiseInterface;
use function GuzzleHttp\Psr7\build_query;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use MangadexSDK\ApiException;
use MangadexSDK\Configuration;
use MangadexSDK\HeaderSelector;
use MangadexSDK\Model\ChapterList;
use MangadexSDK\Model\ErrorResponse;
use MangadexSDK\Model\InlineResponse200;
use MangadexSDK\Model\InlineResponse2001;
use MangadexSDK\Model\InlineResponse2004;
use MangadexSDK\Model\InlineResponse2005;
use MangadexSDK\Model\MangaCreate;
use MangadexSDK\Model\MangaEdit;
use MangadexSDK\Model\MangaList;
use MangadexSDK\Model\MangaResponse;
use MangadexSDK\Model\ModelInterface;
use MangadexSDK\Model\Response;
use MangadexSDK\Model\TagResponse;
use MangadexSDK\Model\UpdateMangaStatus;
use MangadexSDK\ObjectSerializer;

/**
 * MangaApi Class Doc Comment
 *
 * @category Class
 * @package  MangadexSDK
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
final class MangaApi
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
    private const ID = 'id';
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
    private const DELETE = 'DELETE';
    /**
     * @var string
     */
    private const GET = 'GET';
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
    private const TRANSLATED_LANGUAGE = 'translatedLanguage';
    /**
     * @var string
     */
    private const CREATED_AT_SINCE = 'createdAtSince';
    /**
     * @var string
     */
    private const UPDATED_AT_SINCE = 'updatedAtSince';
    /**
     * @var string
     */
    private const ORDER = 'order';
    /**
     * @var string
     */
    private const POST = 'POST';

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
     * Operation deleteMangaId
     *
     * Delete Manga
     *
     * @param  string $id Manga ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return Response|ErrorResponse|ErrorResponse
     */
    public function deleteMangaId(string $id): ModelInterface
    {
        list($response) = $this->deleteMangaIdWithHttpInfo($id);
        return $response;
    }

    /**
     * Operation deleteMangaIdWithHttpInfo
     *
     * Delete Manga
     *
     * @param  string $id Manga ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\Response|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function deleteMangaIdWithHttpInfo(string $id)
    {
        $request = $this->deleteMangaIdRequest($id);

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
                    if ('\MangadexSDK\Model\Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                case 404:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\Response';
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
                        '\MangadexSDK\Model\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation deleteMangaIdAsync
     *
     * Delete Manga
     *
     * @param  string $id Manga ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteMangaIdAsync(string $id): PromiseInterface
    {
        return $this->deleteMangaIdAsyncWithHttpInfo($id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation deleteMangaIdAsyncWithHttpInfo
     *
     * Delete Manga
     *
     * @param  string $id Manga ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteMangaIdAsyncWithHttpInfo(string $id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\Response';
        $request = $this->deleteMangaIdRequest($id);

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
     * Create request for operation 'deleteMangaId'
     *
     * @param  string $id Manga ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteMangaIdRequest(string $id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling deleteMangaId'
            );
        }

        $resourcePath = '/manga/{id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::DELETE,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation deleteMangaIdFollow
     *
     * Unfollow Manga
     *
     * @param  string $id id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return Response|ErrorResponse
     */
    public function deleteMangaIdFollow(string $id): ModelInterface
    {
        list($response) = $this->deleteMangaIdFollowWithHttpInfo($id);
        return $response;
    }

    /**
     * Operation deleteMangaIdFollowWithHttpInfo
     *
     * Unfollow Manga
     *
     * @param  string $id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\Response|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function deleteMangaIdFollowWithHttpInfo(string $id)
    {
        $request = $this->deleteMangaIdFollowRequest($id);

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
                    if ('\MangadexSDK\Model\Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 404:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\Response';
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
                        '\MangadexSDK\Model\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation deleteMangaIdFollowAsync
     *
     * Unfollow Manga
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteMangaIdFollowAsync(string $id): PromiseInterface
    {
        return $this->deleteMangaIdFollowAsyncWithHttpInfo($id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation deleteMangaIdFollowAsyncWithHttpInfo
     *
     * Unfollow Manga
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteMangaIdFollowAsyncWithHttpInfo(string $id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\Response';
        $request = $this->deleteMangaIdFollowRequest($id);

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
     * Create request for operation 'deleteMangaIdFollow'
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteMangaIdFollowRequest(string $id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling deleteMangaIdFollow'
            );
        }

        $resourcePath = '/manga/{id}/follow';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::DELETE,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation deleteMangaIdListListId
     *
     * Remove Manga in CustomList
     *
     * @param  string $id Manga ID (required)
     * @param  string $list_id CustomList ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return Response|ErrorResponse|ErrorResponse
     */
    public function deleteMangaIdListListId(string $id, string $list_id): ModelInterface
    {
        list($response) = $this->deleteMangaIdListListIdWithHttpInfo($id, $list_id);
        return $response;
    }

    /**
     * Operation deleteMangaIdListListIdWithHttpInfo
     *
     * Remove Manga in CustomList
     *
     * @param  string $id Manga ID (required)
     * @param  string $list_id CustomList ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\Response|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function deleteMangaIdListListIdWithHttpInfo(string $id, string $list_id)
    {
        $request = $this->deleteMangaIdListListIdRequest($id, $list_id);

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
                    if ('\MangadexSDK\Model\Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                case 404:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\Response';
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
                        '\MangadexSDK\Model\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation deleteMangaIdListListIdAsync
     *
     * Remove Manga in CustomList
     *
     * @param  string $id Manga ID (required)
     * @param  string $list_id CustomList ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteMangaIdListListIdAsync(string $id, string $list_id): PromiseInterface
    {
        return $this->deleteMangaIdListListIdAsyncWithHttpInfo($id, $list_id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation deleteMangaIdListListIdAsyncWithHttpInfo
     *
     * Remove Manga in CustomList
     *
     * @param  string $id Manga ID (required)
     * @param  string $list_id CustomList ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteMangaIdListListIdAsyncWithHttpInfo(string $id, string $list_id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\Response';
        $request = $this->deleteMangaIdListListIdRequest($id, $list_id);

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
     * Create request for operation 'deleteMangaIdListListId'
     *
     * @param  string $id Manga ID (required)
     * @param  string $list_id CustomList ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteMangaIdListListIdRequest(string $id, string $list_id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling deleteMangaIdListListId'
            );
        }
        // verify the required parameter 'list_id' is set
        if ($list_id === null || (is_array($list_id) && count($list_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $list_id when calling deleteMangaIdListListId'
            );
        }

        $resourcePath = '/manga/{id}/list/{listId}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }
        // path params
        if ($list_id !== null) {
            $resourcePath = str_replace(
                '{listId}',
                ObjectSerializer::toPathValue($list_id),
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::DELETE,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getMangaChapterReadmarkers
     *
     * Manga read markers
     *
     * @param  string $id id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     */
    public function getMangaChapterReadmarkers(string $id): InlineResponse2001
    {
        list($response) = $this->getMangaChapterReadmarkersWithHttpInfo($id);
        return $response;
    }

    /**
     * Operation getMangaChapterReadmarkersWithHttpInfo
     *
     * Manga read markers
     *
     * @param  string $id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\InlineResponse2001, HTTP status code, HTTP response headers (array of strings)
     */
    public function getMangaChapterReadmarkersWithHttpInfo(string $id)
    {
        $request = $this->getMangaChapterReadmarkersRequest($id);

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

            if ($statusCode === 200) {
                if ('\MangadexSDK\Model\InlineResponse2001' === '\SplFileObject') {
                    $content = $response->getBody(); //stream goes to serializer
                } else {
                    $content = (string) $response->getBody();
                }
                return [
                    ObjectSerializer::deserialize($content, '\MangadexSDK\Model\InlineResponse2001', []),
                    $response->getStatusCode(),
                    $response->getHeaders()
                ];
            }

            $returnType = '\MangadexSDK\Model\InlineResponse2001';
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
            if ($e->getCode() === 200) {
                $data = ObjectSerializer::deserialize(
                    $e->getResponseBody(),
                    '\MangadexSDK\Model\InlineResponse2001',
                    $e->getResponseHeaders()
                );
                $e->setResponseObject($data);
            }
            throw $e;
        }
    }

    /**
     * Operation getMangaChapterReadmarkersAsync
     *
     * Manga read markers
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaChapterReadmarkersAsync(string $id): PromiseInterface
    {
        return $this->getMangaChapterReadmarkersAsyncWithHttpInfo($id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getMangaChapterReadmarkersAsyncWithHttpInfo
     *
     * Manga read markers
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaChapterReadmarkersAsyncWithHttpInfo(string $id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\InlineResponse2001';
        $request = $this->getMangaChapterReadmarkersRequest($id);

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
     * Create request for operation 'getMangaChapterReadmarkers'
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaChapterReadmarkersRequest(string $id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling getMangaChapterReadmarkers'
            );
        }

        $resourcePath = '/manga/{id}/read';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getMangaChapterReadmarkers2
     *
     * Manga read markers
     *
     * @param  string[] $ids Manga ids (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     */
    public function getMangaChapterReadmarkers2(array $ids): InlineResponse2001
    {
        list($response) = $this->getMangaChapterReadmarkers2WithHttpInfo($ids);
        return $response;
    }

    /**
     * Operation getMangaChapterReadmarkers2WithHttpInfo
     *
     * Manga read markers
     *
     * @param  string[] $ids Manga ids (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\InlineResponse2001, HTTP status code, HTTP response headers (array of strings)
     */
    public function getMangaChapterReadmarkers2WithHttpInfo(array $ids)
    {
        $request = $this->getMangaChapterReadmarkers2Request($ids);

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

            if ($statusCode === 200) {
                if ('\MangadexSDK\Model\InlineResponse2001' === '\SplFileObject') {
                    $content = $response->getBody(); //stream goes to serializer
                } else {
                    $content = (string) $response->getBody();
                }
                return [
                    ObjectSerializer::deserialize($content, '\MangadexSDK\Model\InlineResponse2001', []),
                    $response->getStatusCode(),
                    $response->getHeaders()
                ];
            }

            $returnType = '\MangadexSDK\Model\InlineResponse2001';
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
            if ($e->getCode() === 200) {
                $data = ObjectSerializer::deserialize(
                    $e->getResponseBody(),
                    '\MangadexSDK\Model\InlineResponse2001',
                    $e->getResponseHeaders()
                );
                $e->setResponseObject($data);
            }
            throw $e;
        }
    }

    /**
     * Operation getMangaChapterReadmarkers2Async
     *
     * Manga read markers
     *
     * @param  string[] $ids Manga ids (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaChapterReadmarkers2Async(array $ids): PromiseInterface
    {
        return $this->getMangaChapterReadmarkers2AsyncWithHttpInfo($ids)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getMangaChapterReadmarkers2AsyncWithHttpInfo
     *
     * Manga read markers
     *
     * @param  string[] $ids Manga ids (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaChapterReadmarkers2AsyncWithHttpInfo(array $ids): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\InlineResponse2001';
        $request = $this->getMangaChapterReadmarkers2Request($ids);

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
     * Create request for operation 'getMangaChapterReadmarkers2'
     *
     * @param  string[] $ids Manga ids (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaChapterReadmarkers2Request(array $ids): Request
    {
        // verify the required parameter 'ids' is set
        if ($ids === null || (is_array($ids) && count($ids) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $ids when calling getMangaChapterReadmarkers2'
            );
        }

        $resourcePath = '/manga/read';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getMangaId
     *
     * View Manga
     *
     * @param  string $id Manga ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return MangaResponse|ErrorResponse
     */
    public function getMangaId(string $id): ModelInterface
    {
        list($response) = $this->getMangaIdWithHttpInfo($id);
        return $response;
    }

    /**
     * Operation getMangaIdWithHttpInfo
     *
     * View Manga
     *
     * @param  string $id Manga ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\MangaResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getMangaIdWithHttpInfo(string $id)
    {
        $request = $this->getMangaIdRequest($id);

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
                    if ('\MangadexSDK\Model\MangaResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\MangaResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\MangaResponse';
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
                        '\MangadexSDK\Model\MangaResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation getMangaIdAsync
     *
     * View Manga
     *
     * @param  string $id Manga ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaIdAsync(string $id): PromiseInterface
    {
        return $this->getMangaIdAsyncWithHttpInfo($id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getMangaIdAsyncWithHttpInfo
     *
     * View Manga
     *
     * @param  string $id Manga ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaIdAsyncWithHttpInfo(string $id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\MangaResponse';
        $request = $this->getMangaIdRequest($id);

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
     * Create request for operation 'getMangaId'
     *
     * @param  string $id Manga ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaIdRequest(string $id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling getMangaId'
            );
        }

        $resourcePath = '/manga/{id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getMangaIdFeed
     *
     * Manga feed
     *
     * @param  string $id Manga ID (required)
     * @param  int $limit limit (optional, default to 100)
     * @param  int $offset offset (optional)
     * @param  string[] $translated_language translated_language (optional)
     * @param  string $created_at_since created_at_since (optional)
     * @param  string $updated_at_since updated_at_since (optional)
     * @param  string $publish_at_since publish_at_since (optional)
     * @param  Order6 $order order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return ChapterList|ErrorResponse
     */
    public function getMangaIdFeed(string $id, int $limit = 100, int $offset = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): ModelInterface
    {
        list($response) = $this->getMangaIdFeedWithHttpInfo($id, $limit, $offset, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order);
        return $response;
    }

    /**
     * Operation getMangaIdFeedWithHttpInfo
     *
     * Manga feed
     *
     * @param  string $id Manga ID (required)
     * @param  int $limit (optional, default to 100)
     * @param  int $offset (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order6 $order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\ChapterList|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getMangaIdFeedWithHttpInfo(string $id, int $limit = 100, int $offset = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null)
    {
        $request = $this->getMangaIdFeedRequest($id, $limit, $offset, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order);

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
                    if ('\MangadexSDK\Model\ChapterList' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ChapterList', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\ChapterList';
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
                        '\MangadexSDK\Model\ChapterList',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation getMangaIdFeedAsync
     *
     * Manga feed
     *
     * @param  string $id Manga ID (required)
     * @param  int $limit (optional, default to 100)
     * @param  int $offset (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order6 $order (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaIdFeedAsync(string $id, int $limit = 100, int $offset = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): PromiseInterface
    {
        return $this->getMangaIdFeedAsyncWithHttpInfo($id, $limit, $offset, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getMangaIdFeedAsyncWithHttpInfo
     *
     * Manga feed
     *
     * @param  string $id Manga ID (required)
     * @param  int $limit (optional, default to 100)
     * @param  int $offset (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order6 $order (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaIdFeedAsyncWithHttpInfo(string $id, int $limit = 100, int $offset = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\ChapterList';
        $request = $this->getMangaIdFeedRequest($id, $limit, $offset, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order);

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
     * Create request for operation 'getMangaIdFeed'
     *
     * @param  string $id Manga ID (required)
     * @param  int $limit (optional, default to 100)
     * @param  int $offset (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order6 $order (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaIdFeedRequest(string $id, int $limit = 100, int $offset = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling getMangaIdFeed'
            );
        }
        if ($limit !== null && $limit > 500) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling MangaApi.getMangaIdFeed, must be smaller than or equal to 500.');
        }
        if ($limit !== null && $limit < 1) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling MangaApi.getMangaIdFeed, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new \InvalidArgumentException('invalid value for "$offset" when calling MangaApi.getMangaIdFeed, must be bigger than or equal to 0.');
        }

        if ($created_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $created_at_since)) {
            throw new \InvalidArgumentException("invalid value for \"created_at_since\" when calling MangaApi.getMangaIdFeed, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }

        if ($updated_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $updated_at_since)) {
            throw new \InvalidArgumentException("invalid value for \"updated_at_since\" when calling MangaApi.getMangaIdFeed, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }

        if ($publish_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $publish_at_since)) {
            throw new \InvalidArgumentException("invalid value for \"publish_at_since\" when calling MangaApi.getMangaIdFeed, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }


        $resourcePath = '/manga/{id}/feed';
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
        if ($translated_language !== null) {
            if(self::FORM === self::FORM && is_array($translated_language)) {
                foreach($translated_language as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams[self::TRANSLATED_LANGUAGE] = $translated_language;
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
                $queryParams[self::CREATED_AT_SINCE] = $created_at_since;
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
                $queryParams[self::UPDATED_AT_SINCE] = $updated_at_since;
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


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getMangaIdStatus
     *
     * Get a Manga reading status
     *
     * @param  string $id id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return InlineResponse2005|ErrorResponse|ErrorResponse
     */
    public function getMangaIdStatus(string $id): ModelInterface
    {
        list($response) = $this->getMangaIdStatusWithHttpInfo($id);
        return $response;
    }

    /**
     * Operation getMangaIdStatusWithHttpInfo
     *
     * Get a Manga reading status
     *
     * @param  string $id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\InlineResponse2005|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getMangaIdStatusWithHttpInfo(string $id)
    {
        $request = $this->getMangaIdStatusRequest($id);

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
                    if ('\MangadexSDK\Model\InlineResponse2005' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\InlineResponse2005', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                case 404:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\InlineResponse2005';
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
                        '\MangadexSDK\Model\InlineResponse2005',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation getMangaIdStatusAsync
     *
     * Get a Manga reading status
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaIdStatusAsync(string $id): PromiseInterface
    {
        return $this->getMangaIdStatusAsyncWithHttpInfo($id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getMangaIdStatusAsyncWithHttpInfo
     *
     * Get a Manga reading status
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaIdStatusAsyncWithHttpInfo(string $id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\InlineResponse2005';
        $request = $this->getMangaIdStatusRequest($id);

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
     * Create request for operation 'getMangaIdStatus'
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaIdStatusRequest(string $id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling getMangaIdStatus'
            );
        }

        $resourcePath = '/manga/{id}/status';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getMangaRandom
     *
     * Get a random Manga
     *
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     */
    public function getMangaRandom(): MangaResponse
    {
        list($response) = $this->getMangaRandomWithHttpInfo();
        return $response;
    }

    /**
     * Operation getMangaRandomWithHttpInfo
     *
     * Get a random Manga
     *
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\MangaResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getMangaRandomWithHttpInfo()
    {
        $request = $this->getMangaRandomRequest();

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

            if ($statusCode === 200) {
                if ('\MangadexSDK\Model\MangaResponse' === '\SplFileObject') {
                    $content = $response->getBody(); //stream goes to serializer
                } else {
                    $content = (string) $response->getBody();
                }
                return [
                    ObjectSerializer::deserialize($content, '\MangadexSDK\Model\MangaResponse', []),
                    $response->getStatusCode(),
                    $response->getHeaders()
                ];
            }

            $returnType = '\MangadexSDK\Model\MangaResponse';
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
            if ($e->getCode() === 200) {
                $data = ObjectSerializer::deserialize(
                    $e->getResponseBody(),
                    '\MangadexSDK\Model\MangaResponse',
                    $e->getResponseHeaders()
                );
                $e->setResponseObject($data);
            }
            throw $e;
        }
    }

    /**
     * Operation getMangaRandomAsync
     *
     * Get a random Manga
     *
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaRandomAsync(): PromiseInterface
    {
        return $this->getMangaRandomAsyncWithHttpInfo()
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getMangaRandomAsyncWithHttpInfo
     *
     * Get a random Manga
     *
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaRandomAsyncWithHttpInfo(): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\MangaResponse';
        $request = $this->getMangaRandomRequest();

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
     * Create request for operation 'getMangaRandom'
     *
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaRandomRequest(): Request
    {

        $resourcePath = '/manga/random';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getMangaStatus
     *
     * Get all Manga reading status for logged User
     *
     * @param  string $status Used to filter the list by given status (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     */
    public function getMangaStatus(string $status = null): InlineResponse2004
    {
        list($response) = $this->getMangaStatusWithHttpInfo($status);
        return $response;
    }

    /**
     * Operation getMangaStatusWithHttpInfo
     *
     * Get all Manga reading status for logged User
     *
     * @param  string $status Used to filter the list by given status (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\InlineResponse2004, HTTP status code, HTTP response headers (array of strings)
     */
    public function getMangaStatusWithHttpInfo(string $status = null)
    {
        $request = $this->getMangaStatusRequest($status);

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

            if ($statusCode === 200) {
                if ('\MangadexSDK\Model\InlineResponse2004' === '\SplFileObject') {
                    $content = $response->getBody(); //stream goes to serializer
                } else {
                    $content = (string) $response->getBody();
                }
                return [
                    ObjectSerializer::deserialize($content, '\MangadexSDK\Model\InlineResponse2004', []),
                    $response->getStatusCode(),
                    $response->getHeaders()
                ];
            }

            $returnType = '\MangadexSDK\Model\InlineResponse2004';
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
            if ($e->getCode() === 200) {
                $data = ObjectSerializer::deserialize(
                    $e->getResponseBody(),
                    '\MangadexSDK\Model\InlineResponse2004',
                    $e->getResponseHeaders()
                );
                $e->setResponseObject($data);
            }
            throw $e;
        }
    }

    /**
     * Operation getMangaStatusAsync
     *
     * Get all Manga reading status for logged User
     *
     * @param  string $status Used to filter the list by given status (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaStatusAsync(string $status = null): PromiseInterface
    {
        return $this->getMangaStatusAsyncWithHttpInfo($status)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getMangaStatusAsyncWithHttpInfo
     *
     * Get all Manga reading status for logged User
     *
     * @param  string $status Used to filter the list by given status (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaStatusAsyncWithHttpInfo(string $status = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\InlineResponse2004';
        $request = $this->getMangaStatusRequest($status);

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
     * Create request for operation 'getMangaStatus'
     *
     * @param  string $status Used to filter the list by given status (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaStatusRequest(string $status = null): Request
    {

        $resourcePath = '/manga/status';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getMangaTag
     *
     * Tag list
     *
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return TagResponse[]
     */
    public function getMangaTag(): array
    {
        list($response) = $this->getMangaTagWithHttpInfo();
        return $response;
    }

    /**
     * Operation getMangaTagWithHttpInfo
     *
     * Tag list
     *
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\TagResponse[], HTTP status code, HTTP response headers (array of strings)
     */
    public function getMangaTagWithHttpInfo()
    {
        $request = $this->getMangaTagRequest();

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

            if ($statusCode === 200) {
                if ('\MangadexSDK\Model\TagResponse[]' === '\SplFileObject') {
                    $content = $response->getBody(); //stream goes to serializer
                } else {
                    $content = (string) $response->getBody();
                }
                return [
                    ObjectSerializer::deserialize($content, '\MangadexSDK\Model\TagResponse[]', []),
                    $response->getStatusCode(),
                    $response->getHeaders()
                ];
            }

            $returnType = '\MangadexSDK\Model\TagResponse[]';
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
            if ($e->getCode() === 200) {
                $data = ObjectSerializer::deserialize(
                    $e->getResponseBody(),
                    '\MangadexSDK\Model\TagResponse[]',
                    $e->getResponseHeaders()
                );
                $e->setResponseObject($data);
            }
            throw $e;
        }
    }

    /**
     * Operation getMangaTagAsync
     *
     * Tag list
     *
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaTagAsync(): PromiseInterface
    {
        return $this->getMangaTagAsyncWithHttpInfo()
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getMangaTagAsyncWithHttpInfo
     *
     * Tag list
     *
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaTagAsyncWithHttpInfo(): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\TagResponse[]';
        $request = $this->getMangaTagRequest();

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
     * Create request for operation 'getMangaTag'
     *
     *
     * @throws \InvalidArgumentException
     */
    public function getMangaTagRequest(): Request
    {

        $resourcePath = '/manga/tag';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
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
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\MangaList|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
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
                    if ('\MangadexSDK\Model\MangaList' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\MangaList', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\MangaList';
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
                        '\MangadexSDK\Model\MangaList',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
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
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     */
    public function getSearchMangaAsyncWithHttpInfo(int $limit = 10, int $offset = null, string $title = null, array $authors = null, array $artists = null, int $year = null, array $included_tags = null, string $included_tags_mode = 'AND', array $excluded_tags = null, string $excluded_tags_mode = 'OR', array $status = null, array $original_language = null, array $publication_demographic = null, array $ids = null, array $content_rating = null, string $created_at_since = null, string $updated_at_since = null, $order = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\MangaList';
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
     * @throws \InvalidArgumentException
     */
    public function getSearchMangaRequest(int $limit = 10, int $offset = null, string $title = null, array $authors = null, array $artists = null, int $year = null, array $included_tags = null, string $included_tags_mode = 'AND', array $excluded_tags = null, string $excluded_tags_mode = 'OR', array $status = null, array $original_language = null, array $publication_demographic = null, array $ids = null, array $content_rating = null, string $created_at_since = null, string $updated_at_since = null, $order = null): Request
    {
        if ($limit !== null && $limit > 100) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling MangaApi.getSearchManga, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling MangaApi.getSearchManga, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new \InvalidArgumentException('invalid value for "$offset" when calling MangaApi.getSearchManga, must be bigger than or equal to 0.');
        }

        if ($created_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $created_at_since)) {
            throw new \InvalidArgumentException("invalid value for \"created_at_since\" when calling MangaApi.getSearchManga, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }

        if ($updated_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $updated_at_since)) {
            throw new \InvalidArgumentException("invalid value for \"updated_at_since\" when calling MangaApi.getSearchManga, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
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
                $queryParams['ids'] = $ids;
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
                $queryParams[self::CREATED_AT_SINCE] = $created_at_since;
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
                $queryParams[self::UPDATED_AT_SINCE] = $updated_at_since;
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
                $httpBody = json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getUserFollowsManga
     *
     * Get logged User followed Manga list
     *
     * @param  int $limit limit (optional, default to 10)
     * @param  int $offset offset (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     */
    public function getUserFollowsManga(int $limit = 10, int $offset = null): MangaList
    {
        list($response) = $this->getUserFollowsMangaWithHttpInfo($limit, $offset);
        return $response;
    }

    /**
     * Operation getUserFollowsMangaWithHttpInfo
     *
     * Get logged User followed Manga list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\MangaList, HTTP status code, HTTP response headers (array of strings)
     */
    public function getUserFollowsMangaWithHttpInfo(int $limit = 10, int $offset = null)
    {
        $request = $this->getUserFollowsMangaRequest($limit, $offset);

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

            if ($statusCode === 200) {
                if ('\MangadexSDK\Model\MangaList' === '\SplFileObject') {
                    $content = $response->getBody(); //stream goes to serializer
                } else {
                    $content = (string) $response->getBody();
                }
                return [
                    ObjectSerializer::deserialize($content, '\MangadexSDK\Model\MangaList', []),
                    $response->getStatusCode(),
                    $response->getHeaders()
                ];
            }

            $returnType = '\MangadexSDK\Model\MangaList';
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
            if ($e->getCode() === 200) {
                $data = ObjectSerializer::deserialize(
                    $e->getResponseBody(),
                    '\MangadexSDK\Model\MangaList',
                    $e->getResponseHeaders()
                );
                $e->setResponseObject($data);
            }
            throw $e;
        }
    }

    /**
     * Operation getUserFollowsMangaAsync
     *
     * Get logged User followed Manga list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getUserFollowsMangaAsync(int $limit = 10, int $offset = null): PromiseInterface
    {
        return $this->getUserFollowsMangaAsyncWithHttpInfo($limit, $offset)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getUserFollowsMangaAsyncWithHttpInfo
     *
     * Get logged User followed Manga list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getUserFollowsMangaAsyncWithHttpInfo(int $limit = 10, int $offset = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\MangaList';
        $request = $this->getUserFollowsMangaRequest($limit, $offset);

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
     * Create request for operation 'getUserFollowsManga'
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getUserFollowsMangaRequest(int $limit = 10, int $offset = null): Request
    {
        if ($limit !== null && $limit > 100) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling MangaApi.getUserFollowsManga, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling MangaApi.getUserFollowsManga, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new \InvalidArgumentException('invalid value for "$offset" when calling MangaApi.getUserFollowsManga, must be bigger than or equal to 0.');
        }


        $resourcePath = '/user/follows/manga';
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getUserFollowsMangaFeed
     *
     * Get logged User followed Manga feed
     *
     * @param  int $limit limit (optional, default to 100)
     * @param  int $offset offset (optional)
     * @param  string[] $translated_language translated_language (optional)
     * @param  string $created_at_since created_at_since (optional)
     * @param  string $updated_at_since updated_at_since (optional)
     * @param  string $publish_at_since publish_at_since (optional)
     * @param  Order2 $order order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return ChapterList|ErrorResponse
     */
    public function getUserFollowsMangaFeed(int $limit = 100, int $offset = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): ModelInterface
    {
        list($response) = $this->getUserFollowsMangaFeedWithHttpInfo($limit, $offset, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order);
        return $response;
    }

    /**
     * Operation getUserFollowsMangaFeedWithHttpInfo
     *
     * Get logged User followed Manga feed
     *
     * @param  int $limit (optional, default to 100)
     * @param  int $offset (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order2 $order (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\ChapterList|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getUserFollowsMangaFeedWithHttpInfo(int $limit = 100, int $offset = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null)
    {
        $request = $this->getUserFollowsMangaFeedRequest($limit, $offset, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order);

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
                    if ('\MangadexSDK\Model\ChapterList' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ChapterList', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\ChapterList';
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
                        '\MangadexSDK\Model\ChapterList',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation getUserFollowsMangaFeedAsync
     *
     * Get logged User followed Manga feed
     *
     * @param  int $limit (optional, default to 100)
     * @param  int $offset (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order2 $order (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getUserFollowsMangaFeedAsync(int $limit = 100, int $offset = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): PromiseInterface
    {
        return $this->getUserFollowsMangaFeedAsyncWithHttpInfo($limit, $offset, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getUserFollowsMangaFeedAsyncWithHttpInfo
     *
     * Get logged User followed Manga feed
     *
     * @param  int $limit (optional, default to 100)
     * @param  int $offset (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order2 $order (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getUserFollowsMangaFeedAsyncWithHttpInfo(int $limit = 100, int $offset = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\ChapterList';
        $request = $this->getUserFollowsMangaFeedRequest($limit, $offset, $translated_language, $created_at_since, $updated_at_since, $publish_at_since, $order);

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
     * Create request for operation 'getUserFollowsMangaFeed'
     *
     * @param  int $limit (optional, default to 100)
     * @param  int $offset (optional)
     * @param  string[] $translated_language (optional)
     * @param  string $created_at_since (optional)
     * @param  string $updated_at_since (optional)
     * @param  string $publish_at_since (optional)
     * @param  Order2 $order (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getUserFollowsMangaFeedRequest(int $limit = 100, int $offset = null, array $translated_language = null, string $created_at_since = null, string $updated_at_since = null, string $publish_at_since = null, $order = null): Request
    {
        if ($limit !== null && $limit > 500) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling MangaApi.getUserFollowsMangaFeed, must be smaller than or equal to 500.');
        }
        if ($limit !== null && $limit < 1) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling MangaApi.getUserFollowsMangaFeed, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new \InvalidArgumentException('invalid value for "$offset" when calling MangaApi.getUserFollowsMangaFeed, must be bigger than or equal to 0.');
        }

        if ($created_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $created_at_since)) {
            throw new \InvalidArgumentException("invalid value for \"created_at_since\" when calling MangaApi.getUserFollowsMangaFeed, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }

        if ($updated_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $updated_at_since)) {
            throw new \InvalidArgumentException("invalid value for \"updated_at_since\" when calling MangaApi.getUserFollowsMangaFeed, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }

        if ($publish_at_since !== null && !preg_match("/^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/", $publish_at_since)) {
            throw new \InvalidArgumentException("invalid value for \"publish_at_since\" when calling MangaApi.getUserFollowsMangaFeed, must conform to the pattern /^\\d{4}-[0-1]\\d-([0-2]\\d|3[0-1])T([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/.");
        }


        $resourcePath = '/user/follows/manga/feed';
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
        if ($translated_language !== null) {
            if(self::FORM === self::FORM && is_array($translated_language)) {
                foreach($translated_language as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams[self::TRANSLATED_LANGUAGE] = $translated_language;
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
                $queryParams[self::CREATED_AT_SINCE] = $created_at_since;
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
                $queryParams[self::UPDATED_AT_SINCE] = $updated_at_since;
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
                $httpBody = json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation mangaIdAggregateGet
     *
     * Get Manga volumes &amp; chapters
     *
     * @param  string $id Manga ID (required)
     * @param  string[] $translated_language translated_language (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     */
    public function mangaIdAggregateGet(string $id, array $translated_language = null): InlineResponse200
    {
        list($response) = $this->mangaIdAggregateGetWithHttpInfo($id, $translated_language);
        return $response;
    }

    /**
     * Operation mangaIdAggregateGetWithHttpInfo
     *
     * Get Manga volumes &amp; chapters
     *
     * @param  string $id Manga ID (required)
     * @param  string[] $translated_language (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\InlineResponse200, HTTP status code, HTTP response headers (array of strings)
     */
    public function mangaIdAggregateGetWithHttpInfo(string $id, array $translated_language = null)
    {
        $request = $this->mangaIdAggregateGetRequest($id, $translated_language);

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

            if ($statusCode === 200) {
                if ('\MangadexSDK\Model\InlineResponse200' === '\SplFileObject') {
                    $content = $response->getBody(); //stream goes to serializer
                } else {
                    $content = (string) $response->getBody();
                }
                return [
                    ObjectSerializer::deserialize($content, '\MangadexSDK\Model\InlineResponse200', []),
                    $response->getStatusCode(),
                    $response->getHeaders()
                ];
            }

            $returnType = '\MangadexSDK\Model\InlineResponse200';
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
            if ($e->getCode() === 200) {
                $data = ObjectSerializer::deserialize(
                    $e->getResponseBody(),
                    '\MangadexSDK\Model\InlineResponse200',
                    $e->getResponseHeaders()
                );
                $e->setResponseObject($data);
            }
            throw $e;
        }
    }

    /**
     * Operation mangaIdAggregateGetAsync
     *
     * Get Manga volumes &amp; chapters
     *
     * @param  string $id Manga ID (required)
     * @param  string[] $translated_language (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function mangaIdAggregateGetAsync(string $id, array $translated_language = null): PromiseInterface
    {
        return $this->mangaIdAggregateGetAsyncWithHttpInfo($id, $translated_language)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation mangaIdAggregateGetAsyncWithHttpInfo
     *
     * Get Manga volumes &amp; chapters
     *
     * @param  string $id Manga ID (required)
     * @param  string[] $translated_language (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function mangaIdAggregateGetAsyncWithHttpInfo(string $id, array $translated_language = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\InlineResponse200';
        $request = $this->mangaIdAggregateGetRequest($id, $translated_language);

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
     * Create request for operation 'mangaIdAggregateGet'
     *
     * @param  string $id Manga ID (required)
     * @param  string[] $translated_language (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function mangaIdAggregateGetRequest(string $id, array $translated_language = null): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling mangaIdAggregateGet'
            );
        }

        $resourcePath = '/manga/{id}/aggregate';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        if ($translated_language !== null) {
            if(self::FORM === self::FORM && is_array($translated_language)) {
                foreach($translated_language as $key => $value) {
                    $queryParams[$key] = $value;
                }
            }
            else {
                $queryParams[self::TRANSLATED_LANGUAGE] = $translated_language;
            }
        }


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::GET,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation postManga
     *
     * Create Manga
     *
     * @param MangaCreate $manga_create The size of the body is limited to 16KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return MangaResponse|ErrorResponse|ErrorResponse
     */
    public function postManga(MangaCreate $manga_create = null): ModelInterface
    {
        list($response) = $this->postMangaWithHttpInfo($manga_create);
        return $response;
    }

    /**
     * Operation postMangaWithHttpInfo
     *
     * Create Manga
     *
     * @param MangaCreate $manga_create The size of the body is limited to 16KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\MangaResponse|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function postMangaWithHttpInfo(MangaCreate $manga_create = null)
    {
        $request = $this->postMangaRequest($manga_create);

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
                    if ('\MangadexSDK\Model\MangaResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\MangaResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                case 403:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\MangaResponse';
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
                        '\MangadexSDK\Model\MangaResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation postMangaAsync
     *
     * Create Manga
     *
     * @param MangaCreate $manga_create The size of the body is limited to 16KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaAsync(MangaCreate $manga_create = null): PromiseInterface
    {
        return $this->postMangaAsyncWithHttpInfo($manga_create)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation postMangaAsyncWithHttpInfo
     *
     * Create Manga
     *
     * @param MangaCreate $manga_create The size of the body is limited to 16KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaAsyncWithHttpInfo(MangaCreate $manga_create = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\MangaResponse';
        $request = $this->postMangaRequest($manga_create);

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
     * Create request for operation 'postManga'
     *
     * @param MangaCreate $manga_create The size of the body is limited to 16KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaRequest(MangaCreate $manga_create = null): Request
    {

        $resourcePath = '/manga';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





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
        if (isset($manga_create)) {
            if ($headers[self::CONTENT_TYPE] === self::APPLICATION_JSON) {
                $httpBody = json_encode(ObjectSerializer::sanitizeForSerialization($manga_create));
            } else {
                $httpBody = $manga_create;
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::POST,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation postMangaIdFollow
     *
     * Follow Manga
     *
     * @param  string $id id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return Response|ErrorResponse
     */
    public function postMangaIdFollow(string $id): ModelInterface
    {
        list($response) = $this->postMangaIdFollowWithHttpInfo($id);
        return $response;
    }

    /**
     * Operation postMangaIdFollowWithHttpInfo
     *
     * Follow Manga
     *
     * @param  string $id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\Response|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function postMangaIdFollowWithHttpInfo(string $id)
    {
        $request = $this->postMangaIdFollowRequest($id);

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
                    if ('\MangadexSDK\Model\Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 404:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\Response';
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
                        '\MangadexSDK\Model\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation postMangaIdFollowAsync
     *
     * Follow Manga
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaIdFollowAsync(string $id): PromiseInterface
    {
        return $this->postMangaIdFollowAsyncWithHttpInfo($id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation postMangaIdFollowAsyncWithHttpInfo
     *
     * Follow Manga
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaIdFollowAsyncWithHttpInfo(string $id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\Response';
        $request = $this->postMangaIdFollowRequest($id);

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
     * Create request for operation 'postMangaIdFollow'
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaIdFollowRequest(string $id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling postMangaIdFollow'
            );
        }

        $resourcePath = '/manga/{id}/follow';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::POST,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation postMangaIdListListId
     *
     * Add Manga in CustomList
     *
     * @param  string $id Manga ID (required)
     * @param  string $list_id CustomList ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return Response|ErrorResponse|ErrorResponse
     */
    public function postMangaIdListListId(string $id, string $list_id): ModelInterface
    {
        list($response) = $this->postMangaIdListListIdWithHttpInfo($id, $list_id);
        return $response;
    }

    /**
     * Operation postMangaIdListListIdWithHttpInfo
     *
     * Add Manga in CustomList
     *
     * @param  string $id Manga ID (required)
     * @param  string $list_id CustomList ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\Response|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function postMangaIdListListIdWithHttpInfo(string $id, string $list_id)
    {
        $request = $this->postMangaIdListListIdRequest($id, $list_id);

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
                    if ('\MangadexSDK\Model\Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                case 404:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\Response';
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
                        '\MangadexSDK\Model\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation postMangaIdListListIdAsync
     *
     * Add Manga in CustomList
     *
     * @param  string $id Manga ID (required)
     * @param  string $list_id CustomList ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaIdListListIdAsync(string $id, string $list_id): PromiseInterface
    {
        return $this->postMangaIdListListIdAsyncWithHttpInfo($id, $list_id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation postMangaIdListListIdAsyncWithHttpInfo
     *
     * Add Manga in CustomList
     *
     * @param  string $id Manga ID (required)
     * @param  string $list_id CustomList ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaIdListListIdAsyncWithHttpInfo(string $id, string $list_id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\Response';
        $request = $this->postMangaIdListListIdRequest($id, $list_id);

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
     * Create request for operation 'postMangaIdListListId'
     *
     * @param  string $id Manga ID (required)
     * @param  string $list_id CustomList ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaIdListListIdRequest(string $id, string $list_id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling postMangaIdListListId'
            );
        }
        // verify the required parameter 'list_id' is set
        if ($list_id === null || (is_array($list_id) && count($list_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $list_id when calling postMangaIdListListId'
            );
        }

        $resourcePath = '/manga/{id}/list/{listId}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }
        // path params
        if ($list_id !== null) {
            $resourcePath = str_replace(
                '{listId}',
                ObjectSerializer::toPathValue($list_id),
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::POST,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation postMangaIdStatus
     *
     * Update Manga reading status
     *
     * @param  string $id id (required)
     * @param UpdateMangaStatus $update_manga_status Using a &#x60;null&#x60; value in &#x60;status&#x60; field will remove the Manga reading status. The size of the body is limited to 2KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return Response|ErrorResponse|ErrorResponse
     */
    public function postMangaIdStatus(string $id, UpdateMangaStatus $update_manga_status = null): ModelInterface
    {
        list($response) = $this->postMangaIdStatusWithHttpInfo($id, $update_manga_status);
        return $response;
    }

    /**
     * Operation postMangaIdStatusWithHttpInfo
     *
     * Update Manga reading status
     *
     * @param  string $id (required)
     * @param UpdateMangaStatus $update_manga_status Using a &#x60;null&#x60; value in &#x60;status&#x60; field will remove the Manga reading status. The size of the body is limited to 2KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\Response|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function postMangaIdStatusWithHttpInfo(string $id, UpdateMangaStatus $update_manga_status = null)
    {
        $request = $this->postMangaIdStatusRequest($id, $update_manga_status);

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
                    if ('\MangadexSDK\Model\Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                case 404:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\Response';
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
                        '\MangadexSDK\Model\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation postMangaIdStatusAsync
     *
     * Update Manga reading status
     *
     * @param  string $id (required)
     * @param UpdateMangaStatus $update_manga_status Using a &#x60;null&#x60; value in &#x60;status&#x60; field will remove the Manga reading status. The size of the body is limited to 2KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaIdStatusAsync(string $id, UpdateMangaStatus $update_manga_status = null): PromiseInterface
    {
        return $this->postMangaIdStatusAsyncWithHttpInfo($id, $update_manga_status)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation postMangaIdStatusAsyncWithHttpInfo
     *
     * Update Manga reading status
     *
     * @param  string $id (required)
     * @param UpdateMangaStatus $update_manga_status Using a &#x60;null&#x60; value in &#x60;status&#x60; field will remove the Manga reading status. The size of the body is limited to 2KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaIdStatusAsyncWithHttpInfo(string $id, UpdateMangaStatus $update_manga_status = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\Response';
        $request = $this->postMangaIdStatusRequest($id, $update_manga_status);

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
     * Create request for operation 'postMangaIdStatus'
     *
     * @param  string $id (required)
     * @param UpdateMangaStatus $update_manga_status Using a &#x60;null&#x60; value in &#x60;status&#x60; field will remove the Manga reading status. The size of the body is limited to 2KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function postMangaIdStatusRequest(string $id, UpdateMangaStatus $update_manga_status = null): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling postMangaIdStatus'
            );
        }

        $resourcePath = '/manga/{id}/status';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
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
        if (isset($update_manga_status)) {
            if ($headers[self::CONTENT_TYPE] === self::APPLICATION_JSON) {
                $httpBody = json_encode(ObjectSerializer::sanitizeForSerialization($update_manga_status));
            } else {
                $httpBody = $update_manga_status;
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            self::POST,
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation putMangaId
     *
     * Update Manga
     *
     * @param  string $id Manga ID (required)
     * @param MangaEdit $manga_edit The size of the body is limited to 16KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return MangaResponse|ErrorResponse|ErrorResponse|ErrorResponse
     */
    public function putMangaId(string $id, MangaEdit $manga_edit = null): ModelInterface
    {
        list($response) = $this->putMangaIdWithHttpInfo($id, $manga_edit);
        return $response;
    }

    /**
     * Operation putMangaIdWithHttpInfo
     *
     * Update Manga
     *
     * @param  string $id Manga ID (required)
     * @param MangaEdit $manga_edit The size of the body is limited to 16KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\MangaResponse|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function putMangaIdWithHttpInfo(string $id, MangaEdit $manga_edit = null)
    {
        $request = $this->putMangaIdRequest($id, $manga_edit);

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
                    if ('\MangadexSDK\Model\MangaResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\MangaResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                case 403:
                case 404:
                    if ('\MangadexSDK\Model\ErrorResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ErrorResponse', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\MangadexSDK\Model\MangaResponse';
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
                        '\MangadexSDK\Model\MangaResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                case 403:
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\MangadexSDK\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation putMangaIdAsync
     *
     * Update Manga
     *
     * @param  string $id Manga ID (required)
     * @param MangaEdit $manga_edit The size of the body is limited to 16KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function putMangaIdAsync(string $id, MangaEdit $manga_edit = null): PromiseInterface
    {
        return $this->putMangaIdAsyncWithHttpInfo($id, $manga_edit)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation putMangaIdAsyncWithHttpInfo
     *
     * Update Manga
     *
     * @param  string $id Manga ID (required)
     * @param MangaEdit $manga_edit The size of the body is limited to 16KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function putMangaIdAsyncWithHttpInfo(string $id, MangaEdit $manga_edit = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\MangaResponse';
        $request = $this->putMangaIdRequest($id, $manga_edit);

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
     * Create request for operation 'putMangaId'
     *
     * @param  string $id Manga ID (required)
     * @param MangaEdit $manga_edit The size of the body is limited to 16KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function putMangaIdRequest(string $id, MangaEdit $manga_edit = null): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling putMangaId'
            );
        }

        $resourcePath = '/manga/{id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . self::ID . '}',
                ObjectSerializer::toPathValue($id),
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
        if (isset($manga_edit)) {
            if ($headers[self::CONTENT_TYPE] === self::APPLICATION_JSON) {
                $httpBody = json_encode(ObjectSerializer::sanitizeForSerialization($manga_edit));
            } else {
                $httpBody = $manga_edit;
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
                $httpBody = build_query($formParams);
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

        $query = build_query($queryParams);
        return new Request(
            'PUT',
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption(): array
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
