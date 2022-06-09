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

use Cloudinary\Transformation\CommonTransformation;
use Cloudinary\Transformation\ImageTransformation;
use Cloudinary\Transformation\ImageTransformationInterface;
use Cloudinary\Transformation\ImageTransformationTrait;

/**
 * Class Image
 *
 * @api
 */
class Image extends BaseMediaAsset implements ImageTransformationInterface
{
    use ImageTransformationTrait;

    /**
     * Gets the transformation.
     *
     * @return CommonTransformation
     */
    public function getTransformation()
    {
        if (! isset($this->transformation)) {
            $this->transformation = new ImageTransformation();
        }

        return $this->transformation;
    }
}
