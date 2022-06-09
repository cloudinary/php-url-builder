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
use Cloudinary\Configuration\AssetConfigTrait;
use Cloudinary\Configuration\CloudConfig;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Configuration\LoggingConfig;
use Cloudinary\Configuration\UrlConfig;
use Cloudinary\JsonUtils;
use Cloudinary\Log\LoggerTrait;

/**
 * Class BaseAsset
 *
 * @api
 */
abstract class BaseAsset implements AssetInterface
{
    use AssetDescriptorTrait;
    use AssetConfigTrait;

    use LoggerTrait;
    use AssetFinalizerTrait;

    // Asset Configuration

    /**
     * @var CloudConfig $cloud The configuration of the cloud.
     */
    public $cloud;

    /**
     * @var UrlConfig $urlConfig The configuration of the URL.
     */
    public $urlConfig;

    /**
     * @var AssetDescriptor $asset The asset descriptor.
     */
    public $asset;

    /**
     * @var AuthToken $authToken The authentication token.
     */
    public $authToken;

    /**
     * BaseAsset constructor.
     *
     * @param       $source
     * @param mixed $configuration
     */
    public function __construct($source, $configuration = null)
    {
        if ($source instanceof $this) { // copy constructor
            $this->deepCopy($source);

            return;
        }

        if ($source instanceof self) { // here comes conversion
            // TODO: discuss configuration transfer
            $this->asset = clone $source->asset;

            $this->cloud     = clone $source->cloud;
            $this->urlConfig = clone $source->urlConfig;
            $this->authToken = clone $source->authToken;
            $this->logging   = clone $source->logging;

            return;
        }

        if ($source instanceof AssetDescriptor) {
            $this->asset = clone $source;
        } else {
            $this->asset = new AssetDescriptor($source);
        }

        if ($configuration === null) {
            $configuration = Configuration::instance(); // get global instance
        }

        $this->configuration($configuration);

        $this->authToken = new AuthToken($configuration);
    }

    /**
     * Performs a deep copy from another asset.
     *
     * @param static $other The source asset.
     *
     * @internal
     */
    public function deepCopy($other)
    {
        $this->cloud     = clone $other->cloud;
        $this->urlConfig = clone $other->urlConfig;
        $this->asset     = clone $other->asset;
        $this->authToken = clone $other->authToken;
        $this->logging   = clone $other->logging;
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
        //TODO: Parse URL and populate the asset
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
        //TODO: implement me
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
        //TODO: implement me
    }


    /**
     * Imports data from the provided JSON.
     *
     * @param string|array $json The asset json. Can be an array or a JSON string.
     *
     * @return mixed
     */
    public function importJson($json)
    {
        try {
            $json = JsonUtils::decode($json);

            $this->cloud     = CloudConfig::fromJson($json, true);
            $this->urlConfig = UrlConfig::fromJson($json, false);
            $this->asset     = AssetDescriptor::fromJson($json);
            $this->authToken = AuthToken::fromJson($json);
            $this->logging   = LoggingConfig::fromJson($json);
        } catch (\InvalidArgumentException $e) {
            $this->getLogger()->critical(
                'Error importing JSON',
                [
                    'exception' => $e->getMessage(),
                    'json'      => json_encode($json),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                ]
            );
            throw $e;
        }

        return $this;
    }

    /**
     * Sets the asset configuration.
     *
     * @param array|Configuration $configuration The configuration source.
     *
     * @return static
     */
    public function configuration($configuration)
    {
        $tempConfiguration = new Configuration($configuration, true); // TODO: improve performance here
        $this->cloud       = $tempConfiguration->cloud;
        $this->urlConfig   = $tempConfiguration->url;
        $this->logging     = $tempConfiguration->logging;

        return $this;
    }

    /**
     * Imports (merges) the asset configuration.
     *
     * @param array|Configuration $configuration The configuration source.
     *
     * @return static
     */
    public function importConfiguration($configuration)
    {
        $this->cloud->importJson($configuration->cloud->jsonSerialize());
        $this->urlConfig->importJson($configuration->url->jsonSerialize());
        $this->logging->importJson($configuration->logging->jsonSerialize());

        return $this;
    }


    /**
     * Returns the public ID of the asset.
     *
     * @param bool $omitExtension Indicates whether to exclude the file extension.
     *
     * @return string
     */
    public function getPublicId($omitExtension = false)
    {
        return $this->asset->publicId($omitExtension);
    }

    /**
     * Sets the public ID of the asset.
     *
     * @param string $publicId The public ID.
     *
     * @return static
     */
    public function setPublicId($publicId)
    {
        $this->asset->setPublicId($publicId);

        return $this;
    }

    /**
     * Internal pre-serialization helper.
     *
     * @return array
     *
     * @internal
     */
    protected function prepareUrlParts()
    {
        return [
            'distribution' => $this->finalizeDistribution(),
            'signature'    => $this->finalizeSimpleSignature(),
            'version'      => $this->finalizeVersion(),
            'source'       => $this->finalizeSource(),
        ];
    }

    /**
     * Serializes to URL string.
     *
     * @return string
     */
    public function toUrl()
    {
        return $this->finalizeUrl(ArrayUtils::implodeUrl($this->prepareUrlParts()));
    }


    /**
     * Serializes to string.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return (string)$this->toUrl();
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * Serialized to JSON.
     *
     * @param bool $includeEmptyKeys     Whether to include empty keys.
     * @param bool $includeEmptySections Whether to include empty sections.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize($includeEmptyKeys = false, $includeEmptySections = false)
    {
        $json = $this->asset->jsonSerialize($includeEmptyKeys);

        foreach ([$this->cloud, $this->urlConfig] as $confSection) {
            $section = $confSection->jsonSerialize(false, $includeEmptyKeys, $includeEmptySections);
            if (! $includeEmptySections && empty(array_values($section)[0])) {
                continue;
            }
            $json = array_merge($json, $section);
        }

        return $json;
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
        try {
            $this->asset->setAssetProperty($propertyName, $propertyValue);
        } catch (\UnexpectedValueException $e) {
            $this->getLogger()->critical(
                $e->getMessage(),
                [
                    'propertyName'  => $propertyName,
                    'propertyValue' => $propertyValue,
                    'file'          => $e->getFile(),
                    'line'          => $e->getLine(),
                ]
            );
            throw $e;
        }

        return $this;
    }

    /**
     * Sets the Account configuration key with the specified value.
     *
     * @param string $configKey   The configuration key.
     * @param mixed  $configValue THe configuration value.
     *
     * @return $this
     *
     * @internal
     */
    public function setCloudConfig($configKey, $configValue)
    {
        $this->cloud->setCloudConfig($configKey, $configValue);

        return $this;
    }

    /**
     * Sets the Url configuration key with the specified value.
     *
     * @param string $configKey   The configuration key.
     * @param mixed  $configValue THe configuration value.
     *
     * @return $this
     *
     * @internal
     */
    public function setUrlConfig($configKey, $configValue)
    {
        $this->urlConfig->setUrlConfig($configKey, $configValue);

        return $this;
    }
}
