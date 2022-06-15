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

use Cloudinary\Asset\Media;
use Cloudinary\Transformation\AudioCodec;
use Cloudinary\Transformation\Gravity;
use UnexpectedValueException;

/**
 * Class MediaTest
 */
final class MediaTest extends AssetTestCase
{
    /**
     * @var Media $media Test image that is commonly reused by tests
     */
    protected $media;

    public function setUp(): void
    {
        parent::setUp();

        $this->media = new Media(self::IMAGE_NAME);
    }

    public function testSimpleMedia()
    {
        self::assertImageUrl(
            self::IMAGE_NAME,
            $this->media
        );

        self::assertImageUrl(
            self::IMAGE_NAME,
            $this->media->toUrl()
        );
    }
}
