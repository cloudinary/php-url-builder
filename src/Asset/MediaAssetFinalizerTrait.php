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
use Cloudinary\StringUtils;
use Cloudinary\Transformation\CommonTransformation;
use Cloudinary\Utils;

/**
 * Trait MediaAssetFinalizerTrait
 *
 * @property AssetDescriptor      $asset
 * @property AuthToken            $authToken
 * @property CommonTransformation $transformation
 */
trait MediaAssetFinalizerTrait
{
    /**
     * Finalizes asset transformation.
     *
     * @param string|CommonTransformation|null $withTransformation Additional transformation
     * @param bool                             $append             Whether to append transformation or set in instead
     *                                                             of the asset transformation
     *
     * @return string
     */
    protected function finalizeTransformation(
        CommonTransformation|string $withTransformation = null,
        bool $append = true
    ): string {
        if ($withTransformation === null) {
            return (string)$this->transformation;
        }

        if (! $append || $this->transformation === null) {
            return (string)$withTransformation;
        }

        $resultingTransformation = clone $this->transformation;

        $resultingTransformation->addTransformation($withTransformation);

        return (string)$resultingTransformation;
    }

    /**
     * Sign both transformation and asset parts of the URL.
     *
     * @return string
     */
    protected function finalizeSimpleSignature(): string
    {
        if (! $this->urlConfig->signUrl || $this->authToken->isEnabled()) {
            return '';
        }

        $toSign    = ArrayUtils::implodeUrl([$this->transformation, $this->asset->publicId()]);
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
}
