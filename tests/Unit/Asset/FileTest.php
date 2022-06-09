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

use Cloudinary\Asset\File;
use InvalidArgumentException;
use Monolog\Logger as Monolog;
use ReflectionException;

/**
 * Class FileTest
 */
final class FileTest extends AssetTestCase
{
    public function testFile()
    {
        $f = new File(self::FILE_NAME);

        self::assertFileUrl(
            self::FILE_NAME,
            (string)$f
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testInvalidFileImportJson()
    {
        $file = new File(self::FILE_NAME, self::TEST_LOGGING);

        $message = null;
        $expectedLogMessage = 'Error importing JSON';
        $expectedExceptionMessage = 'JsonException :';
        try {
            $file->importJson('{NOT_A_JSON}');
        } catch (InvalidArgumentException $e) {
            $message = $e->getMessage();
        }

        self::assertStringStartsWith($expectedExceptionMessage, $message);
        self::assertObjectLoggedMessage($file, $expectedLogMessage, Monolog::CRITICAL);
    }
}
