<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use InvalidArgumentException;
use JsonSerializable;

final class CustomListEdit implements ModelInterface, ArrayAccess, JsonSerializable
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
    private static $openAPIModelName = 'CustomListEdit';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::NAME => 'string',
        self::VISIBILITY => 'string',
        self::MANGA => 'string[]',
        self::VERSION => 'int'
    ];
    /**
     * @var string
     */
    private const NAME = 'name';
    /**
     * @var string
     */
    private const VISIBILITY = 'visibility';
    /**
     * @var string
     */
    private const MANGA = 'manga';
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
        $this->container[self::NAME] = $data[self::NAME] ?? null;
        $this->container[self::VISIBILITY] = $data[self::VISIBILITY] ?? null;
        $this->container[self::MANGA] = $data[self::MANGA] ?? null;
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
        self::NAME => null,
        self::VISIBILITY => null,
        self::MANGA => 'uuid',
        self::VERSION => null
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    private static $attributeMap = [
        self::NAME => self::NAME,
        self::VISIBILITY => self::VISIBILITY,
        self::MANGA => self::MANGA,
        self::VERSION => self::VERSION
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    private static $setters = [
        self::NAME => 'setName',
        self::VISIBILITY => 'setVisibility',
        self::MANGA => 'setManga',
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
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getVisibilityAllowableValues(): array
    {
        return [
            self::VISIBILITY__PUBLIC,
            self::VISIBILITY__PRIVATE,
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

        $allowedValues = $this->getVisibilityAllowableValues();
        if (!is_null($this->container[self::VISIBILITY]) && !in_array($this->container[self::VISIBILITY], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'visibility', must be one of '%s'",
                $this->container[self::VISIBILITY],
                implode("', '", $allowedValues)
            );
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
    const VISIBILITY__PUBLIC = 'public';
    const VISIBILITY__PRIVATE = 'private';


    /**
     * Gets name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->container[self::NAME];
    }
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    private static $getters = [
        self::NAME => 'getName',
        self::VISIBILITY => 'getVisibility',
        self::MANGA => 'getManga',
        self::VERSION => 'getVersion'
    ];

    /**
     * Sets name
     *
     * @param string|null $name name
     */
    public function setName(?string $name): self
    {
        $this->container[self::NAME] = $name;

        return $this;
    }

    /**
     * Gets visibility
     *
     * @return string|null
     */
    public function getVisibility(): ?string
    {
        return $this->container[self::VISIBILITY];
    }

    /**
     * Sets visibility
     *
     * @param string|null $visibility visibility
     */
    public function setVisibility(?string $visibility): self
    {
        $allowedValues = $this->getVisibilityAllowableValues();
        if (!is_null($visibility) && !in_array($visibility, $allowedValues, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'visibility', must be one of '%s'",
                    $visibility,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container[self::VISIBILITY] = $visibility;

        return $this;
    }

    /**
     * Gets manga
     *
     * @return string[]|null
     */
    public function getManga(): ?array
    {
        return $this->container[self::MANGA];
    }

    /**
     * Sets manga
     *
     * @param string[]|null $manga manga
     */
    public function setManga(?array $manga): self
    {
        $this->container[self::MANGA] = $manga;

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
            throw new InvalidArgumentException('invalid value for $version when calling CustomListEdit., must be bigger than or equal to 1.');
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


