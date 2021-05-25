<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use InvalidArgumentException;
use JsonSerializable;

final class ScanlationGroupAttributes implements ModelInterface, ArrayAccess, JsonSerializable
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
    private static $openAPIModelName = 'ScanlationGroupAttributes';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::NAME => 'string',
        self::LEADER => '\Mapsred\MangadexSDK\Model\User',
        self::VERSION => 'int',
        self::CREATED_AT => 'string',
        self::UPDATED_AT => 'string'
    ];
    /**
     * @var string
     */
    private const NAME = 'name';
    /**
     * @var string
     */
    private const LEADER = 'leader';
    /**
     * @var string
     */
    private const VERSION = 'version';
    /**
     * @var string
     */
    private const CREATED_AT = 'created_at';
    /**
     * @var string
     */
    private const UPDATED_AT = 'updated_at';
    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container[self::NAME] = $data[self::NAME] ?? null;
        $this->container[self::LEADER] = $data[self::LEADER] ?? null;
        $this->container[self::VERSION] = $data[self::VERSION] ?? null;
        $this->container[self::CREATED_AT] = $data[self::CREATED_AT] ?? null;
        $this->container[self::UPDATED_AT] = $data[self::UPDATED_AT] ?? null;
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
        self::LEADER => null,
        self::VERSION => null,
        self::CREATED_AT => null,
        self::UPDATED_AT => null
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    private static $attributeMap = [
        self::NAME => self::NAME,
        self::LEADER => self::LEADER,
        self::VERSION => self::VERSION,
        self::CREATED_AT => 'createdAt',
        self::UPDATED_AT => 'updatedAt'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    private static $setters = [
        self::NAME => 'setName',
        self::LEADER => 'setLeader',
        self::VERSION => 'setVersion',
        self::CREATED_AT => 'setCreatedAt',
        self::UPDATED_AT => 'setUpdatedAt'
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

        if (!is_null($this->container[self::VERSION]) && ($this->container[self::VERSION] < 1)) {
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
        self::LEADER => 'getLeader',
        self::VERSION => 'getVersion',
        self::CREATED_AT => 'getCreatedAt',
        self::UPDATED_AT => 'getUpdatedAt'
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
     * Gets leader
     *
     * @return User|null
     */
    public function getLeader(): ?User
    {
        return $this->container[self::LEADER];
    }

    /**
     * Sets leader
     *
     * @param User|null $leader leader
     */
    public function setLeader(?User $leader): self
    {
        $this->container[self::LEADER] = $leader;

        return $this;
    }

    /**
     * Gets version
     *
     * @return int|null
     */
    public function getVersion(): ?int
    {
        return $this->container[self::VERSION];
    }

    /**
     * Sets version
     *
     * @param int|null $version version
     */
    public function setVersion(?int $version): self
    {

        if (!is_null($version) && ($version < 1)) {
            throw new InvalidArgumentException('invalid value for $version when calling ScanlationGroupAttributes., must be bigger than or equal to 1.');
        }

        $this->container[self::VERSION] = $version;

        return $this;
    }

    /**
     * Gets created_at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->container[self::CREATED_AT];
    }

    /**
     * Sets created_at
     *
     * @param string|null $created_at created_at
     */
    public function setCreatedAt(?string $created_at): self
    {
        $this->container[self::CREATED_AT] = $created_at;

        return $this;
    }

    /**
     * Gets updated_at
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->container[self::UPDATED_AT];
    }

    /**
     * Sets updated_at
     *
     * @param string|null $updated_at updated_at
     */
    public function setUpdatedAt(?string $updated_at): self
    {
        $this->container[self::UPDATED_AT] = $updated_at;

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


