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

use Cloudinary\Asset\File;
use Cloudinary\Asset\Image;
use Cloudinary\Asset\Media;
use Cloudinary\Asset\Video;
use Cloudinary\Configuration\Configuration;

/**
 * Defines the Cloudinary instance.
 *
 * @api
 */
class Cloudinary
{
    /**
     * The current version of the SDK.
     *
     * @var string VERSION
     */
    const VERSION = '0.1.0-beta';

    /**
     * Defines the Cloudinary cloud details and other global configuration options.
     *
     * @var Configuration $configuration
     */
    public $configuration;

    /**
     * Cloudinary constructor.
     *
     * @param Configuration|string|array|null $config The Configuration source.
     */
    public function __construct($config = null)
    {
        $this->configuration = new Configuration($config);
        $this->configuration->validate();
    }

    /**
     * Creates a new Image instance using the current configuration instance.
     *
     * @param string $publicId The public ID of the image.
     *
     * @return Image
     */
    public function image($publicId)
    {
        return $this->createWithConfiguration($publicId, Image::class);
    }

    /**
     * Creates a new Media instance using the current configuration instance.
     *
     * @param string $publicId The public ID of the media.
     *
     * @return Image
     */
    public function media($publicId)
    {
        return $this->createWithConfiguration($publicId, Media::class);
    }

    /**
     * Creates a new Video instance using the current configuration instance.
     *
     * @param string|mixed $publicId The public ID of the video.
     *
     * @return Video
     */
    public function video($publicId)
    {
        return $this->createWithConfiguration($publicId, Video::class);
    }

    /**
     * Creates a new Raw instance using the current configuration instance.
     *
     * @param string|mixed $publicId The public ID of the file.
     *
     * @return File
     */
    public function raw($publicId)
    {
        return $this->createWithConfiguration($publicId, File::class);
    }

    /**
     * Creates a new object and imports current instance configuration.
     *
     * @param mixed  $publicId  The public Id or the object.
     * @param string $className The class name of the object to create.
     * @param mixed  ...$args   Additional constructor arguments.
     *
     * @return mixed
     *
     * @internal
     */
    protected function createWithConfiguration($publicId, $className, ...$args)
    {
        $instance = ClassUtils::forceInstance($publicId, $className, null, $this->configuration, ...$args);
        // this covers the case when an instance of the asset is provided and the line above is a no op.
        $instance->importConfiguration($this->configuration);

        return $instance;
    }
}
