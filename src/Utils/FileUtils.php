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

/**
 * Class Utils
 *
 * @internal
 */
class FileUtils
{
    /**
     * @var int Maximum number of characters allowed for the file extension.
     */
    const MAX_FILE_EXTENSION_LEN = 5;

    /**
     * Helper function that removes current dir(.) if no dirname found.
     *
     * @param $path
     *
     * @return mixed|null
     */
    public static function dirName($path)
    {
        return ! empty($path) && pathinfo($path, PATHINFO_DIRNAME) !== '.' ? pathinfo($path, PATHINFO_DIRNAME) : null;
    }

    /**
     * Returns filename and extension for the given path.
     *
     * In case the path does not have an extension, null value for the extension is returned.
     *
     * @param string $fullPath The path to split.
     *
     * @return array containing filename and extension.
     */
    public static function splitPathFilenameExtension($fullPath)
    {
        if (empty($fullPath)) {
            return ['', '', ''];
        }

        $path = self::dirName($fullPath);

        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

        if (strlen($extension) <= self::MAX_FILE_EXTENSION_LEN) {
            $filename = pathinfo($fullPath, PATHINFO_FILENAME);
        } else {
            $filename  = pathinfo($fullPath, PATHINFO_BASENAME);
            $extension = null;
        }

        return [$path, $filename, $extension];
    }

    /**
     * Removes file extension from the file path (can be full path or just filename)
     *
     * @param $path
     *
     * @return string
     */
    public static function removeFileExtension($path)
    {
        return implode('/', ArrayUtils::safeFilter([self::dirName($path), pathinfo($path, PATHINFO_FILENAME)]));
    }
}
