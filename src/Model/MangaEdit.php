<?php declare(strict_types=1);

namespace Mapsred\MangadexSDK\Model;

use \ArrayAccess;
use \Mapsred\MangadexSDK\ObjectSerializer;
use InvalidArgumentException;
use JsonSerializable;

final class MangaEdit implements ModelInterface, ArrayAccess, JsonSerializable
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
    private static $openAPIModelName = 'MangaEdit';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    private static $openAPITypes = [
        self::TITLE => 'array<string,string>',
        self::ALT_TITLES => '\Mapsred\MangadexSDK\Model\array[]',
        self::DESCRIPTION => 'array<string,string>',
        self::AUTHORS => 'string[]',
        self::ARTISTS => 'string[]',
        self::LINKS => 'array<string,string>',
        self::ORIGINAL_LANGUAGE => 'string',
        self::LAST_VOLUME => 'string',
        self::LAST_CHAPTER => 'string',
        self::PUBLICATION_DEMOGRAPHIC => 'string',
        self::STATUS => 'string',
        self::YEAR => 'int',
        self::CONTENT_RATING => 'string',
        self::MOD_NOTES => 'string',
        self::VERSION => 'int'
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
    private const AUTHORS = 'authors';
    /**
     * @var string
     */
    private const ARTISTS = 'artists';
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
    private const MOD_NOTES = 'mod_notes';
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
        $this->container[self::TITLE] = $data[self::TITLE] ?? null;
        $this->container[self::ALT_TITLES] = $data[self::ALT_TITLES] ?? null;
        $this->container[self::DESCRIPTION] = $data[self::DESCRIPTION] ?? null;
        $this->container[self::AUTHORS] = $data[self::AUTHORS] ?? null;
        $this->container[self::ARTISTS] = $data[self::ARTISTS] ?? null;
        $this->container[self::LINKS] = $data[self::LINKS] ?? null;
        $this->container[self::ORIGINAL_LANGUAGE] = $data[self::ORIGINAL_LANGUAGE] ?? null;
        $this->container[self::LAST_VOLUME] = $data[self::LAST_VOLUME] ?? null;
        $this->container[self::LAST_CHAPTER] = $data[self::LAST_CHAPTER] ?? null;
        $this->container[self::PUBLICATION_DEMOGRAPHIC] = $data[self::PUBLICATION_DEMOGRAPHIC] ?? null;
        $this->container[self::STATUS] = $data[self::STATUS] ?? null;
        $this->container[self::YEAR] = $data[self::YEAR] ?? null;
        $this->container[self::CONTENT_RATING] = $data[self::CONTENT_RATING] ?? null;
        $this->container[self::MOD_NOTES] = $data[self::MOD_NOTES] ?? null;
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
        self::TITLE => null,
        self::ALT_TITLES => null,
        self::DESCRIPTION => null,
        self::AUTHORS => 'uuid',
        self::ARTISTS => 'uuid',
        self::LINKS => null,
        self::ORIGINAL_LANGUAGE => null,
        self::LAST_VOLUME => null,
        self::LAST_CHAPTER => null,
        self::PUBLICATION_DEMOGRAPHIC => null,
        self::STATUS => null,
        self::YEAR => null,
        self::CONTENT_RATING => null,
        self::MOD_NOTES => null,
        self::VERSION => null
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
        self::AUTHORS => self::AUTHORS,
        self::ARTISTS => self::ARTISTS,
        self::LINKS => self::LINKS,
        self::ORIGINAL_LANGUAGE => 'originalLanguage',
        self::LAST_VOLUME => 'lastVolume',
        self::LAST_CHAPTER => 'lastChapter',
        self::PUBLICATION_DEMOGRAPHIC => 'publicationDemographic',
        self::STATUS => self::STATUS,
        self::YEAR => self::YEAR,
        self::CONTENT_RATING => 'contentRating',
        self::MOD_NOTES => 'modNotes',
        self::VERSION => self::VERSION
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
        self::AUTHORS => 'setAuthors',
        self::ARTISTS => 'setArtists',
        self::LINKS => 'setLinks',
        self::ORIGINAL_LANGUAGE => 'setOriginalLanguage',
        self::LAST_VOLUME => 'setLastVolume',
        self::LAST_CHAPTER => 'setLastChapter',
        self::PUBLICATION_DEMOGRAPHIC => 'setPublicationDemographic',
        self::STATUS => 'setStatus',
        self::YEAR => 'setYear',
        self::CONTENT_RATING => 'setContentRating',
        self::MOD_NOTES => 'setModNotes',
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
    public function getPublicationDemographicAllowableValues(): array
    {
        return [
            self::PUBLICATION_DEMOGRAPHIC_SHOUNEN,
            self::PUBLICATION_DEMOGRAPHIC_SHOUJO,
            self::PUBLICATION_DEMOGRAPHIC_JOSEI,
            self::PUBLICATION_DEMOGRAPHIC_SEINEN,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getStatusAllowableValues(): array
    {
        return [
            self::STATUS_ONGOING,
            self::STATUS_COMPLETED,
            self::STATUS_HIATUS,
            self::STATUS_CANCELLED,
        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getContentRatingAllowableValues(): array
    {
        return [
            self::CONTENT_RATING_SAFE,
            self::CONTENT_RATING_SUGGESTIVE,
            self::CONTENT_RATING_EROTICA,
            self::CONTENT_RATING_PORNOGRAPHIC,
        ];
    }

    const PUBLICATION_DEMOGRAPHIC_SHOUNEN = 'shounen';
    const PUBLICATION_DEMOGRAPHIC_SHOUJO = 'shoujo';
    const PUBLICATION_DEMOGRAPHIC_JOSEI = 'josei';
    const PUBLICATION_DEMOGRAPHIC_SEINEN = 'seinen';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_HIATUS = 'hiatus';
    const STATUS_CANCELLED = 'cancelled';
    const CONTENT_RATING_SAFE = 'safe';
    const CONTENT_RATING_SUGGESTIVE = 'suggestive';
    const CONTENT_RATING_EROTICA = 'erotica';
    const CONTENT_RATING_PORNOGRAPHIC = 'pornographic';

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if (!is_null($this->container[self::ORIGINAL_LANGUAGE]) && !preg_match("/^[a-zA-Z\\-]{2,5}$/", $this->container[self::ORIGINAL_LANGUAGE])) {
            $invalidProperties[] = "invalid value for 'original_language', must be conform to the pattern /^[a-zA-Z\\-]{2,5}$/.";
        }

        $allowedValues = $this->getPublicationDemographicAllowableValues();
        if (!is_null($this->container[self::PUBLICATION_DEMOGRAPHIC]) && !in_array($this->container[self::PUBLICATION_DEMOGRAPHIC], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'publication_demographic', must be one of '%s'",
                $this->container[self::PUBLICATION_DEMOGRAPHIC],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getStatusAllowableValues();
        if (!is_null($this->container[self::STATUS]) && !in_array($this->container[self::STATUS], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'status', must be one of '%s'",
                $this->container[self::STATUS],
                implode("', '", $allowedValues)
            );
        }

        if (!is_null($this->container[self::YEAR]) && ($this->container[self::YEAR] > 9999)) {
            $invalidProperties[] = "invalid value for 'year', must be smaller than or equal to 9999.";
        }

        if (!is_null($this->container[self::YEAR]) && ($this->container[self::YEAR] < 1)) {
            $invalidProperties[] = "invalid value for 'year', must be bigger than or equal to 1.";
        }

        $allowedValues = $this->getContentRatingAllowableValues();
        if (!is_null($this->container[self::CONTENT_RATING]) && !in_array($this->container[self::CONTENT_RATING], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'content_rating', must be one of '%s'",
                $this->container[self::CONTENT_RATING],
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
        self::AUTHORS => 'getAuthors',
        self::ARTISTS => 'getArtists',
        self::LINKS => 'getLinks',
        self::ORIGINAL_LANGUAGE => 'getOriginalLanguage',
        self::LAST_VOLUME => 'getLastVolume',
        self::LAST_CHAPTER => 'getLastChapter',
        self::PUBLICATION_DEMOGRAPHIC => 'getPublicationDemographic',
        self::STATUS => 'getStatus',
        self::YEAR => 'getYear',
        self::CONTENT_RATING => 'getContentRating',
        self::MOD_NOTES => 'getModNotes',
        self::VERSION => 'getVersion'
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
     * Gets authors
     *
     * @return string[]|null
     */
    public function getAuthors(): ?array
    {
        return $this->container[self::AUTHORS];
    }

    /**
     * Sets authors
     *
     * @param string[]|null $authors authors
     */
    public function setAuthors(?array $authors): self
    {
        $this->container[self::AUTHORS] = $authors;

        return $this;
    }

    /**
     * Gets artists
     *
     * @return string[]|null
     */
    public function getArtists(): ?array
    {
        return $this->container[self::ARTISTS];
    }

    /**
     * Sets artists
     *
     * @param string[]|null $artists artists
     */
    public function setArtists(?array $artists): self
    {
        $this->container[self::ARTISTS] = $artists;

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

        if (!is_null($original_language) && (!preg_match("/^[a-zA-Z\\-]{2,5}$/", $original_language))) {
            throw new InvalidArgumentException("invalid value for $original_language when calling MangaEdit., must conform to the pattern /^[a-zA-Z\\-]{2,5}$/.");
        }

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
        $allowedValues = $this->getPublicationDemographicAllowableValues();
        if (!is_null($publication_demographic) && !in_array($publication_demographic, $allowedValues, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'publication_demographic', must be one of '%s'",
                    $publication_demographic,
                    implode("', '", $allowedValues)
                )
            );
        }
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
        $allowedValues = $this->getStatusAllowableValues();
        if (!is_null($status) && !in_array($status, $allowedValues, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'status', must be one of '%s'",
                    $status,
                    implode("', '", $allowedValues)
                )
            );
        }
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

        if (!is_null($year) && ($year > 9999)) {
            throw new InvalidArgumentException('invalid value for $year when calling MangaEdit., must be smaller than or equal to 9999.');
        }
        if (!is_null($year) && ($year < 1)) {
            throw new InvalidArgumentException('invalid value for $year when calling MangaEdit., must be bigger than or equal to 1.');
        }

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
        $allowedValues = $this->getContentRatingAllowableValues();
        if (!is_null($content_rating) && !in_array($content_rating, $allowedValues, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'content_rating', must be one of '%s'",
                    $content_rating,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container[self::CONTENT_RATING] = $content_rating;

        return $this;
    }

    /**
     * Gets mod_notes
     *
     * @return string|null
     */
    public function getModNotes(): ?string
    {
        return $this->container[self::MOD_NOTES];
    }

    /**
     * Sets mod_notes
     *
     * @param string|null $mod_notes mod_notes
     */
    public function setModNotes(?string $mod_notes): self
    {
        $this->container[self::MOD_NOTES] = $mod_notes;

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
            throw new InvalidArgumentException('invalid value for $version when calling MangaEdit., must be bigger than or equal to 1.');
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


