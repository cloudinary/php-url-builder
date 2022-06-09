<?php
/**
 * This file is part of the Cloudinary PHP package.
 *
 * (c) Cloudinary
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cloudinary\Configuration;

/**
 * Defines the global configuration applied when generating Cloudinary URLs.
 *
 * @property bool $forceVersion                  By default, set to self::DEFAULT_FORCE_VERSION.
 *
 * @api
 */
class UrlConfig extends BaseConfigSection
{
    use UrlConfigTrait;

    /**
     * @internal
     */
    const CONFIG_NAME = 'url';

    /**
     * @internal
     */
    const DEFAULT_DOMAIN_NAME = 'cloudinary.net';

    /**
     * @internal
     */
    const DEFAULT_SUB_DOMAIN = 'media';

    /**
     * @internal
     */
    const DEFAULT_SHARED_DOMAIN = self::DEFAULT_SUB_DOMAIN . '.' . self::DEFAULT_DOMAIN_NAME;

    const PROTOCOL_HTTPS = 'https';

    /**
     * Default value for forcing version.
     */
    const DEFAULT_FORCE_VERSION = true;

    /**
     * Default value for analytics.
     */
    const DEFAULT_ANALYTICS = true;

    // Supported parameters
    const DOMAIN             = 'domain';
    const SHARED_DOMAIN      = 'shared_domain';
    const SIGN_URL           = 'sign_url';
    const LONG_URL_SIGNATURE = 'long_url_signature';
    const FORCE_VERSION      = 'force_version';
    const ANALYTICS          = 'analytics';


    /**
     * The domain name of the CDN distribution to use for building HTTPS URLs. Relevant only for Advanced plan users
     * that have a private CDN distribution.
     *
     * @var string
     *
     * @see https://cloudinary.com/documentation/advanced_url_delivery_options#private_cdns_and_cnames
     */
    public $domain;

    /**
     * The shared domain to use.
     *
     * @var bool
     * @internal
     */
    protected $sharedDomain;

    /**
     * Set to true to create a Cloudinary URL signed with the first 8 characters of a SHA-1 hash.
     *
     * @var bool
     *
     * @see https://cloudinary.com/documentation/advanced_url_delivery_options#generating_delivery_url_signatures
     */
    public $signUrl;

    /**
     * Setting both this and signUrl to true will sign the URL using the first 32 characters of a SHA-256 hash.
     *
     * @var bool
     *
     * @see https://cloudinary.com/documentation/advanced_url_delivery_options#generating_delivery_url_signatures
     */
    public $longUrlSignature;

    /**
     * Set to false to omit default version string for assets in folders in the delivery URL.
     *
     * @var bool
     */
    protected $forceVersion;

    /**
     * Set to "false" to omit analytics data.
     *
     * @var bool
     */
    protected $analytics;

    /**
     * Serialises configuration section to a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString([self::DOMAIN]);
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
        return $this->setConfig($configKey, $configValue);
    }
}
