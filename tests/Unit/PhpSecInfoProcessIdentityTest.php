<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SecurityInfo\tests\Unit;

use PhpSecInfo_Test_Core_Gid;
use PhpSecInfo_Test_Core_Uid;
use Piwik\Tests\Framework\TestCase\UnitTestCase;

class PhpSecInfoProcessIdentityTest extends UnitTestCase
{
    public function testOnlyRootIdsTriggerProcessIdentityWarnings(): void
    {
        require_once PIWIK_INCLUDE_PATH . '/plugins/SecurityInfo/PhpSecInfo/PhpSecInfo.php';
        require_once PHPSECINFO_BASE_DIR . '/Test/Core/uid.php';
        require_once PHPSECINFO_BASE_DIR . '/Test/Core/gid.php';

        $ok = PHPSECINFO_TEST_RESULT_OK;
        $warn = PHPSECINFO_TEST_RESULT_WARN;
        $notRun = PHPSECINFO_TEST_RESULT_NOTRUN;
        $cases = [
            'Debian www-data' => [33, 33, [33], $ok, $ok],
            'Apache' => [48, 48, [48], $ok, $ok],
            'root user' => [0, 33, [33], $warn, $ok],
            'effective group 0' => [33, 0, [33], $ok, $warn],
            'supplementary group 0' => [1000, 1000, [1000, 0], $ok, $warn],
            'no supplementary groups' => [33, 33, [], $ok, $ok],
            'unavailable identity' => [null, null, [], $notRun, $notRun],
        ];

        foreach ($cases as $name => [$uid, $gid, $groups, $expectedUid, $expectedGid]) {
            $identity = $uid === null ? false : ['uid' => $uid, 'gid' => $gid, 'groups' => array_fill_keys($groups, '')];

            $uidTest = new class ($identity) extends PhpSecInfo_Test_Core_Uid {
                public function __construct(private array|false $identity)
                {
                    parent::__construct();
                }

                public function getUnixId()
                {
                    return $this->identity;
                }
            };
            $gidTest = new class ($identity) extends PhpSecInfo_Test_Core_Gid {
                public function __construct(private array|false $identity)
                {
                    parent::__construct();
                }

                public function getUnixId()
                {
                    return $this->identity;
                }
            };

            foreach ([[$uidTest, $expectedUid], [$gidTest, $expectedGid]] as [$check, $expected]) {
                if ($check->isTestable()) {
                    $check->test();
                }
                self::assertSame($expected, $check->getResult(), $name . ': ' . $check->getTestName());
            }
        }
    }
}
