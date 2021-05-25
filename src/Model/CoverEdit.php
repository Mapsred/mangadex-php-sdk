<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use InvalidArgumentException;
use JsonSerializable;

final class CoverEdit implements ModelInterface, ArrayAccess, JsonSerializable
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
    private static $openAPIModelName = 'CoverEdit';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::VOLUME => 'string',
        self::DESCRIPTION => 'string',
        self::VERSION => 'int'
    ];
    /**
     * @var string
     */
    private const VOLUME = 'volume';
    /**
     * @var string
     */
    private const DESCRIPTION = 'description';
    /**
     * @var string
     */
    private const VERSION = 'version';
    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container[self::VOLUME] = $data[self::VOLUME] ?? null;
        $this->container[self::DESCRIPTION] = $data[self::DESCRIPTION] ?? null;
        $this->container[self::VERSION] = $data[self::VERSION] ?? null;
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
        self::DESCRIPTION => null,
        self::VERSION => null
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    private static $attributeMap = [
        self::VOLUME => self::VOLUME,
        self::DESCRIPTION => self::DESCRIPTION,
        self::VERSION => self::VERSION
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    private static $setters = [
        self::VOLUME => 'setVolume',
        self::DESCRIPTION => 'setDescription',
        self::VERSION => 'setVersion'
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
        $invalidProperties = [];

        if ($this->container[self::VOLUME] === null) {
            $invalidProperties[] = "'volume' can't be null";
        }
        if ((mb_strlen($this->container[self::VOLUME]) > 8)) {
            $invalidProperties[] = "invalid value for 'volume', the character length must be smaller than or equal to 8.";
        }

        if ((mb_strlen($this->container[self::VOLUME]) < 0)) {
            $invalidProperties[] = "invalid value for 'volume', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container[self::DESCRIPTION]) && (mb_strlen($this->container[self::DESCRIPTION]) > 512)) {
            $invalidProperties[] = "invalid value for 'description', the character length must be smaller than or equal to 512.";
        }

        if (!is_null($this->container[self::DESCRIPTION]) && (mb_strlen($this->container[self::DESCRIPTION]) < 0)) {
            $invalidProperties[] = "invalid value for 'description', the character length must be bigger than or equal to 0.";
        }

        if ($this->container[self::VERSION] === null) {
            $invalidProperties[] = "'version' can't be null";
        }
        if (($this->container[self::VERSION] < 1)) {
            $invalidProperties[] = "invalid value for 'version', must be bigger than or equal to 1.";
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


    /**
     * Gets volume
     */
    public function getVolume(): string
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
        self::DESCRIPTION => 'getDescription',
        self::VERSION => 'getVersion'
    ];

    /**
     * Sets volume
     *
     * @param string $volume volume
     */
    public function setVolume(string $volume): self
    {
        if ((mb_strlen($volume) > 8)) {
            throw new InvalidArgumentException('invalid length for $volume when calling CoverEdit., must be smaller than or equal to 8.');
        }
        if ((mb_strlen($volume) < 0)) {
            throw new InvalidArgumentException('invalid length for $volume when calling CoverEdit., must be bigger than or equal to 0.');
        }

        $this->container[self::VOLUME] = $volume;

        return $this;
    }

    /**
     * Gets description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->container[self::DESCRIPTION];
    }

    /**
     * Sets description
     *
     * @param string|null $description description
     */
    public function setDescription(?string $description): self
    {
        if (!is_null($description) && (mb_strlen($description) > 512)) {
            throw new InvalidArgumentException('invalid length for $description when calling CoverEdit., must be smaller than or equal to 512.');
        }
        if (!is_null($description) && (mb_strlen($description) < 0)) {
            throw new InvalidArgumentException('invalid length for $description when calling CoverEdit., must be bigger than or equal to 0.');
        }

        $this->container[self::DESCRIPTION] = $description;

        return $this;
    }

    /**
     * Gets version
     */
    public function getVersion(): int
    {
        return $this->container[self::VERSION];
    }

    /**
     * Sets version
     *
     * @param int $version version
     */
    public function setVersion(int $version): self
    {

        if (($version < 1)) {
            throw new InvalidArgumentException('invalid value for $version when calling CoverEdit., must be bigger than or equal to 1.');
        }

        $this->container[self::VERSION] = $version;

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


