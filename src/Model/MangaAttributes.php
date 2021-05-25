<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use InvalidArgumentException;
use JsonSerializable;

final class MangaAttributes implements ModelInterface, ArrayAccess, JsonSerializable
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
    private static $openAPIModelName = 'MangaAttributes';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::TITLE => 'array<string,string>',
        self::ALT_TITLES => '\Mapsred\MangadexSDK\Model\array[]',
        self::DESCRIPTION => 'array<string,string>',
        self::IS_LOCKED => 'bool',
        self::LINKS => 'array<string,string>',
        self::ORIGINAL_LANGUAGE => 'string',
        self::LAST_VOLUME => 'string',
        self::LAST_CHAPTER => 'string',
        self::PUBLICATION_DEMOGRAPHIC => 'string',
        self::STATUS => 'string',
        self::YEAR => 'int',
        self::CONTENT_RATING => 'string',
        self::TAGS => '\Mapsred\MangadexSDK\Model\Tag[]',
        self::VERSION => 'int',
        self::CREATED_AT => 'string',
        self::UPDATED_AT => 'string'
    ];
    /**
     * @var string
     */
    private const TITLE = 'title';
    /**
     * @var string
     */
    private const ALT_TITLES = 'alt_titles';
    /**
     * @var string
     */
    private const DESCRIPTION = 'description';
    /**
     * @var string
     */
    private const IS_LOCKED = 'is_locked';
    /**
     * @var string
     */
    private const LINKS = 'links';
    /**
     * @var string
     */
    private const ORIGINAL_LANGUAGE = 'original_language';
    /**
     * @var string
     */
    private const LAST_VOLUME = 'last_volume';
    /**
     * @var string
     */
    private const LAST_CHAPTER = 'last_chapter';
    /**
     * @var string
     */
    private const PUBLICATION_DEMOGRAPHIC = 'publication_demographic';
    /**
     * @var string
     */
    private const STATUS = 'status';
    /**
     * @var string
     */
    private const YEAR = 'year';
    /**
     * @var string
     */
    private const CONTENT_RATING = 'content_rating';
    /**
     * @var string
     */
    private const TAGS = 'tags';
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
        $this->container[self::TITLE] = $data[self::TITLE] ?? null;
        $this->container[self::ALT_TITLES] = $data[self::ALT_TITLES] ?? null;
        $this->container[self::DESCRIPTION] = $data[self::DESCRIPTION] ?? null;
        $this->container[self::IS_LOCKED] = $data[self::IS_LOCKED] ?? null;
        $this->container[self::LINKS] = $data[self::LINKS] ?? null;
        $this->container[self::ORIGINAL_LANGUAGE] = $data[self::ORIGINAL_LANGUAGE] ?? null;
        $this->container[self::LAST_VOLUME] = $data[self::LAST_VOLUME] ?? null;
        $this->container[self::LAST_CHAPTER] = $data[self::LAST_CHAPTER] ?? null;
        $this->container[self::PUBLICATION_DEMOGRAPHIC] = $data[self::PUBLICATION_DEMOGRAPHIC] ?? null;
        $this->container[self::STATUS] = $data[self::STATUS] ?? null;
        $this->container[self::YEAR] = $data[self::YEAR] ?? null;
        $this->container[self::CONTENT_RATING] = $data[self::CONTENT_RATING] ?? null;
        $this->container[self::TAGS] = $data[self::TAGS] ?? null;
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
        self::TITLE => null,
        self::ALT_TITLES => null,
        self::DESCRIPTION => null,
        self::IS_LOCKED => null,
        self::LINKS => null,
        self::ORIGINAL_LANGUAGE => null,
        self::LAST_VOLUME => null,
        self::LAST_CHAPTER => null,
        self::PUBLICATION_DEMOGRAPHIC => null,
        self::STATUS => null,
        self::YEAR => null,
        self::CONTENT_RATING => null,
        self::TAGS => null,
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
        self::TITLE => self::TITLE,
        self::ALT_TITLES => 'altTitles',
        self::DESCRIPTION => self::DESCRIPTION,
        self::IS_LOCKED => 'isLocked',
        self::LINKS => self::LINKS,
        self::ORIGINAL_LANGUAGE => 'originalLanguage',
        self::LAST_VOLUME => 'lastVolume',
        self::LAST_CHAPTER => 'lastChapter',
        self::PUBLICATION_DEMOGRAPHIC => 'publicationDemographic',
        self::STATUS => self::STATUS,
        self::YEAR => self::YEAR,
        self::CONTENT_RATING => 'contentRating',
        self::TAGS => self::TAGS,
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
        self::TITLE => 'setTitle',
        self::ALT_TITLES => 'setAltTitles',
        self::DESCRIPTION => 'setDescription',
        self::IS_LOCKED => 'setIsLocked',
        self::LINKS => 'setLinks',
        self::ORIGINAL_LANGUAGE => 'setOriginalLanguage',
        self::LAST_VOLUME => 'setLastVolume',
        self::LAST_CHAPTER => 'setLastChapter',
        self::PUBLICATION_DEMOGRAPHIC => 'setPublicationDemographic',
        self::STATUS => 'setStatus',
        self::YEAR => 'setYear',
        self::CONTENT_RATING => 'setContentRating',
        self::TAGS => 'setTags',
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
     * Gets title
     *
     * @return array<string,string>|null
     */
    public function getTitle(): ?array
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
        self::ALT_TITLES => 'getAltTitles',
        self::DESCRIPTION => 'getDescription',
        self::IS_LOCKED => 'getIsLocked',
        self::LINKS => 'getLinks',
        self::ORIGINAL_LANGUAGE => 'getOriginalLanguage',
        self::LAST_VOLUME => 'getLastVolume',
        self::LAST_CHAPTER => 'getLastChapter',
        self::PUBLICATION_DEMOGRAPHIC => 'getPublicationDemographic',
        self::STATUS => 'getStatus',
        self::YEAR => 'getYear',
        self::CONTENT_RATING => 'getContentRating',
        self::TAGS => 'getTags',
        self::VERSION => 'getVersion',
        self::CREATED_AT => 'getCreatedAt',
        self::UPDATED_AT => 'getUpdatedAt'
    ];

    /**
     * Sets title
     *
     * @param array<string,string>|null $title title
     */
    public function setTitle(?array $title): self
    {
        $this->container[self::TITLE] = $title;

        return $this;
    }

    /**
     * Gets alt_titles
     *
     * @return \Mapsred\MangadexSDK\Model\array[]|null
     */
    public function getAltTitles(): ?array
    {
        return $this->container[self::ALT_TITLES];
    }

    /**
     * Sets alt_titles
     *
     * @param \Mapsred\MangadexSDK\Model\array[]|null $alt_titles alt_titles
     */
    public function setAltTitles(?array $alt_titles): self
    {
        $this->container[self::ALT_TITLES] = $alt_titles;

        return $this;
    }

    /**
     * Gets description
     *
     * @return array<string,string>|null
     */
    public function getDescription(): ?array
    {
        return $this->container[self::DESCRIPTION];
    }

    /**
     * Sets description
     *
     * @param array<string,string>|null $description description
     */
    public function setDescription(?array $description): self
    {
        $this->container[self::DESCRIPTION] = $description;

        return $this;
    }

    /**
     * Gets is_locked
     *
     * @return bool|null
     */
    public function getIsLocked(): ?bool
    {
        return $this->container[self::IS_LOCKED];
    }

    /**
     * Sets is_locked
     *
     * @param bool|null $is_locked is_locked
     */
    public function setIsLocked(?bool $is_locked): self
    {
        $this->container[self::IS_LOCKED] = $is_locked;

        return $this;
    }

    /**
     * Gets links
     *
     * @return array<string,string>|null
     */
    public function getLinks(): ?array
    {
        return $this->container[self::LINKS];
    }

    /**
     * Sets links
     *
     * @param array<string,string>|null $links links
     */
    public function setLinks(?array $links): self
    {
        $this->container[self::LINKS] = $links;

        return $this;
    }

    /**
     * Gets original_language
     *
     * @return string|null
     */
    public function getOriginalLanguage(): ?string
    {
        return $this->container[self::ORIGINAL_LANGUAGE];
    }

    /**
     * Sets original_language
     *
     * @param string|null $original_language original_language
     */
    public function setOriginalLanguage(?string $original_language): self
    {
        $this->container[self::ORIGINAL_LANGUAGE] = $original_language;

        return $this;
    }

    /**
     * Gets last_volume
     *
     * @return string|null
     */
    public function getLastVolume(): ?string
    {
        return $this->container[self::LAST_VOLUME];
    }

    /**
     * Sets last_volume
     *
     * @param string|null $last_volume last_volume
     */
    public function setLastVolume(?string $last_volume): self
    {
        $this->container[self::LAST_VOLUME] = $last_volume;

        return $this;
    }

    /**
     * Gets last_chapter
     *
     * @return string|null
     */
    public function getLastChapter(): ?string
    {
        return $this->container[self::LAST_CHAPTER];
    }

    /**
     * Sets last_chapter
     *
     * @param string|null $last_chapter last_chapter
     */
    public function setLastChapter(?string $last_chapter): self
    {
        $this->container[self::LAST_CHAPTER] = $last_chapter;

        return $this;
    }

    /**
     * Gets publication_demographic
     *
     * @return string|null
     */
    public function getPublicationDemographic(): ?string
    {
        return $this->container[self::PUBLICATION_DEMOGRAPHIC];
    }

    /**
     * Sets publication_demographic
     *
     * @param string|null $publication_demographic publication_demographic
     */
    public function setPublicationDemographic(?string $publication_demographic): self
    {
        $this->container[self::PUBLICATION_DEMOGRAPHIC] = $publication_demographic;

        return $this;
    }

    /**
     * Gets status
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->container[self::STATUS];
    }

    /**
     * Sets status
     *
     * @param string|null $status status
     */
    public function setStatus(?string $status): self
    {
        $this->container[self::STATUS] = $status;

        return $this;
    }

    /**
     * Gets year
     *
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->container[self::YEAR];
    }

    /**
     * Sets year
     *
     * @param int|null $year Year of release
     */
    public function setYear(?int $year): self
    {
        $this->container[self::YEAR] = $year;

        return $this;
    }

    /**
     * Gets content_rating
     *
     * @return string|null
     */
    public function getContentRating(): ?string
    {
        return $this->container[self::CONTENT_RATING];
    }

    /**
     * Sets content_rating
     *
     * @param string|null $content_rating content_rating
     */
    public function setContentRating(?string $content_rating): self
    {
        $this->container[self::CONTENT_RATING] = $content_rating;

        return $this;
    }

    /**
     * Gets tags
     *
     * @return Tag[]|null
     */
    public function getTags(): ?array
    {
        return $this->container[self::TAGS];
    }

    /**
     * Sets tags
     *
     * @param Tag[]|null $tags tags
     */
    public function setTags(?array $tags): self
    {
        $this->container[self::TAGS] = $tags;

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
            throw new InvalidArgumentException('invalid value for $version when calling MangaAttributes., must be bigger than or equal to 1.');
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


