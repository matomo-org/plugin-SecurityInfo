<?php
/**
 * Test class for Piwik Application
 *
 * @package PhpSecInfo
 * @author Piwik
 */
use Piwik\UpdateCheck;
use Piwik\Version;

/**
 * require the PhpSecInfo_Test_Application class
 */
require_once(PHPSECINFO_BASE_DIR . '/Test/Test_Application.php');

/**
 * Test class for Piwik application
 *
 * Checks Piwik version
 *
 * @package PhpSecInfo
 * @author Piwik
 */
class PhpSecInfo_Test_Application_Piwik extends PhpSecInfo_Test_Application
{
    var $test_name = "Matomo";

    var $recommended_value = null;

    function _retrieveCurrentValue()
    {
        $this->current_value = Version::VERSION;

        $this->recommended_value = \Piwik\Option::get(UpdateCheck::LATEST_VERSION);
    }

    function _execTest()
    {
        if (version_compare($this->current_value, '0.5') < 0) {
            return PHPSECINFO_TEST_RESULT_WARN;
        }

        if (empty($this->recommended_value)) {
            return PHPSECINFO_TEST_RESULT_ERROR;
        }

        if (version_compare($this->current_value, $this->recommended_value) >= 0) {
            return PHPSECINFO_TEST_RESULT_OK;
        }

        return PHPSECINFO_TEST_RESULT_NOTICE;
    }

    function _setMessages()
    {
        parent::_setMessages();

        $piwikVersion = $this->current_value;
        $recommendedValue = $this->recommended_value;

        if(\Piwik\Common::getRequestVar('tests_hide_piwik_version', 0, 'int') == 1) {
            $piwikVersion = $recommendedValue = 'Screenshot testing - Version is hidden';
        }

        $this->setMessageForResult(PHPSECINFO_TEST_RESULT_OK, 'en', "You are running Matomo " . $piwikVersion . " (the latest version).");
        $this->setMessageForResult(PHPSECINFO_TEST_RESULT_NOTICE, 'en', "You are running Matomo " . $piwikVersion . ".  The latest version of Matomo is " . $recommendedValue . ".");
        $this->setMessageForResult(PHPSECINFO_TEST_RESULT_WARN, 'en', "You are running Matomo " . $piwikVersion . " which is no longer supported by the Matomo developers. We recommend running the latest (stable) version of Matomo which includes numerous enhancements, bug fixes, and security fixes.");
        $this->setMessageForResult(PHPSECINFO_TEST_RESULT_ERROR, 'en', "Unable to determine the latest version of Matomo available.");
    }
}
