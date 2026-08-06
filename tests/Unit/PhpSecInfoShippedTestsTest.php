<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SecurityInfo\tests\Unit;

use Piwik\Tests\Framework\TestCase\UnitTestCase;

class PhpSecInfoShippedTestsTest extends UnitTestCase
{
    public function testShippedTestsAllowlistMatchesShippedTestFiles()
    {
        require_once PIWIK_INCLUDE_PATH . '/plugins/SecurityInfo/PhpSecInfo/PhpSecInfo.php';

        $reflection = new \ReflectionClass('PhpSecInfo');
        $property = $reflection->getProperty('shippedTests');
        $property->setAccessible(true);

        $configuredFiles = array();
        foreach ($property->getValue() as $testGroup => $testFiles) {
            foreach ($testFiles as $testFile) {
                $configuredFiles[] = $testGroup . '/' . $testFile . '.php';
            }
        }

        sort($configuredFiles);

        $testRoot = PIWIK_INCLUDE_PATH . '/plugins/SecurityInfo/PhpSecInfo/Test';
        $actualFiles = glob($testRoot . '/*/*.php');
        $actualFiles = array_map(function ($path) use ($testRoot) {
            return str_replace($testRoot . '/', '', $path);
        }, $actualFiles ?: array());

        sort($actualFiles);

        $this->assertNotEmpty($configuredFiles);
        $this->assertSame($actualFiles, $configuredFiles);
    }
}
