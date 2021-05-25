<?php declare(strict_types=1);
/**
 * ScanlationGroupApi
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
use MangadexSDK\Model\CreateScanlationGroup;
use MangadexSDK\Model\ErrorResponse;
use MangadexSDK\Model\ModelInterface;
use MangadexSDK\Model\Response;
use MangadexSDK\Model\ScanlationGroupEdit;
use MangadexSDK\Model\ScanlationGroupList;
use MangadexSDK\Model\ScanlationGroupResponse;
use MangadexSDK\ObjectSerializer;

/**
 * ScanlationGroupApi Class Doc Comment
 *
 * @category Class
 * @package  MangadexSDK
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
final class ScanlationGroupApi
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
    private const GET = 'GET';
    /**
     * @var string
     */
    private const FORM = 'form';

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
     * Operation deleteGroupId
     *
     * Delete Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return Response|ErrorResponse|ErrorResponse
     */
    public function deleteGroupId(string $id): ModelInterface
    {
        list($response) = $this->deleteGroupIdWithHttpInfo($id);
        return $response;
    }

    /**
     * Operation deleteGroupIdWithHttpInfo
     *
     * Delete Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\Response|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function deleteGroupIdWithHttpInfo(string $id)
    {
        $request = $this->deleteGroupIdRequest($id);

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
     * Operation deleteGroupIdAsync
     *
     * Delete Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteGroupIdAsync(string $id): PromiseInterface
    {
        return $this->deleteGroupIdAsyncWithHttpInfo($id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation deleteGroupIdAsyncWithHttpInfo
     *
     * Delete Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteGroupIdAsyncWithHttpInfo(string $id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\Response';
        $request = $this->deleteGroupIdRequest($id);

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
     * Create request for operation 'deleteGroupId'
     *
     * @param  string $id Scanlation Group ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteGroupIdRequest(string $id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling deleteGroupId'
            );
        }

        $resourcePath = '/group/{id}';
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
            'DELETE',
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation deleteGroupIdFollow
     *
     * Unfollow Scanlation Group
     *
     * @param  string $id id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return Response|ErrorResponse
     */
    public function deleteGroupIdFollow(string $id): ModelInterface
    {
        list($response) = $this->deleteGroupIdFollowWithHttpInfo($id);
        return $response;
    }

    /**
     * Operation deleteGroupIdFollowWithHttpInfo
     *
     * Unfollow Scanlation Group
     *
     * @param  string $id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\Response|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function deleteGroupIdFollowWithHttpInfo(string $id)
    {
        $request = $this->deleteGroupIdFollowRequest($id);

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
     * Operation deleteGroupIdFollowAsync
     *
     * Unfollow Scanlation Group
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteGroupIdFollowAsync(string $id): PromiseInterface
    {
        return $this->deleteGroupIdFollowAsyncWithHttpInfo($id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation deleteGroupIdFollowAsyncWithHttpInfo
     *
     * Unfollow Scanlation Group
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteGroupIdFollowAsyncWithHttpInfo(string $id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\Response';
        $request = $this->deleteGroupIdFollowRequest($id);

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
     * Create request for operation 'deleteGroupIdFollow'
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function deleteGroupIdFollowRequest(string $id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling deleteGroupIdFollow'
            );
        }

        $resourcePath = '/group/{id}/follow';
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
            'DELETE',
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getGroupId
     *
     * View Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return ScanlationGroupResponse|ErrorResponse
     */
    public function getGroupId(string $id): ModelInterface
    {
        list($response) = $this->getGroupIdWithHttpInfo($id);
        return $response;
    }

    /**
     * Operation getGroupIdWithHttpInfo
     *
     * View Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\ScanlationGroupResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getGroupIdWithHttpInfo(string $id)
    {
        $request = $this->getGroupIdRequest($id);

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
                    if ('\MangadexSDK\Model\ScanlationGroupResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ScanlationGroupResponse', []),
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

            $returnType = '\MangadexSDK\Model\ScanlationGroupResponse';
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
                        '\MangadexSDK\Model\ScanlationGroupResponse',
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
     * Operation getGroupIdAsync
     *
     * View Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getGroupIdAsync(string $id): PromiseInterface
    {
        return $this->getGroupIdAsyncWithHttpInfo($id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getGroupIdAsyncWithHttpInfo
     *
     * View Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getGroupIdAsyncWithHttpInfo(string $id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\ScanlationGroupResponse';
        $request = $this->getGroupIdRequest($id);

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
     * Create request for operation 'getGroupId'
     *
     * @param  string $id Scanlation Group ID (required)
     *
     * @throws \InvalidArgumentException
     */
    public function getGroupIdRequest(string $id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling getGroupId'
            );
        }

        $resourcePath = '/group/{id}';
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
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\ScanlationGroupList|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
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
                    if ('\MangadexSDK\Model\ScanlationGroupList' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ScanlationGroupList', []),
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

            $returnType = '\MangadexSDK\Model\ScanlationGroupList';
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
                        '\MangadexSDK\Model\ScanlationGroupList',
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
     * Operation getSearchGroupAsync
     *
     * Scanlation Group list
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     * @param  string[] $ids ScanlationGroup ids (limited to 100 per request) (optional)
     * @param  string $name (optional)
     *
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     */
    public function getSearchGroupAsyncWithHttpInfo(int $limit = 10, int $offset = null, array $ids = null, string $name = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\ScanlationGroupList';
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
     * @throws \InvalidArgumentException
     */
    public function getSearchGroupRequest(int $limit = 10, int $offset = null, array $ids = null, string $name = null): Request
    {
        if ($limit !== null && $limit > 100) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling ScanlationGroupApi.getSearchGroup, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling ScanlationGroupApi.getSearchGroup, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new \InvalidArgumentException('invalid value for "$offset" when calling ScanlationGroupApi.getSearchGroup, must be bigger than or equal to 0.');
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
     * Operation getUserFollowsGroup
     *
     * Get logged User followed Groups
     *
     * @param  int $limit limit (optional, default to 10)
     * @param  int $offset offset (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     */
    public function getUserFollowsGroup(int $limit = 10, int $offset = null): ScanlationGroupList
    {
        list($response) = $this->getUserFollowsGroupWithHttpInfo($limit, $offset);
        return $response;
    }

    /**
     * Operation getUserFollowsGroupWithHttpInfo
     *
     * Get logged User followed Groups
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\ScanlationGroupList, HTTP status code, HTTP response headers (array of strings)
     */
    public function getUserFollowsGroupWithHttpInfo(int $limit = 10, int $offset = null)
    {
        $request = $this->getUserFollowsGroupRequest($limit, $offset);

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
                if ('\MangadexSDK\Model\ScanlationGroupList' === '\SplFileObject') {
                    $content = $response->getBody(); //stream goes to serializer
                } else {
                    $content = (string) $response->getBody();
                }
                return [
                    ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ScanlationGroupList', []),
                    $response->getStatusCode(),
                    $response->getHeaders()
                ];
            }

            $returnType = '\MangadexSDK\Model\ScanlationGroupList';
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
                    '\MangadexSDK\Model\ScanlationGroupList',
                    $e->getResponseHeaders()
                );
                $e->setResponseObject($data);
            }
            throw $e;
        }
    }

    /**
     * Operation getUserFollowsGroupAsync
     *
     * Get logged User followed Groups
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getUserFollowsGroupAsync(int $limit = 10, int $offset = null): PromiseInterface
    {
        return $this->getUserFollowsGroupAsyncWithHttpInfo($limit, $offset)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation getUserFollowsGroupAsyncWithHttpInfo
     *
     * Get logged User followed Groups
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getUserFollowsGroupAsyncWithHttpInfo(int $limit = 10, int $offset = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\ScanlationGroupList';
        $request = $this->getUserFollowsGroupRequest($limit, $offset);

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
     * Create request for operation 'getUserFollowsGroup'
     *
     * @param  int $limit (optional, default to 10)
     * @param  int $offset (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function getUserFollowsGroupRequest(int $limit = 10, int $offset = null): Request
    {
        if ($limit !== null && $limit > 100) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling ScanlationGroupApi.getUserFollowsGroup, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new \InvalidArgumentException('invalid value for "$limit" when calling ScanlationGroupApi.getUserFollowsGroup, must be bigger than or equal to 1.');
        }

        if ($offset !== null && $offset < 0) {
            throw new \InvalidArgumentException('invalid value for "$offset" when calling ScanlationGroupApi.getUserFollowsGroup, must be bigger than or equal to 0.');
        }


        $resourcePath = '/user/follows/group';
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
     * Operation postGroup
     *
     * Create Scanlation Group
     *
     * @param CreateScanlationGroup $create_scanlation_group The size of the body is limited to 8KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return ScanlationGroupResponse|ErrorResponse|ErrorResponse
     */
    public function postGroup(CreateScanlationGroup $create_scanlation_group = null): ModelInterface
    {
        list($response) = $this->postGroupWithHttpInfo($create_scanlation_group);
        return $response;
    }

    /**
     * Operation postGroupWithHttpInfo
     *
     * Create Scanlation Group
     *
     * @param CreateScanlationGroup $create_scanlation_group The size of the body is limited to 8KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\ScanlationGroupResponse|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function postGroupWithHttpInfo(CreateScanlationGroup $create_scanlation_group = null)
    {
        $request = $this->postGroupRequest($create_scanlation_group);

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
                    if ('\MangadexSDK\Model\ScanlationGroupResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ScanlationGroupResponse', []),
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

            $returnType = '\MangadexSDK\Model\ScanlationGroupResponse';
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
                        '\MangadexSDK\Model\ScanlationGroupResponse',
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
     * Operation postGroupAsync
     *
     * Create Scanlation Group
     *
     * @param CreateScanlationGroup $create_scanlation_group The size of the body is limited to 8KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function postGroupAsync(CreateScanlationGroup $create_scanlation_group = null): PromiseInterface
    {
        return $this->postGroupAsyncWithHttpInfo($create_scanlation_group)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation postGroupAsyncWithHttpInfo
     *
     * Create Scanlation Group
     *
     * @param CreateScanlationGroup $create_scanlation_group The size of the body is limited to 8KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function postGroupAsyncWithHttpInfo(CreateScanlationGroup $create_scanlation_group = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\ScanlationGroupResponse';
        $request = $this->postGroupRequest($create_scanlation_group);

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
     * Create request for operation 'postGroup'
     *
     * @param CreateScanlationGroup $create_scanlation_group The size of the body is limited to 8KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function postGroupRequest(CreateScanlationGroup $create_scanlation_group = null): Request
    {

        $resourcePath = '/group';
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
        if (isset($create_scanlation_group)) {
            if ($headers[self::CONTENT_TYPE] === self::APPLICATION_JSON) {
                $httpBody = json_encode(ObjectSerializer::sanitizeForSerialization($create_scanlation_group));
            } else {
                $httpBody = $create_scanlation_group;
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
            'POST',
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation postGroupIdFollow
     *
     * Follow Scanlation Group
     *
     * @param  string $id id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return Response|ErrorResponse
     */
    public function postGroupIdFollow(string $id): ModelInterface
    {
        list($response) = $this->postGroupIdFollowWithHttpInfo($id);
        return $response;
    }

    /**
     * Operation postGroupIdFollowWithHttpInfo
     *
     * Follow Scanlation Group
     *
     * @param  string $id (required)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\Response|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function postGroupIdFollowWithHttpInfo(string $id)
    {
        $request = $this->postGroupIdFollowRequest($id);

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
     * Operation postGroupIdFollowAsync
     *
     * Follow Scanlation Group
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function postGroupIdFollowAsync(string $id): PromiseInterface
    {
        return $this->postGroupIdFollowAsyncWithHttpInfo($id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation postGroupIdFollowAsyncWithHttpInfo
     *
     * Follow Scanlation Group
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function postGroupIdFollowAsyncWithHttpInfo(string $id): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\Response';
        $request = $this->postGroupIdFollowRequest($id);

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
     * Create request for operation 'postGroupIdFollow'
     *
     * @param  string $id (required)
     *
     * @throws \InvalidArgumentException
     */
    public function postGroupIdFollowRequest(string $id): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling postGroupIdFollow'
            );
        }

        $resourcePath = '/group/{id}/follow';
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
            'POST',
            $this->config->getHost() . $resourcePath . ($query !== '' ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation putGroupId
     *
     * Update Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     * @param ScanlationGroupEdit $scanlation_group_edit The size of the body is limited to 8KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return ScanlationGroupResponse|ErrorResponse|ErrorResponse|ErrorResponse
     */
    public function putGroupId(string $id, ScanlationGroupEdit $scanlation_group_edit = null): ModelInterface
    {
        list($response) = $this->putGroupIdWithHttpInfo($id, $scanlation_group_edit);
        return $response;
    }

    /**
     * Operation putGroupIdWithHttpInfo
     *
     * Update Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     * @param ScanlationGroupEdit $scanlation_group_edit The size of the body is limited to 8KB. (optional)
     *
     * @throws ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \MangadexSDK\Model\ScanlationGroupResponse|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse|\MangadexSDK\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function putGroupIdWithHttpInfo(string $id, ScanlationGroupEdit $scanlation_group_edit = null)
    {
        $request = $this->putGroupIdRequest($id, $scanlation_group_edit);

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
                    if ('\MangadexSDK\Model\ScanlationGroupResponse' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\MangadexSDK\Model\ScanlationGroupResponse', []),
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

            $returnType = '\MangadexSDK\Model\ScanlationGroupResponse';
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
                        '\MangadexSDK\Model\ScanlationGroupResponse',
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
     * Operation putGroupIdAsync
     *
     * Update Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     * @param ScanlationGroupEdit $scanlation_group_edit The size of the body is limited to 8KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function putGroupIdAsync(string $id, ScanlationGroupEdit $scanlation_group_edit = null): PromiseInterface
    {
        return $this->putGroupIdAsyncWithHttpInfo($id, $scanlation_group_edit)
            ->then(
                function ($response) {
                    return $response[0];
                }
            )
        ;
    }

    /**
     * Operation putGroupIdAsyncWithHttpInfo
     *
     * Update Scanlation Group
     *
     * @param  string $id Scanlation Group ID (required)
     * @param ScanlationGroupEdit $scanlation_group_edit The size of the body is limited to 8KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function putGroupIdAsyncWithHttpInfo(string $id, ScanlationGroupEdit $scanlation_group_edit = null): PromiseInterface
    {
        $returnType = '\MangadexSDK\Model\ScanlationGroupResponse';
        $request = $this->putGroupIdRequest($id, $scanlation_group_edit);

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
     * Create request for operation 'putGroupId'
     *
     * @param  string $id Scanlation Group ID (required)
     * @param ScanlationGroupEdit $scanlation_group_edit The size of the body is limited to 8KB. (optional)
     *
     * @throws \InvalidArgumentException
     */
    public function putGroupIdRequest(string $id, ScanlationGroupEdit $scanlation_group_edit = null): Request
    {
        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling putGroupId'
            );
        }

        $resourcePath = '/group/{id}';
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
        if (isset($scanlation_group_edit)) {
            if ($headers[self::CONTENT_TYPE] === self::APPLICATION_JSON) {
                $httpBody = json_encode(ObjectSerializer::sanitizeForSerialization($scanlation_group_edit));
            } else {
                $httpBody = $scanlation_group_edit;
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
