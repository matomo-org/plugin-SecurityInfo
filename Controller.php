<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SecurityInfo;

use PhpSecInfo;
use Piwik\Piwik;
use Piwik\View;

/**
 */
class Controller extends \Piwik\Plugin\ControllerAdmin
{
    function index()
    {
        Piwik::checkUserHasSuperUserAccess();

        require_once(dirname(__FILE__) . '/PhpSecInfo/PhpSecInfo.php');

        // instantiate the class
        $psi = new PhpSecInfo();

        // load and run all tests
        $psi->loadAndRun();

        // grab the results as a multidimensional array
        $results = $psi->getResultsAsArray();

        // suppress results
        unset($results['test_results']['Core']['memory_limit']);
        unset($results['test_results']['Core']['post_max_size']);
        unset($results['test_results']['Core']['upload_max_filesize']);

        $view = new View('@SecurityInfo/index');
        $this->setBasicVariablesView($view);
        $view->results = $results;
        return $view->render();
    }
}
