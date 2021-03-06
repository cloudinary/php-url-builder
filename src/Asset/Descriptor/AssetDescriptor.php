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
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class AssetDescriptor
 *
 * @api
 */
class AssetDescriptor implements AssetInterface
{
    /**
     * @var string|int|null $version Asset version, typically set to unix timestamp.
     */
    public string|int|null $version = null;
    /**
     * @var string|null $location Can be directory, URL(including path, excluding filename), etc.
     */
    public ?string $location = null;
    /**
     * @var string|null $filename Basename without extension.
     */
    public ?string $filename = null;
    /**
     * @var string|null $extension A.K.A format.
     */
    public ?string $extension = null;

    /**
     * AssetDescriptor constructor.
     *
     * @param string $publicId The public ID of the asset.
     */
    public function __construct(string $publicId)
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
    public function __get(string $name)
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
    public function __isset(string $key): bool
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
    public function __set(string $name, mixed $value): void
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
    public function setPublicId(string $publicId): static
    {
        [$this->location, $this->filename, $this->extension] = FileUtils::splitPathFilenameExtension($publicId);

        return $this;
    }

    /**
     * Gets the public ID of the asset
     *
     * @param bool $noExtension When true, omits file extension.
     *
     * @return string
     */
    public function publicId(bool $noExtension = false): string
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
     * @return AssetDescriptor
     */
    public static function fromString(string $string): static
    {
        throw new \BadMethodCallException('Not Implemented');
    }

    /**
     * Creates a new asset from the provided JSON.
     *
     * @param array|string $json The asset json. Can be an array or a JSON string.
     *
     * @return AssetDescriptor
     */
    public static function fromJson(array|string $json): static
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
    public function importString(string $string): static
    {
        throw new \BadMethodCallException('Not Implemented');
    }


    /**
     * Imports data from the provided JSON.
     *
     * @param array|string $json The asset json. Can be an array or a JSON string.
     *
     * @return AssetDescriptor
     */
    public function importJson(array|string $json): static
    {
        $json = JsonUtils::decode($json);

        if (! array_key_exists('asset', $json) || ! array_key_exists('filename', $json['asset'])) {
            throw new \InvalidArgumentException('Invalid asset JSON');
        }

        $assetJson = $json['asset'];

        $this->version   = ArrayUtils::get($assetJson, 'version');
        $this->location  = ArrayUtils::get($assetJson, 'location');
        $this->filename  = ArrayUtils::get($assetJson, 'filename');
        $this->extension = ArrayUtils::get($assetJson, 'extension');

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
     * @return array
     */
    #[ArrayShape(['asset' => "array"])]
    public function jsonSerialize(bool $includeEmptyKeys = false): array
    {
        $dataArr = [
            'version'   => $this->version,
            'location'  => $this->location,
            'filename'  => $this->filename,
            'extension' => $this->extension,
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
