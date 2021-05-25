<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use InvalidArgumentException;
use JsonSerializable;

final class CheckResponse implements ModelInterface, ArrayAccess, JsonSerializable
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
    private static $openAPIModelName = 'CheckResponse';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::OK => 'string',
        self::IS_AUTHENTICATED => 'bool',
        self::ROLES => 'string[]',
        self::PERMISSIONS => 'string[]'
    ];
    /**
     * @var string
     */
    private const OK = 'ok';
    /**
     * @var string
     */
    private const IS_AUTHENTICATED = 'is_authenticated';
    /**
     * @var string
     */
    private const ROLES = 'roles';
    /**
     * @var string
     */
    private const PERMISSIONS = 'permissions';
    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container[self::OK] = $data[self::OK] ?? null;
        $this->container[self::IS_AUTHENTICATED] = $data[self::IS_AUTHENTICATED] ?? null;
        $this->container[self::ROLES] = $data[self::ROLES] ?? null;
        $this->container[self::PERMISSIONS] = $data[self::PERMISSIONS] ?? null;
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
        self::OK => null,
        self::IS_AUTHENTICATED => null,
        self::ROLES => null,
        self::PERMISSIONS => null
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    private static $attributeMap = [
        self::OK => self::OK,
        self::IS_AUTHENTICATED => 'isAuthenticated',
        self::ROLES => self::ROLES,
        self::PERMISSIONS => self::PERMISSIONS
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    private static $setters = [
        self::OK => 'setOk',
        self::IS_AUTHENTICATED => 'setIsAuthenticated',
        self::ROLES => 'setRoles',
        self::PERMISSIONS => 'setPermissions'
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
    public function getOkAllowableValues(): array
    {
        return [
            self::OK_OK,
            self::OK_ERROR,
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

        $allowedValues = $this->getOkAllowableValues();
        if (!is_null($this->container[self::OK]) && !in_array($this->container[self::OK], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'ok', must be one of '%s'",
                $this->container[self::OK],
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
    const OK_OK = self::OK;
    const OK_ERROR = 'error';


    /**
     * Gets ok
     *
     * @return string|null
     */
    public function getOk(): ?string
    {
        return $this->container[self::OK];
    }
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    private static $getters = [
        self::OK => 'getOk',
        self::IS_AUTHENTICATED => 'getIsAuthenticated',
        self::ROLES => 'getRoles',
        self::PERMISSIONS => 'getPermissions'
    ];

    /**
     * Sets ok
     *
     * @param string|null $ok ok
     */
    public function setOk(?string $ok): self
    {
        $allowedValues = $this->getOkAllowableValues();
        if (!is_null($ok) && !in_array($ok, $allowedValues, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'ok', must be one of '%s'",
                    $ok,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container[self::OK] = $ok;

        return $this;
    }

    /**
     * Gets is_authenticated
     *
     * @return bool|null
     */
    public function getIsAuthenticated(): ?bool
    {
        return $this->container[self::IS_AUTHENTICATED];
    }

    /**
     * Sets is_authenticated
     *
     * @param bool|null $is_authenticated is_authenticated
     */
    public function setIsAuthenticated(?bool $is_authenticated): self
    {
        $this->container[self::IS_AUTHENTICATED] = $is_authenticated;

        return $this;
    }

    /**
     * Gets roles
     *
     * @return string[]|null
     */
    public function getRoles(): ?array
    {
        return $this->container[self::ROLES];
    }

    /**
     * Sets roles
     *
     * @param string[]|null $roles roles
     */
    public function setRoles(?array $roles): self
    {
        $this->container[self::ROLES] = $roles;

        return $this;
    }

    /**
     * Gets permissions
     *
     * @return string[]|null
     */
    public function getPermissions(): ?array
    {
        return $this->container[self::PERMISSIONS];
    }

    /**
     * Sets permissions
     *
     * @param string[]|null $permissions permissions
     */
    public function setPermissions(?array $permissions): self
    {
        $this->container[self::PERMISSIONS] = $permissions;

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


