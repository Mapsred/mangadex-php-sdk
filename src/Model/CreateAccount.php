<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use InvalidArgumentException;
use JsonSerializable;

final class CreateAccount implements ModelInterface, ArrayAccess, JsonSerializable
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
    private static $openAPIModelName = 'CreateAccount';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::USERNAME => 'string',
        self::PASSWORD => 'string',
        self::EMAIL => 'string'
    ];
    /**
     * @var string
     */
    private const USERNAME = 'username';
    /**
     * @var string
     */
    private const PASSWORD = 'password';
    /**
     * @var string
     */
    private const EMAIL = 'email';
    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container[self::USERNAME] = $data[self::USERNAME] ?? null;
        $this->container[self::PASSWORD] = $data[self::PASSWORD] ?? null;
        $this->container[self::EMAIL] = $data[self::EMAIL] ?? null;
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
        self::USERNAME => null,
        self::PASSWORD => null,
        self::EMAIL => self::EMAIL
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    private static $attributeMap = [
        self::USERNAME => self::USERNAME,
        self::PASSWORD => self::PASSWORD,
        self::EMAIL => self::EMAIL
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    private static $setters = [
        self::USERNAME => 'setUsername',
        self::PASSWORD => 'setPassword',
        self::EMAIL => 'setEmail'
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

        if ($this->container[self::USERNAME] === null) {
            $invalidProperties[] = "'username' can't be null";
        }
        if ((mb_strlen($this->container[self::USERNAME]) > 64)) {
            $invalidProperties[] = "invalid value for 'username', the character length must be smaller than or equal to 64.";
        }

        if ((mb_strlen($this->container[self::USERNAME]) < 1)) {
            $invalidProperties[] = "invalid value for 'username', the character length must be bigger than or equal to 1.";
        }

        if ($this->container[self::PASSWORD] === null) {
            $invalidProperties[] = "'password' can't be null";
        }
        if ((mb_strlen($this->container[self::PASSWORD]) > 1024)) {
            $invalidProperties[] = "invalid value for 'password', the character length must be smaller than or equal to 1024.";
        }

        if ((mb_strlen($this->container[self::PASSWORD]) < 8)) {
            $invalidProperties[] = "invalid value for 'password', the character length must be bigger than or equal to 8.";
        }

        if ($this->container[self::EMAIL] === null) {
            $invalidProperties[] = "'email' can't be null";
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
     * Gets username
     */
    public function getUsername(): string
    {
        return $this->container[self::USERNAME];
    }
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    private static $getters = [
        self::USERNAME => 'getUsername',
        self::PASSWORD => 'getPassword',
        self::EMAIL => 'getEmail'
    ];

    /**
     * Sets username
     *
     * @param string $username username
     */
    public function setUsername(string $username): self
    {
        if ((mb_strlen($username) > 64)) {
            throw new InvalidArgumentException('invalid length for $username when calling CreateAccount., must be smaller than or equal to 64.');
        }
        if ((mb_strlen($username) < 1)) {
            throw new InvalidArgumentException('invalid length for $username when calling CreateAccount., must be bigger than or equal to 1.');
        }

        $this->container[self::USERNAME] = $username;

        return $this;
    }

    /**
     * Gets password
     */
    public function getPassword(): string
    {
        return $this->container[self::PASSWORD];
    }

    /**
     * Sets password
     *
     * @param string $password password
     */
    public function setPassword(string $password): self
    {
        if ((mb_strlen($password) > 1024)) {
            throw new InvalidArgumentException('invalid length for $password when calling CreateAccount., must be smaller than or equal to 1024.');
        }
        if ((mb_strlen($password) < 8)) {
            throw new InvalidArgumentException('invalid length for $password when calling CreateAccount., must be bigger than or equal to 8.');
        }

        $this->container[self::PASSWORD] = $password;

        return $this;
    }

    /**
     * Gets email
     */
    public function getEmail(): string
    {
        return $this->container[self::EMAIL];
    }

    /**
     * Sets email
     *
     * @param string $email email
     */
    public function setEmail(string $email): self
    {
        $this->container[self::EMAIL] = $email;

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


