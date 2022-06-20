<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SecurityInfo;

/**
 */
class SecurityInfo extends \Piwik\Plugin
{
    /**
     * @see \Piwik\Plugin::registerEvents
     */
    public function registerEvents()
    {
        return array(
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
        );
    }

    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = "plugins/SecurityInfo/stylesheets/securityinfo.less";
    }

    public function getClientSideTranslationKeys(&$keys)
    {
        $keys[] = 'SecurityInfo_SecurityInformation';
        $keys[] = 'SecurityInfo_PluginDescription';
        $keys[] = 'SecurityInfo_Test';
        $keys[] = 'SecurityInfo_Result';
    }
}
