<?php
/**
 * This file is part of the Cloudinary PHP package.
 *
 * (c) Cloudinary
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cloudinary\Asset;

use Cloudinary\ArrayUtils;
use Cloudinary\FileUtils;
use Cloudinary\JsonUtils;

/**
 * Class AssetDescriptor
 *
 * @api
 */
class AssetDescriptor implements AssetInterface
{
    /**
     * @var int|string $version Asset version, typically set to unix timestamp.
     */
    public $version;
    /**
     * @var string $location Can be directory, URL(including path, excluding filename), etc.
     */
    public $location;
    /**
     * @var string $filename Basename without extension.
     */
    public $filename;
    /**
     * @var string $extension A.K.A format.
     */
    public $extension;

    /**
     * AssetDescriptor constructor.
     *
     * @param string $publicId  The public ID of the asset.
     */
    public function __construct($publicId)
    {
        $this->setPublicId($publicId);
    }

    /**
     * Gets inaccessible class property by name.
     *
     * @param string $name The name of the property.
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        trigger_error('Undefined property: ' . static::class . '::$' . $name, E_USER_NOTICE);

        return null;
    }

    /**
     * Indicates whether the inaccessible class property is set.
     *
     * @param string $key The class property name.
     *
     * @return bool
     */
    public function __isset($key)
    {
        try {
            if (null === $this->__get($key)) {
                return false;
            }
        } catch (\Exception $e) { // Undefined property
            return false;
        }

        return true;
    }

    /**
     * Sets the inaccessible class property.
     *
     * @param string $name  The class property name.
     * @param mixed  $value The class property value.
     */
    public function __set($name, $value)
    {
        $this->setAssetProperty($name, $value);
    }

    /**
     * Sets the public ID of the asset.
     *
     * @param string $publicId The public ID of the asset.
     *
     * @return $this
     */
    public function setPublicId($publicId)
    {
        list($this->location, $this->filename, $this->extension) = FileUtils::splitPathFilenameExtension($publicId);

        return $this;
    }

    /**
     * Gets the public ID of the asset
     *
     * @param bool $noExtension When true, omits file extension.
     *
     * @return string
     */
    public function publicId($noExtension = false)
    {
        return ArrayUtils::implodeFiltered(
            '.',
            [
                ArrayUtils::implodeFiltered('/', [$this->location, $this->filename]),
                $noExtension ? '' : $this->extension,
            ]
        );
    }

    /**
     * Creates a new asset from the provided string (URL).
     *
     * @param string $string The asset string (URL).
     *
     * @return mixed
     */
    public static function fromString($string)
    {
        throw new \BadMethodCallException('Not Implemented');
    }

    /**
     * Creates a new asset from the provided JSON.
     *
     * @param string|array $json The asset json. Can be an array or a JSON string.
     *
     * @return mixed
     */
    public static function fromJson($json)
    {
        $new = new self('');

        $new->importJson($json);

        return $new;
    }

    /**
     * Imports data from the provided string (URL).
     *
     * @param string $string The asset string (URL).
     *
     * @return mixed
     */
    public function importString($string)
    {
        throw new \BadMethodCallException('Not Implemented');
    }


    /**
     * Imports data from the provided JSON.
     *
     * @param string|array $json The asset json. Can be an array or a JSON string.
     *
     * @return AssetDescriptor
     */
    public function importJson($json)
    {
        $json = JsonUtils::decode($json);

        if (! array_key_exists('asset', $json) || ! array_key_exists('filename', $json['asset'])) {
            throw new \InvalidArgumentException('Invalid asset JSON');
        }

        $assetJson = $json['asset'];

        $this->version      = ArrayUtils::get($assetJson, 'version');
        $this->location     = ArrayUtils::get($assetJson, 'location');
        $this->filename     = ArrayUtils::get($assetJson, 'filename');
        $this->extension    = ArrayUtils::get($assetJson, 'extension');

        return $this;
    }

    /**
     * Serializes to string.
     *
     * @return string
     */
    public function __toString()
    {
        return ArrayUtils::implodeUrl(array_values($this->jsonSerialize()));
    }


    /**
     * Serializes to json.
     *
     * @param bool $includeEmptyKeys Whether to include empty keys.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize($includeEmptyKeys = false)
    {
        $dataArr = [
            'version'       => $this->version,
            'location'      => $this->location,
            'filename'      => $this->filename,
            'extension'     => $this->extension,
        ];

        if (! $includeEmptyKeys) {
            $dataArr = array_filter($dataArr);
        }

        return ['asset' => $dataArr];
    }

    /**
     * Sets the property of the asset descriptor.
     *
     * @param string $propertyName  The name of the property.
     * @param mixed  $propertyValue The value of the property.
     *
     * @return $this
     *
     * @internal
     */
    public function setAssetProperty($propertyName, $propertyValue)
    {
        $this->$propertyName = $propertyValue;

        return $this;
    }
}
