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
use Cloudinary\Transformation\Transformation;
use Cloudinary\Transformation\TransformationTrait;

/**
 * Class Media
 *
 * @api
 */
class Media extends BaseMediaAsset
{
    use TransformationTrait;

    /**
     * Gets the transformation.
     *
     * @return Transformation
     */
    public function getTransformation()
    {
        if (! isset($this->transformation)) {
            $this->transformation = new Transformation();
        }

        return $this->transformation;
    }
}
