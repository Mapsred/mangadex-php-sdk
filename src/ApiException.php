<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK;

use \Exception;
use stdClass;

final class ApiException extends Exception
{

    /**
     * The HTTP body of the server response either as Json or string.
     *
     * @var stdClass|string|null
     */
    private $responseBody;

    /**
     * The HTTP header of the server response.
     *
     * @var string[]|null
     */
    private $responseHeaders;

    /**
     * The deserialized response object
     *
     * @var stdClass|string|null
     */
    private $responseObject;

    /**
     * Constructor
     *
     * @param string                $message         Error message
     * @param int                   $code            HTTP status code
     * @param string[]|null         $responseHeaders HTTP response header
     * @param stdClass|string|null $responseBody HTTP decoded body of the server response either as \stdClass or string
     */
    public function __construct($message = "", $code = 0, $responseHeaders = [], $responseBody = null)
    {
        parent::__construct($message, $code);
        $this->responseHeaders = $responseHeaders;
        $this->responseBody = $responseBody;
    }

    /**
     * Gets the HTTP response header
     *
     * @return string[]|null HTTP response header
     */
    public function getResponseHeaders(): ?array
    {
        return $this->responseHeaders;
    }

    /**
     * Gets the HTTP body of the server response either as Json or string
     *
     * @return stdClass|string|null HTTP body of the server response either as \stdClass or string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * Sets the deseralized response object (during deserialization)
     *
     * @param mixed $obj Deserialized response object
     */
    public function setResponseObject($obj): void
    {
        $this->responseObject = $obj;
    }

    /**
     * Gets the deseralized response object (during deserialization)
     *
     * @return stdClass|string|null the deserialized response object
     */
    public function getResponseObject()
    {
        return $this->responseObject;
    }
}
