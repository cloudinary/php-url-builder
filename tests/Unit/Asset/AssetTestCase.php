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

use Cloudinary\ArrayUtils;
use Cloudinary\Asset\File;
use Cloudinary\Asset\Media;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Configuration\UrlConfig;
use Cloudinary\Test\Unit\UnitTestCase;
use Exception;

/**
 * Class AssetTestCase
 */
abstract class AssetTestCase extends UnitTestCase
{
    const ASSET_ID = 'sample';

    const IMG_EXT        = 'png';
    const IMG_EXT_JPG    = 'jpg';
    const IMG_EXT_GIF    = 'gif';
    const IMAGE_NAME     = self::ASSET_ID . '.' . self::IMG_EXT;
    const IMAGE_NAME_GIF = self::ASSET_ID . '.' . self::IMG_EXT_GIF;

    const VID_EXT    = 'mp4';
    const VIDEO_NAME = self::ASSET_ID . '.' . self::VID_EXT;

    const FILE_EXT  = 'bin';
    const FILE_NAME = self::ASSET_ID . '.' . self::FILE_EXT;

    const DOCX_EXT  = 'docx';
    const DOCX_NAME = self::ASSET_ID . '.' . self::DOCX_EXT;

    const ASSET_DISPLAY_NAME = 'test';

    const ASSET_FOLDER    = 'asset_folder';
    const FOLDER          = 'test_folder';
    const NESTED_FOLDER   = 'folder/test';
    const IMAGE_IN_FOLDER = self::FOLDER . '/' . self::IMAGE_NAME;

    const FETCH_IMAGE_URL            = 'https://res.cloudinary.com/demo/image/upload/' . self::IMAGE_NAME;
    const FETCH_IMAGE_URL_WITH_QUERY = 'https://res.cloudinary.com/demo/image/upload/' . self::IMAGE_NAME . '?q=a';

    const PROTOCOL_HTTP  = 'http';
    const PROTOCOL_HTTPS = 'https';

    const TEST_HOSTNAME        = 'hello.com';

    const TEST_ASSET_VERSION        = 1486020273;
    const TEST_ASSET_VERSION_STR    = 'v' . self::TEST_ASSET_VERSION;
    const DEFAULT_ASSET_VERSION     = 1;
    const DEFAULT_ASSET_VERSION_STR = 'v' . self::DEFAULT_ASSET_VERSION;

    public function tearDown(): void
    {
        parent::tearDown();

        Configuration::instance()->init();
    }

    /**
     * @param        $expectedPath
     * @param        $actualUrl
     * @param array  $options
     */
    protected static function assertAssetUrl(
        $expectedPath,
        $actualUrl,
        $options = []
    ) {
        $message    = ArrayUtils::get($options, 'message', '');
        $protocol   = ArrayUtils::get($options, 'protocol', 'https');
        $hostname   = ArrayUtils::get($options, 'hostname', UrlConfig::DEFAULT_SHARED_DOMAIN);
        $cloudName  = ArrayUtils::get($options, 'cloud_name', self::CLOUD_NAME);

        if ($hostname !== UrlConfig::DEFAULT_SHARED_DOMAIN) {
            $hostAndCloud = $hostname;
        } else {
            $hostAndCloud = ArrayUtils::implodeFiltered('.', [$cloudName, $hostname]);
        }

        $expectedUrl = "$protocol://$hostAndCloud/$expectedPath";

        self::assertEquals($expectedUrl, (string)$actualUrl, $message);
    }

    /**
     * @param        $expectedPath
     * @param        $actualUrl
     * @param array  $options
     */
    protected static function assertImageUrl(
        $expectedPath,
        $actualUrl,
        $options = []
    ) {
        self::assertAssetUrl($expectedPath, $actualUrl, $options);
    }

    /**
     * @param        $expectedPath
     * @param        $actualUrl
     * @param null   $message
     */
    protected static function assertVideoUrl(
        $expectedPath,
        $actualUrl,
        $message = null
    ) {
        self::assertAssetUrl($expectedPath, $actualUrl, $message);
    }

    /**
     * @param        $expectedPath
     * @param        $actualUrl
     * @param null   $message
     */
    protected static function assertFileUrl(
        $expectedPath,
        $actualUrl,
        $message = null
    ) {
        self::assertAssetUrl($expectedPath, $actualUrl, $message);
    }

    /**
     * @param callable $function
     */
    protected static function assertErrorThrowing(callable $function)
    {
        $errorsThrown = 0;
        set_error_handler(
            static function () use (&$errorsThrown) {
                $errorsThrown++;

                return true;
            }
        );

        try {
            $function();
        } catch (Exception $e) {
            $errorsThrown++;
        }

        restore_error_handler();
        self::assertEquals(1, $errorsThrown, 'Failed assert that error was thrown');
    }
}
