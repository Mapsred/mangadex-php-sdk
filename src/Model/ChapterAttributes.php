<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use InvalidArgumentException;
use JsonSerializable;

final class ChapterAttributes implements ModelInterface, ArrayAccess, JsonSerializable
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
    private static $openAPIModelName = 'ChapterAttributes';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::TITLE => 'string',
        self::VOLUME => 'string',
        self::CHAPTER => 'string',
        self::TRANSLATED_LANGUAGE => 'string',
        self::HASH => 'string',
        self::DATA => 'string[]',
        self::DATA_SAVER => 'string[]',
        self::UPLOADER => 'string',
        self::VERSION => 'int',
        self::CREATED_AT => 'string',
        self::UPDATED_AT => 'string',
        self::PUBLISH_AT => 'string'
    ];
    /**
     * @var string
     */
    private const TITLE = 'title';
    /**
     * @var string
     */
    private const VOLUME = 'volume';
    /**
     * @var string
     */
    private const CHAPTER = 'chapter';
    /**
     * @var string
     */
    private const TRANSLATED_LANGUAGE = 'translated_language';
    /**
     * @var string
     */
    private const HASH = 'hash';
    /**
     * @var string
     */
    private const DATA = 'data';
    /**
     * @var string
     */
    private const DATA_SAVER = 'data_saver';
    /**
     * @var string
     */
    private const UPLOADER = 'uploader';
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
     * @var string
     */
    private const PUBLISH_AT = 'publish_at';
    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container[self::TITLE] = $data[self::TITLE] ?? null;
        $this->container[self::VOLUME] = $data[self::VOLUME] ?? null;
        $this->container[self::CHAPTER] = $data[self::CHAPTER] ?? null;
        $this->container[self::TRANSLATED_LANGUAGE] = $data[self::TRANSLATED_LANGUAGE] ?? null;
        $this->container[self::HASH] = $data[self::HASH] ?? null;
        $this->container[self::DATA] = $data[self::DATA] ?? null;
        $this->container[self::DATA_SAVER] = $data[self::DATA_SAVER] ?? null;
        $this->container[self::UPLOADER] = $data[self::UPLOADER] ?? null;
        $this->container[self::VERSION] = $data[self::VERSION] ?? null;
        $this->container[self::CREATED_AT] = $data[self::CREATED_AT] ?? null;
        $this->container[self::UPDATED_AT] = $data[self::UPDATED_AT] ?? null;
        $this->container[self::PUBLISH_AT] = $data[self::PUBLISH_AT] ?? null;
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
        self::TITLE => null,
        self::VOLUME => null,
        self::CHAPTER => null,
        self::TRANSLATED_LANGUAGE => null,
        self::HASH => null,
        self::DATA => null,
        self::DATA_SAVER => null,
        self::UPLOADER => 'uuid',
        self::VERSION => null,
        self::CREATED_AT => null,
        self::UPDATED_AT => null,
        self::PUBLISH_AT => null
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    private static $attributeMap = [
        self::TITLE => self::TITLE,
        self::VOLUME => self::VOLUME,
        self::CHAPTER => self::CHAPTER,
        self::TRANSLATED_LANGUAGE => 'translatedLanguage',
        self::HASH => self::HASH,
        self::DATA => self::DATA,
        self::DATA_SAVER => 'dataSaver',
        self::UPLOADER => self::UPLOADER,
        self::VERSION => self::VERSION,
        self::CREATED_AT => 'createdAt',
        self::UPDATED_AT => 'updatedAt',
        self::PUBLISH_AT => 'publishAt'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    private static $setters = [
        self::TITLE => 'setTitle',
        self::VOLUME => 'setVolume',
        self::CHAPTER => 'setChapter',
        self::TRANSLATED_LANGUAGE => 'setTranslatedLanguage',
        self::HASH => 'setHash',
        self::DATA => 'setData',
        self::DATA_SAVER => 'setDataSaver',
        self::UPLOADER => 'setUploader',
        self::VERSION => 'setVersion',
        self::CREATED_AT => 'setCreatedAt',
        self::UPDATED_AT => 'setUpdatedAt',
        self::PUBLISH_AT => 'setPublishAt'
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

        if (!is_null($this->container[self::TITLE]) && (mb_strlen($this->container[self::TITLE]) > 255)) {
            $invalidProperties[] = "invalid value for 'title', the character length must be smaller than or equal to 255.";
        }

        if (!is_null($this->container[self::CHAPTER]) && (mb_strlen($this->container[self::CHAPTER]) > 8)) {
            $invalidProperties[] = "invalid value for 'chapter', the character length must be smaller than or equal to 8.";
        }

        if (!is_null($this->container[self::TRANSLATED_LANGUAGE]) && !preg_match("/^[a-zA-Z\\-]{2,5}$/", $this->container[self::TRANSLATED_LANGUAGE])) {
            $invalidProperties[] = "invalid value for 'translated_language', must be conform to the pattern /^[a-zA-Z\\-]{2,5}$/.";
        }

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
     * Gets title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->container[self::TITLE];
    }
    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    private static $getters = [
        self::TITLE => 'getTitle',
        self::VOLUME => 'getVolume',
        self::CHAPTER => 'getChapter',
        self::TRANSLATED_LANGUAGE => 'getTranslatedLanguage',
        self::HASH => 'getHash',
        self::DATA => 'getData',
        self::DATA_SAVER => 'getDataSaver',
        self::UPLOADER => 'getUploader',
        self::VERSION => 'getVersion',
        self::CREATED_AT => 'getCreatedAt',
        self::UPDATED_AT => 'getUpdatedAt',
        self::PUBLISH_AT => 'getPublishAt'
    ];

    /**
     * Sets title
     *
     * @param string|null $title title
     */
    public function setTitle(?string $title): self
    {
        if (!is_null($title) && (mb_strlen($title) > 255)) {
            throw new InvalidArgumentException('invalid length for $title when calling ChapterAttributes., must be smaller than or equal to 255.');
        }

        $this->container[self::TITLE] = $title;

        return $this;
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
     * Sets volume
     *
     * @param string|null $volume volume
     */
    public function setVolume(?string $volume): self
    {
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
        if (!is_null($chapter) && (mb_strlen($chapter) > 8)) {
            throw new InvalidArgumentException('invalid length for $chapter when calling ChapterAttributes., must be smaller than or equal to 8.');
        }

        $this->container[self::CHAPTER] = $chapter;

        return $this;
    }

    /**
     * Gets translated_language
     *
     * @return string|null
     */
    public function getTranslatedLanguage(): ?string
    {
        return $this->container[self::TRANSLATED_LANGUAGE];
    }

    /**
     * Sets translated_language
     *
     * @param string|null $translated_language translated_language
     */
    public function setTranslatedLanguage(?string $translated_language): self
    {

        if (!is_null($translated_language) && (!preg_match("/^[a-zA-Z\\-]{2,5}$/", $translated_language))) {
            throw new InvalidArgumentException("invalid value for $translated_language when calling ChapterAttributes., must conform to the pattern /^[a-zA-Z\\-]{2,5}$/.");
        }

        $this->container[self::TRANSLATED_LANGUAGE] = $translated_language;

        return $this;
    }

    /**
     * Gets hash
     *
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->container[self::HASH];
    }

    /**
     * Sets hash
     *
     * @param string|null $hash hash
     */
    public function setHash(?string $hash): self
    {
        $this->container[self::HASH] = $hash;

        return $this;
    }

    /**
     * Gets data
     *
     * @return string[]|null
     */
    public function getData(): ?array
    {
        return $this->container[self::DATA];
    }

    /**
     * Sets data
     *
     * @param string[]|null $data data
     */
    public function setData(?array $data): self
    {
        $this->container[self::DATA] = $data;

        return $this;
    }

    /**
     * Gets data_saver
     *
     * @return string[]|null
     */
    public function getDataSaver(): ?array
    {
        return $this->container[self::DATA_SAVER];
    }

    /**
     * Sets data_saver
     *
     * @param string[]|null $data_saver data_saver
     */
    public function setDataSaver(?array $data_saver): self
    {
        $this->container[self::DATA_SAVER] = $data_saver;

        return $this;
    }

    /**
     * Gets uploader
     *
     * @return string|null
     */
    public function getUploader(): ?string
    {
        return $this->container[self::UPLOADER];
    }

    /**
     * Sets uploader
     *
     * @param string|null $uploader uploader
     */
    public function setUploader(?string $uploader): self
    {
        $this->container[self::UPLOADER] = $uploader;

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
            throw new InvalidArgumentException('invalid value for $version when calling ChapterAttributes., must be bigger than or equal to 1.');
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
     * Gets publish_at
     *
     * @return string|null
     */
    public function getPublishAt(): ?string
    {
        return $this->container[self::PUBLISH_AT];
    }

    /**
     * Sets publish_at
     *
     * @param string|null $publish_at publish_at
     */
    public function setPublishAt(?string $publish_at): self
    {
        $this->container[self::PUBLISH_AT] = $publish_at;

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


