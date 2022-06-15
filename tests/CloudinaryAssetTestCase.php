<?php
/**
 * This file is part of the Cloudinary PHP package.
 *
 * (c) Cloudinary
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cloudinary\Test;

use Monolog\Handler\TestHandler;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;

/**
 * Class CloudinaryTestCase
 *
 * Base class for all tests.
 */
abstract class CloudinaryAssetTestCase extends TestCase
{
    /**
     * Helper for invoking non-public class method
     *
     * @param mixed  $class      Classname or object (instance of the class) that contains the method.
     * @param string $methodName Name of the method, or the method FQN in the form 'Foo::bar' if $class argument missing
     * @param mixed  ...$args    The method arguments
     *
     * @return mixed
     */
    public static function invokeNonPublicMethod($class, $methodName, ...$args)
    {
        $classInstance = is_string($class) ? null : $class;

        try {
            $method = new ReflectionMethod($class, $methodName);
        } catch (ReflectionException $e) {
            // oops
            self::fail((string)$e);

            // we actually never get here
            return null;
        }

        $method->setAccessible(true);

        return $method->invoke($classInstance, ...$args);
    }

    /**
     * Reports an error if the $haystack array does not contain the instance of $className.
     *
     * @param string $className Name of the class to find an instance of
     * @param array  $haystack  The array to search through
     */
    public static function assertContainsInstancesOf($className, array $haystack)
    {
        $instanceFound = false;
        foreach ($haystack as $object) {
            if ($object instanceof $className) {
                $instanceFound = true;
            }
        }
        self::assertTrue($instanceFound, 'The $haystack array does not contain an instance of ' . $className);
    }

    /**
     * Asserts that string representations of the objects are equal.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertStrEquals($expected, $actual, $message = '')
    {
        self::assertEquals((string)$expected, (string)$actual, $message);
    }

    /**
     * Asserts that a given object logged a message of a certain level
     *
     * @param object     $obj     The object that should have logged a message
     * @param string     $message The message that was logged
     * @param string|int $level   Logging level value or name
     *
     * @throws ReflectionException
     */
    protected static function assertObjectLoggedMessage($obj, $message, $level)
    {
        $reflectionMethod = new ReflectionMethod(get_class($obj), 'getLogger');
        $reflectionMethod->setAccessible(true);
        $logger = $reflectionMethod->invoke($obj);
        /** @var TestHandler $testHandler */
        $testHandler = $logger->getTestHandler();

        self::assertInstanceOf(TestHandler::class, $testHandler);
        self::assertTrue(
            $testHandler->hasRecordThatContains($message, $level),
            sprintf('Object %s did not log the message or logged it with a different level', get_class($obj))
        );
    }
}
