<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use JsonSerializable;

final class AuthorList implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;
    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    private $container = [];

    /**
      * The original name of the model.
      *
      * @var string
      */
    private static $openAPIModelName = 'AuthorList';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::RESULTS => '\Mapsred\MangadexSDK\Model\AuthorResponse[]',
        self::LIMIT => 'int',
        self::OFFSET => 'int',
        self::TOTAL => 'int'
    ];
    /**
     * @var string
     */
    private const RESULTS = 'results';
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
    private const TOTAL = 'total';
    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container[self::RESULTS] = $data[self::RESULTS] ?? null;
        $this->container[self::LIMIT] = $data[self::LIMIT] ?? null;
        $this->container[self::OFFSET] = $data[self::OFFSET] ?? null;
        $this->container[self::TOTAL] = $data[self::TOTAL] ?? null;
    }
    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    private static $openAPIFormats = [
        self::RESULTS => null,
        self::LIMIT => null,
        self::OFFSET => null,
        self::TOTAL => null
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    private static $attributeMap = [
        self::RESULTS => self::RESULTS,
        self::LIMIT => self::LIMIT,
        self::OFFSET => self::OFFSET,
        self::TOTAL => self::TOTAL
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    private static $setters = [
        self::RESULTS => 'setResults',
        self::LIMIT => 'setLimit',
        self::OFFSET => 'setOffset',
        self::TOTAL => 'setTotal'
    ];

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        return [];
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets results
     *
     * @return AuthorResponse[]|null
     */
    public function getResults(): ?array
    {
        return $this->container[self::RESULTS];
    }
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    private static $getters = [
        self::RESULTS => 'getResults',
        self::LIMIT => 'getLimit',
        self::OFFSET => 'getOffset',
        self::TOTAL => 'getTotal'
    ];

    /**
     * Sets results
     *
     * @param AuthorResponse[]|null $results results
     */
    public function setResults(?array $results): self
    {
        $this->container[self::RESULTS] = $results;

        return $this;
    }

    /**
     * Gets limit
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->container[self::LIMIT];
    }

    /**
     * Sets limit
     *
     * @param int|null $limit limit
     */
    public function setLimit(?int $limit): self
    {
        $this->container[self::LIMIT] = $limit;

        return $this;
    }

    /**
     * Gets offset
     *
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->container[self::OFFSET];
    }

    /**
     * Sets offset
     *
     * @param int|null $offset offset
     */
    public function setOffset(?int $offset): self
    {
        $this->container[self::OFFSET] = $offset;

        return $this;
    }

    /**
     * Gets total
     *
     * @return int|null
     */
    public function getTotal(): ?int
    {
        return $this->container[self::TOTAL];
    }

    /**
     * Sets total
     *
     * @param int|null $total total
     */
    public function setTotal(?int $total): self
    {
        $this->container[self::TOTAL] = $total;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    public function jsonSerialize()
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string|bool
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }
    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }
    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }
    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }
}


