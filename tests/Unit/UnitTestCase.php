<?php
/**
 * This file is part of the Cloudinary PHP package.
 *
 * (c) Cloudinary
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cloudinary\Test\Unit;

use Cloudinary\Configuration\Configuration;
use Cloudinary\Configuration\ConfigUtils;
use Cloudinary\Test\CloudinaryAssetTestCase;

/**
 * Class UnitTestCase
 *
 * Base class for all Unit tests.
 */
abstract class UnitTestCase extends CloudinaryAssetTestCase
{
    const CLOUD_NAME = 'test123';
    const API_KEY    = 'key';
    const API_SECRET = 'secret';

    const DOMAIN = 'secure-dist';

    const TEST_LOGGING = ['logging' => ['test' => ['level' => 'debug']]];

    protected $cloudinaryUrl;

    private $cldUrlEnvBackup;

    public function setUp(): void
    {
        parent::setUp();

        $this->cldUrlEnvBackup = getenv(Configuration::CLOUDINARY_URL_ENV_VAR);

        $this->cloudinaryUrl = 'cloudinary://' . $this::API_KEY . ':' . $this::API_SECRET . '@' . $this::CLOUD_NAME;

        putenv(Configuration::CLOUDINARY_URL_ENV_VAR . '=' . $this->cloudinaryUrl);

        $config = ConfigUtils::parseCloudinaryUrl(getenv(Configuration::CLOUDINARY_URL_ENV_VAR));
        $config = array_merge($config, self::TEST_LOGGING);
        Configuration::instance()->init($config);

        Configuration::instance()->url->analytics(false); // disable analytics for all unit tests
    }

    public function tearDown(): void
    {
        parent::tearDown();

        putenv(Configuration::CLOUDINARY_URL_ENV_VAR . '=' . $this->cldUrlEnvBackup);
    }

    protected static function clearEnvironment(): void
    {
        putenv(Configuration::CLOUDINARY_URL_ENV_VAR); // unset CLOUDINARY_URL

        Configuration::instance()->init();
    }
}
