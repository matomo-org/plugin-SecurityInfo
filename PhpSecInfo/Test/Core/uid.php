<?php

/**
 * Test class for UID
 *
 * @package PhpSecInfo
 * @author Ed Finkler <coj@funkatron.com>
 */

/**
 * require the PhpSecInfo_Test_Core class
 */
require_once(PHPSECINFO_BASE_DIR . '/Test/Test_Core.php');


/**
 * Test class for UID
 *
 * @package PhpSecInfo
 */
class PhpSecInfo_Test_Core_Uid extends PhpSecInfo_Test_Core
{
    /**
     * This should be a <b>unique</b>, human-readable identifier for this test
     *
     * @var string
     */
    public $test_name = "user_id";

    public $recommended_value = "Nonzero";

    /**
     * This test only works under Unix OSes
     *
     * @return boolean
     */
    public function isTestable()
    {
        if ($this->current_value === false) {
            if (!$this->osIsWindows()) {
                $this->setMessageForResult(PHPSECINFO_TEST_RESULT_NOTRUN, 'en', 'Unable to determine the PHP process UID');
            }
            return false;
        }
        return true;
    }


    public function _retrieveCurrentValue()
    {
        $id = $this->getUnixId();
        $this->current_value = $id === false ? false : (int) $id['uid'];
    }

    public function _execTest()
    {
        return $this->current_value === 0 ? PHPSECINFO_TEST_RESULT_WARN : PHPSECINFO_TEST_RESULT_OK;
    }

    /**
     * Set the messages specific to this test
     */
    public function _setMessages()
    {
        parent::_setMessages();

        $this->setMessageForResult(PHPSECINFO_TEST_RESULT_OK, 'en', 'PHP is not running as root (effective UID is not 0). This check does not assess other privileges.');
        $this->setMessageForResult(PHPSECINFO_TEST_RESULT_WARN, 'en', 'PHP is running as root (effective UID 0). Run PHP as a dedicated non-root user.');
        $this->setMessageForResult(PHPSECINFO_TEST_RESULT_NOTRUN, 'en', 'This test will not run on Windows OSes');
    }
}
