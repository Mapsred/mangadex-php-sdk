<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use InvalidArgumentException;
use JsonSerializable;

final class Order3 implements ModelInterface, ArrayAccess, JsonSerializable
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
    private static $openAPIModelName = 'order_3';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::VOLUME => 'string',
        self::CHAPTER => 'string'
    ];
    /**
     * @var string
     */
    private const VOLUME = 'volume';
    /**
     * @var string
     */
    private const CHAPTER = 'chapter';
    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container[self::VOLUME] = $data[self::VOLUME] ?? null;
        $this->container[self::CHAPTER] = $data[self::CHAPTER] ?? null;
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
        self::VOLUME => null,
        self::CHAPTER => null
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    private static $attributeMap = [
        self::VOLUME => self::VOLUME,
        self::CHAPTER => self::CHAPTER
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    private static $setters = [
        self::VOLUME => 'setVolume',
        self::CHAPTER => 'setChapter'
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
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getVolumeAllowableValues(): array
    {
        return [
            self::VOLUME_ASC,
            self::VOLUME_DESC,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getChapterAllowableValues(): array
    {
        return [
            self::CHAPTER_ASC,
            self::CHAPTER_DESC,
        ];
    }
    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        $allowedValues = $this->getVolumeAllowableValues();
        if (!is_null($this->container[self::VOLUME]) && !in_array($this->container[self::VOLUME], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'volume', must be one of '%s'",
                $this->container[self::VOLUME],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getChapterAllowableValues();
        if (!is_null($this->container[self::CHAPTER]) && !in_array($this->container[self::CHAPTER], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'chapter', must be one of '%s'",
                $this->container[self::CHAPTER],
                implode("', '", $allowedValues)
            );
        }

        return $invalidProperties;
    }

    const VOLUME_ASC = 'asc';
    const VOLUME_DESC = 'desc';
    const CHAPTER_ASC = 'asc';
    const CHAPTER_DESC = 'desc';

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
     * Gets volume
     *
     * @return string|null
     */
    public function getVolume(): ?string
    {
        return $this->container[self::VOLUME];
    }
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    private static $getters = [
        self::VOLUME => 'getVolume',
        self::CHAPTER => 'getChapter'
    ];

    /**
     * Sets volume
     *
     * @param string|null $volume volume
     */
    public function setVolume(?string $volume): self
    {
        $allowedValues = $this->getVolumeAllowableValues();
        if (!is_null($volume) && !in_array($volume, $allowedValues, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'volume', must be one of '%s'",
                    $volume,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container[self::VOLUME] = $volume;

        return $this;
    }

    /**
     * Gets chapter
     *
     * @return string|null
     */
    public function getChapter(): ?string
    {
        return $this->container[self::CHAPTER];
    }

    /**
     * Sets chapter
     *
     * @param string|null $chapter chapter
     */
    public function setChapter(?string $chapter): self
    {
        $allowedValues = $this->getChapterAllowableValues();
        if (!is_null($chapter) && !in_array($chapter, $allowedValues, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'chapter', must be one of '%s'",
                    $chapter,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container[self::CHAPTER] = $chapter;

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


