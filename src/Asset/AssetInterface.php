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

use JsonSerializable;

/**
 * Interface AssetInterface
 */
interface AssetInterface extends JsonSerializable
{
    /**
     * Creates a new asset from the provided string (URL).
     *
     * @param string $string The asset string (URL).
     *
     * @return static
     */
    public static function fromString(string $string): static;

    /**
     * Creates a new asset from the provided JSON.
     *
     * @param array|string $json The asset json. Can be an array or a JSON string.
     *
     * @return static
     */
    public static function fromJson(array|string $json): static;

    /**
     * Imports data from the provided string (URL).
     *
     * @param string $string The asset string (URL).
     *
     * @return static
     */
    public function importString(string $string): static;

    /**
     * Imports data from the provided JSON.
     *
     * @param array|string $json The asset json. Can be an array or a JSON string.
     *
     * @return static
     */
    public function importJson(array|string $json): static;

    /**
     * Serializes to string.
     *
     * @return string
     */
    public function __toString();

    /**
     * Serializes to json.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): mixed;
}
