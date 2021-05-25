<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use JsonSerializable;

final class Error implements ModelInterface, ArrayAccess, JsonSerializable
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
    private static $openAPIModelName = 'Error';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::ID => 'string',
        self::STATUS => 'int',
        self::TITLE => 'string',
        self::DETAIL => 'string'
    ];
    /**
     * @var string
     */
    private const ID = 'id';
    /**
     * @var string
     */
    private const STATUS = 'status';
    /**
     * @var string
     */
    private const TITLE = 'title';
    /**
     * @var string
     */
    private const DETAIL = 'detail';
    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container[self::ID] = $data[self::ID] ?? null;
        $this->container[self::STATUS] = $data[self::STATUS] ?? null;
        $this->container[self::TITLE] = $data[self::TITLE] ?? null;
        $this->container[self::DETAIL] = $data[self::DETAIL] ?? null;
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
        self::ID => null,
        self::STATUS => null,
        self::TITLE => null,
        self::DETAIL => null
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    private static $attributeMap = [
        self::ID => self::ID,
        self::STATUS => self::STATUS,
        self::TITLE => self::TITLE,
        self::DETAIL => self::DETAIL
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    private static $setters = [
        self::ID => 'setId',
        self::STATUS => 'setStatus',
        self::TITLE => 'setTitle',
        self::DETAIL => 'setDetail'
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
     * Gets id
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->container[self::ID];
    }
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    private static $getters = [
        self::ID => 'getId',
        self::STATUS => 'getStatus',
        self::TITLE => 'getTitle',
        self::DETAIL => 'getDetail'
    ];

    /**
     * Sets id
     *
     * @param string|null $id id
     */
    public function setId(?string $id): self
    {
        $this->container[self::ID] = $id;

        return $this;
    }

    /**
     * Gets status
     *
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->container[self::STATUS];
    }

    /**
     * Sets status
     *
     * @param int|null $status status
     */
    public function setStatus(?int $status): self
    {
        $this->container[self::STATUS] = $status;

        return $this;
    }

    /**
     * Gets title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->container[self::TITLE];
    }

    /**
     * Sets title
     *
     * @param string|null $title title
     */
    public function setTitle(?string $title): self
    {
        $this->container[self::TITLE] = $title;

        return $this;
    }

    /**
     * Gets detail
     *
     * @return string|null
     */
    public function getDetail(): ?string
    {
        return $this->container[self::DETAIL];
    }

    /**
     * Sets detail
     *
     * @param string|null $detail detail
     */
    public function setDetail(?string $detail): self
    {
        $this->container[self::DETAIL] = $detail;

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


