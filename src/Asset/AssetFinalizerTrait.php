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
use Cloudinary\Configuration\UrlConfig;
use Cloudinary\StringUtils;
use Cloudinary\Utils;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Trait AssetFinalizerTrait
 *
 * @property AssetDescriptor $asset
 * @property AuthToken       $authToken
 */
trait AssetFinalizerTrait
{
    /**
     *  Builds the domain for the asset distribution.
     *
     *   1) Customers in shared distribution (e.g. res.cloudinary.com)
     *      If cdn_domain is true uses res-[1-5].cloudinary.com for both http and https.
     *      Setting secure_cdn_subdomain to false disables this for https.
     *   2) Customers with private cdn
     *      If cdn_domain is true uses cloudname-res-[1-5].cloudinary.com for http
     *      If secure_cdn_domain is true uses cloudname-res-[1-5].cloudinary.com for https
     *      (please contact support if you require this)
     *   3) Customers with cname
     *      If cdn_domain is true uses a[1-5].cname for http.
     *      For https, uses the same naming scheme as 1 for shared distribution and as 2 for private distribution.
     *
     * @return string
     */
    protected function finalizeDistribution(): string
    {
        $protocol = UrlConfig::PROTOCOL_HTTPS;

        if (! empty($this->urlConfig->domain)) {
            return "$protocol://{$this->urlConfig->domain}";
        }

        return "$protocol://{$this->cloud->cloudName}.{$this->urlConfig->sharedDomain}";
    }

    /**
     * Finalizes asset source.
     *
     * @return string
     */
    protected function finalizeSource(): string
    {
        $source = $this->asset->publicId(true);

        if (! preg_match('/^https?:\//i', $source)) {
            $source = rawurldecode($source);
        }

        $source = StringUtils::smartEscape($source);

        if (! empty($this->asset->extension)) {
            $source = "{$source}.{$this->asset->extension}";
        }

        return $source;
    }

    /**
     * Finalizes version part of the asset URL.
     *
     * @return string|null
     */
    protected function finalizeVersion(): ?string
    {
        $version = $this->asset->version;

        if (empty($version) && $this->urlConfig->forceVersion
            && ! empty($this->asset->location)
            && ! preg_match("/^https?:\//", $this->asset->publicId())
            && ! preg_match('/^v\d+/', $this->asset->publicId())
        ) {
            $version = '1';
        }

        return $version ? 'v' . $version : null;
    }

    /**
     * Finalizes URL signature.
     *
     * @see https://cloudinary.com/documentation/advanced_url_delivery_options#generating_delivery_url_signatures
     *
     * @return string
     */
    protected function finalizeSimpleSignature(): string
    {
        if (! $this->urlConfig->signUrl || $this->authToken->isEnabled()) {
            return '';
        }

        $toSign = $this->asset->publicId();
        $signature = StringUtils::base64UrlEncode(
            Utils::sign(
                $toSign,
                $this->cloud->apiSecret,
                true,
                $this->getSignatureAlgorithm()
            )
        );

        return Utils::formatSimpleSignature(
            $signature,
            $this->urlConfig->longUrlSignature ? Utils::LONG_URL_SIGNATURE_LENGTH : Utils::SHORT_URL_SIGNATURE_LENGTH
        );
    }

    /**
     * Check if passed signatureAlgorithm is supported otherwise return SHA1.
     *
     * @return string
     */
    protected function getSignatureAlgorithm(): string
    {
        if ($this->urlConfig->longUrlSignature) {
            return Utils::ALGO_SHA256;
        }

        if (ArrayUtils::inArrayI($this->cloud->signatureAlgorithm, Utils::SUPPORTED_SIGNATURE_ALGORITHMS)) {
            return $this->cloud->signatureAlgorithm;
        }

        return Utils::ALGO_SHA1;
    }

    /**
     * Finalizes URL.
     *
     * @param string $urlStr The URL to finalize.
     *
     * @return UriInterface The resulting URL.
     */
    protected function finalizeUrl(string $urlStr): UriInterface
    {
        $urlParts = parse_url($urlStr);

        $urlParts = $this->finalizeUrlWithAuthToken($urlParts);
        $urlParts = $this->finalizeUrlWithAnalytics($urlParts);

        return Uri::fromParts($urlParts);
    }

    /**
     * Finalizes URL signature, when AuthToken is used.
     *
     * @param array $urlParts The URL parts to sign.
     *
     * @return array resulting URL parts
     */
    protected function finalizeUrlWithAuthToken(array $urlParts): array
    {
        if (! $this->urlConfig->signUrl || ! $this->authToken->isEnabled()) {
            return $urlParts;
        }

        $token = $this->authToken->generate($urlParts['path']);

        $urlParts['query'] = ArrayUtils::implodeAssoc(
            ArrayUtils::mergeNonEmpty(
                StringUtils::parseQueryString(ArrayUtils::get($urlParts, 'query')),
                StringUtils::parseQueryString($token)
            ),
            '='
        );

        return $urlParts;
    }

    /**
     * Finalizes URL with analytics data.
     *
     * @param array $urlParts The URL to add analytics to.
     *
     * @return array resulting URL
     */
    protected function finalizeUrlWithAnalytics(array $urlParts): array
    {
        if (! $this->urlConfig->analytics) {
            return $urlParts;
        }

        // Disable analytics for public IDs containing query params.
        if (! empty($urlParts['query']) || StringUtils::contains($this->asset->publicId(), "?")) {
            return $urlParts;
        }

        $urlParts['query'] = ArrayUtils::implodeAssoc(
            ArrayUtils::mergeNonEmpty(
                StringUtils::parseQueryString(ArrayUtils::get($urlParts, 'query')),
                [Analytics::QUERY_KEY, Analytics::sdkAnalyticsSignature()]
            ),
            '='
        );

        return $urlParts;
    }
}
