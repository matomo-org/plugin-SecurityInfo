<?php

/**
 * Test class for GID
 *
 * @package PhpSecInfo
 * @author Ed Finkler <coj@funkatron.com>
 */

/**
 * require the PhpSecInfo_Test_Core class
 */
require_once(PHPSECINFO_BASE_DIR . '/Test/Test_Core.php');


/**
 * Test class for GID
 *
 * @package PhpSecInfo
 */
class PhpSecInfo_Test_Core_Gid extends PhpSecInfo_Test_Core
{
    /**
     * This should be a <b>unique</b>, human-readable identifier for this test
     *
     * @var string
     */
    public $test_name = "group_id";

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
                $this->setMessageForResult(PHPSECINFO_TEST_RESULT_NOTRUN, 'en', 'Unable to determine the PHP process GID');
            }
            return false;
        }
        return true;
    }

    public function _retrieveCurrentValue()
    {
        $id = $this->getUnixId();
        if ($id === false) {
            $this->current_value = false;
            return;
        }

        $this->current_value = array_key_exists(0, $id['groups']) ? 0 : (int) $id['gid'];
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

        $this->setMessageForResult(PHPSECINFO_TEST_RESULT_OK, 'en', 'PHP does not have group ID 0. This check does not assess other group permissions.');
        $this->setMessageForResult(PHPSECINFO_TEST_RESULT_WARN, 'en', 'PHP has group ID 0 as its effective or supplementary group. Review whether this membership is necessary.');
        $this->setMessageForResult(PHPSECINFO_TEST_RESULT_NOTRUN, 'en', 'This test will not run on Windows OSes');
    }
}
