<?php
/**
 * This file is part of the Cloudinary PHP package.
 *
 * (c) Cloudinary
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cloudinary;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Class Utils
 *
 * @internal
 */
class Utils
{
    const ALGO_SHA1                      = 'sha1';
    const ALGO_SHA256                    = 'sha256';
    const SUPPORTED_SIGNATURE_ALGORITHMS = [self::ALGO_SHA1, self::ALGO_SHA256];
    const SHORT_URL_SIGNATURE_LENGTH     = 8;
    const LONG_URL_SIGNATURE_LENGTH      = 32;


    /**
     * Format a given signature hash into a string that will be used to sign a url
     *
     * @param string $signature The signature to format
     * @param int    $length    Number of characters to use from the start of the signature
     *
     * @return string
     */
    public static function formatSimpleSignature($signature, $length)
    {
        return 's--' . substr($signature, 0, $length) . '--';
    }

    /**
     * Creates a signature for content using specified secret.
     *
     * @param string $content
     * @param string $secret
     * @param bool   $raw
     * @param string $algo
     *
     * @return string
     */
    public static function sign($content, $secret, $raw = null, $algo = self::ALGO_SHA1)
    {
        return hash($algo, $content . $secret, $raw);
    }

    /**
     * Parses URL if applicable, otherwise returns false.
     *
     * @param string|UriInterface $url            The URL to parse.
     * @param array|null          $allowedSchemes Optional array of the allowed schemes.
     *
     * @return false|UriInterface
     */
    public static function tryParseUrl($url, array $allowedSchemes = null)
    {
        if (! $url instanceof UriInterface) {
            if (! is_string($url)) {
                return false;
            }

            $urlParts = parse_url($url);
            if ($urlParts === false || count($urlParts) <= 1) {
                return false;
            }
            $url = Uri::fromParts($urlParts);
        }

        if ($allowedSchemes !== null && ! in_array($url->getScheme(), $allowedSchemes, false)) {
            return false;
        }

        return $url;
    }


    /**
     * Returns current UNIX time in seconds.
     *
     * @return int
     */
    public static function unixTimeNow()
    {
        return time();
    }

    /**
     * Recursively casts all params from array to suggested type
     *
     * @param array $params The array of params
     *
     * @return array
     */
    public static function tryParseValues(array $params)
    {
        return array_map(
            static function ($value) {
                if (is_array($value)) {
                    return static::tryParseValues($value);
                }

                if (is_string($value)) {
                    if ($value === '[]') {
                        return [];
                    }

                    return static::tryParseBoolean($value);
                }

                return $value;
            },
            $params
        );
    }

    /**
     * Parses boolean from string
     *
     * @param $value
     *
     * @return bool
     */
    public static function tryParseBoolean($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        if (ArrayUtils::inArrayI($value, ['true', 'false'])) {
            return stripos($value, 'true') === 0;
        }

        return $value;
    }
}
