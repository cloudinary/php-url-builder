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
 * Trait UrlConfigTrait
 *
 * @api
 */
trait UrlConfigTrait
{
    /**
     * The domain name of the CDN distribution to use for building URLs.
     * Relevant only for Advanced plan users that have a private CDN distribution.
     *
     * @param string $domain The domain for the URLs.
     *
     * @return $this
     *
     * @see https://cloudinary.com/documentation/advanced_url_delivery_options#private_cdns_and_cnames
     *
     */
    public function domain(string $domain): static
    {
        return $this->setUrlConfig(UrlConfig::DOMAIN, $domain);
    }

    /**
     * The domain of the shared host.
     *
     * @param string $sharedDomain The shared domain for the URLs.
     *
     * @return $this
     *
     * @see https://cloudinary.com/documentation/advanced_url_delivery_options#private_cdns_and_cnames
     *
     */
    public function sharedDomain(string $sharedDomain): static
    {
        return $this->setUrlConfig(UrlConfig::SHARED_DOMAIN, $sharedDomain);
    }

    /**
     * Set to true to create a signed Cloudinary URL.
     *
     * @param bool|null $signUrl
     *
     * @return $this
     */
    public function signUrl(?bool $signUrl = true): static
    {
        return $this->setUrlConfig(UrlConfig::SIGN_URL, $signUrl);
    }

    /**
     * Setting both this and signUrl to true will sign the URL using the first 32 characters of a SHA-256 hash.
     *
     * @param bool $longUrlSignature
     *
     * @return $this
     *
     * @see https://cloudinary.com/documentation/advanced_url_delivery_options#generating_delivery_url_signatures
     */
    public function longUrlSignature(?bool $longUrlSignature = true): static
    {
        return $this->setUrlConfig(UrlConfig::LONG_URL_SIGNATURE, $longUrlSignature);
    }

    /**
     * Set to false to omit default version string for assets in folders in the delivery URL.
     *
     * @param bool $forceVersion
     *
     * @return $this
     */
    public function forceVersion(?bool $forceVersion = true): static
    {
        return $this->setUrlConfig(UrlConfig::FORCE_VERSION, $forceVersion);
    }

    /**
     * Set to false to omit analytics.
     *
     * @param bool $analytics Whether to include analytics.
     *
     * @return $this
     */
    public function analytics(?bool $analytics = true): static
    {
        return $this->setUrlConfig(UrlConfig::ANALYTICS, $analytics);
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
    abstract public function setUrlConfig(string $configKey, mixed $configValue): static;
}
