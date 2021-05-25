<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use InvalidArgumentException;
use JsonSerializable;

final class MappingIdAttributes implements ModelInterface, ArrayAccess, JsonSerializable
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
    private static $openAPIModelName = 'MappingIdAttributes';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::TYPE => 'string',
        self::LEGACY_ID => 'int',
        self::NEW_ID => 'string'
    ];
    /**
     * @var string
     */
    private const TYPE = 'type';
    /**
     * @var string
     */
    private const LEGACY_ID = 'legacy_id';
    /**
     * @var string
     */
    private const NEW_ID = 'new_id';
    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container[self::TYPE] = $data[self::TYPE] ?? null;
        $this->container[self::LEGACY_ID] = $data[self::LEGACY_ID] ?? null;
        $this->container[self::NEW_ID] = $data[self::NEW_ID] ?? null;
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
        self::TYPE => null,
        self::LEGACY_ID => null,
        self::NEW_ID => 'uuid'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    private static $attributeMap = [
        self::TYPE => self::TYPE,
        self::LEGACY_ID => 'legacyId',
        self::NEW_ID => 'newId'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    private static $setters = [
        self::TYPE => 'setType',
        self::LEGACY_ID => 'setLegacyId',
        self::NEW_ID => 'setNewId'
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
    public function getTypeAllowableValues(): array
    {
        return [
            self::TYPE_MANGA,
            self::TYPE_CHAPTER,
            self::TYPE_GROUP,
            self::TYPE_TAG,
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

        $allowedValues = $this->getTypeAllowableValues();
        if (!is_null($this->container[self::TYPE]) && !in_array($this->container[self::TYPE], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'type', must be one of '%s'",
                $this->container[self::TYPE],
                implode("', '", $allowedValues)
            );
        }

        return $invalidProperties;
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

    const TYPE_MANGA = 'manga';
    const TYPE_CHAPTER = 'chapter';
    const TYPE_GROUP = 'group';
    const TYPE_TAG = 'tag';


    /**
     * Gets type
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->container[self::TYPE];
    }
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    private static $getters = [
        self::TYPE => 'getType',
        self::LEGACY_ID => 'getLegacyId',
        self::NEW_ID => 'getNewId'
    ];

    /**
     * Sets type
     *
     * @param string|null $type type
     */
    public function setType(?string $type): self
    {
        $allowedValues = $this->getTypeAllowableValues();
        if (!is_null($type) && !in_array($type, $allowedValues, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'type', must be one of '%s'",
                    $type,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container[self::TYPE] = $type;

        return $this;
    }

    /**
     * Gets legacy_id
     *
     * @return int|null
     */
    public function getLegacyId(): ?int
    {
        return $this->container[self::LEGACY_ID];
    }

    /**
     * Sets legacy_id
     *
     * @param int|null $legacy_id legacy_id
     */
    public function setLegacyId(?int $legacy_id): self
    {
        $this->container[self::LEGACY_ID] = $legacy_id;

        return $this;
    }

    /**
     * Gets new_id
     *
     * @return string|null
     */
    public function getNewId(): ?string
    {
        return $this->container[self::NEW_ID];
    }

    /**
     * Sets new_id
     *
     * @param string|null $new_id new_id
     */
    public function setNewId(?string $new_id): self
    {
        $this->container[self::NEW_ID] = $new_id;

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


