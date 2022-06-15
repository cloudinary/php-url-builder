<?php
/**
 * This file is part of the Cloudinary PHP package.
 *
 * (c) Cloudinary
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cloudinary\Test\Unit\Asset;

use Cloudinary\Asset\Analytics;
use Cloudinary\Asset\AuthToken;
use Cloudinary\Asset\Image;
use Cloudinary\Configuration\Configuration;

/**
 * Class DistributionTest
 */
final class DistributionTest extends AssetTestCase
{
    const EXPECTED_SHARD = 2;

    /**
     * @var Image $image Test image that is commonly reused by tests
     */
    protected $image;

    public function setUp(): void
    {
        parent::setUp();

        $this->image = new Image(self::IMAGE_NAME);
    }

    /**
     * Should allow overwriting secure distribution if secure=TRUE
     */
    public function testDomainOverwrite()
    {
        $host = 'something.else.com';

        self::assertImageUrl(
            self::IMAGE_NAME,
            $this->image->domain($host),
            ['protocol' => self::PROTOCOL_HTTPS, 'hostname' => $host]
        );
    }

    /**
     * Should take secure distribution from config if secure=true
     */
    public function testSecureDistributionHostFromConfig()
    {
        $host = 'config.secure.distribution.com';

        Configuration::instance()->url->domain($host);

        self::assertImageUrl(
            self::IMAGE_NAME,
            (new Image(self::IMAGE_NAME)),
            ['protocol' => self::PROTOCOL_HTTPS, 'hostname' => $host]
        );
    }

    /**
     * Should not add cloud_name if private_cdn and secure non akamai secure_distribution
     */
    public function testSecureNonAkamai()
    {
        $host = 'something.cloudfront.net';

        self::assertImageUrl(
            self::IMAGE_NAME,
            $this->image->domain($host),
            [
                'protocol'   => self::PROTOCOL_HTTPS,
                'hostname'   => $host,
                'cloud_name' => null,
            ]
        );
    }

    public function testSignature()
    {
        self::assertImageUrl('s--MDvxhRxa--/' . self::IMAGE_NAME, $this->image->signUrl());
    }

    /**
     * Should support long url signature
     */
    public function testLongSignature()
    {
        $this->image->urlConfig->signUrl          = true;
        $this->image->urlConfig->longUrlSignature = true;

        self::assertImageUrl('s--RVsT3IpYGITMIc0RjCpde9T9Uujc2c1X--/' . self::IMAGE_NAME, $this->image);
    }

    public function testForceVersion()
    {
        self::assertImageUrl(
            self::DEFAULT_ASSET_VERSION_STR . '/' . self::IMAGE_IN_FOLDER,
            new Image(self::IMAGE_IN_FOLDER)
        );

        self::assertImageUrl(
            self::IMAGE_IN_FOLDER,
            (new Image(self::IMAGE_IN_FOLDER))->forceVersion(false)
        );
    }

    public function testAnalytics()
    {
        $config = new Configuration(Configuration::instance());

        $config->url->analytics();

        self::assertStringContainsString(
            '?' . Analytics::QUERY_KEY . '=',
            (string)new Image(self::ASSET_ID, $config)
        );
    }

    public function testNoAnalyticsPublicIDWithQuery()
    {
        $config = new Configuration(Configuration::instance());

        $config->url->analytics();

        self::assertStringNotContainsString(
            '?' . Analytics::QUERY_KEY . '=',
            (string)new Image(self::FETCH_IMAGE_URL_WITH_QUERY, $config)
        );
    }

    public function testNoAnalyticsWithAuthToken()
    {
        $config = new Configuration(Configuration::instance());

        $config->authToken->key      = AuthTokenTestCase::AUTH_TOKEN_KEY;
        $config->authToken->duration = AuthTokenTestCase::DURATION;

        $config->url->signUrl()->analytics();

        self::assertStringNotContainsString(
            '?' . Analytics::QUERY_KEY . '=',
            (string)new Image(self::ASSET_ID, $config)
        );

        self::assertStringContainsString(
            '__cld_token__',
            (string)new Image(self::ASSET_ID, $config)
        );
    }
}
