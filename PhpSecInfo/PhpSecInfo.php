<?php

/**
 * Main class file
 *
 * @package PhpSecInfo
 * @author Ed Finkler <coj@funkatron.com>
 */

/**
 * The default language setting if none is set/retrievable
 *
 */
define('PHPSECINFO_LANG_DEFAULT', 'en');

/**
 * a general version string to differentiate releases
 *
 */
define('PHPSECINFO_VERSION', '0.2.2');

/**
 * a YYYYMMDD date string to indicate "build" date
 *
 */
define('PHPSECINFO_BUILD', '20080723');

/**
 * Homepage for phpsecinfo project
 *
 */
define('PHPSECINFO_URL', 'http://phpsecinfo.com');

/**
 * The base folder where views are stored.  Include trailing slash
 *
 */
define('PHPSECINFO_VIEW_DIR_DEFAULT', 'View/');


/**
 * The default format, used to load the proper view.
 */
define('PHPSECINFO_FORMAT_DEFAULT', 'Html');


/**
 * The base directory, used to resolve requires and includes
 */
define('PHPSECINFO_BASE_DIR', dirname(__FILE__));

/**
 * This is the main class for the phpsecinfo system.  It's responsible for
 * dynamically loading tests, running those tests, and generating the results
 * output
 *
 * Example:
 * <code>
 * <?php require_once(PHPSECINFO_BASE_DIR.'/PhpSecInfo.php'); ?>
 * <?php phpsecinfo(); ?>
 * </code>
 *
 * If you want to capture the output, or just grab the test results and display them
 * in your own way, you'll need to do slightly more work.
 *
 * Example:
 * <code>
 * require_once(PHPSECINFO_BASE_DIR.'/PhpSecInfo.php');
 * // instantiate the class
 * $psi = new PhpSecInfo();
 *
 * // load and run all tests
 * $psi->loadAndRun();
 *
 * // grab the results as a multidimensional array
 * $results = $psi->getResultsAsArray();
 * echo "<pre>"; echo print_r($results, true); echo "</pre>";
 *
 * // grab the standard results output as a string
 * $html = $psi->getOutput();
 *
 * // send it to the browser
 * echo $html;
 * </code>
 *
 *
 * The procedural function "phpsecinfo" is defined below this class.
 * @see phpsecinfo()
 *
 * @author Ed Finkler <coj@funkatron.com>
 *
 * see CHANGELOG for changes
 *
 */
class PhpSecInfo
{
    /**
     * An array of tests to run
     *
     * @var array PhpSecInfo_Test
     */
    public $tests_to_run = array();


    /**
     * An array of results.  Each result is an associative array:
     * <code>
     * $result['result'] = PHPSECINFO_TEST_RESULT_NOTICE;
     * $result['message'] = "a string describing the test results and what they mean";
     * </code>
     *
     * @var array
     */
    public $test_results = array();


    /**
     * An array of tests that were not run
     *
     * <code>
     * $result['result'] = PHPSECINFO_TEST_RESULT_NOTRUN;
     * $result['message'] = "a string explaining why the test was not run";
     * </code>
     *
     * @var array
     */
    public $tests_not_run = array();


    /**
     * The language code used.  Defaults to PHPSECINFO_LANG_DEFAULT, which
     * is 'en'
     *
     * @var string
     * @see PHPSECINFO_LANG_DEFAULT
     */
    public $language = PHPSECINFO_LANG_DEFAULT;


    /**
     * An array of integers recording the number of test results in each category.  Categories can include
     * some or all of the PHPSECINFO_TEST_* constants.  Constants are the keys, # of results are the values.
     *
     * @var array
     */
    public $result_counts = array();


    /**
     * The number of tests that have been run
     *
     * @var integer
     */
    public $num_tests_run = 0;


    /**
     * The base directory for phpsecinfo. Set within the constructor. Paths are resolved from this.
     * @var string
     */
    public $_base_dir;


    /**
     * The directory PHPSecInfo will look for views.  It defaults to the value
     * in PHPSECINFO_VIEW_DIR_DEFAULT, but can be changed with the setViewDirectory()
     * method.
     *
     * @var string
     */
    public $_view_directory;


    /**
     * The output format, used to load the proper view
     *
     * @var string
     **/
    public $_format;

    /**
     * Constructor
     *
     * @param null|array $opts
     * @return PhpSecInfo
     */
    public function __construct($opts = null)
    {

        $this->_base_dir = dirname(__FILE__);

        if ($opts) {
            if (isset($opts['view_directory'])) {
                $this->setViewDirectory($opts['view_directory']);
            } else {
                $this->setViewDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . PHPSECINFO_VIEW_DIR_DEFAULT);
            }

            if (isset($opts['format'])) {
                $this->setFormat($opts['format']);
            } else {
                if (!strcasecmp(PHP_SAPI, 'cli')) {
                    $this->setFormat('Cli');
                } else {
                    $this->setFormat(PHPSECINFO_FORMAT_DEFAULT);
                }
            }
        } else { /* Use defaults */
            $this->setViewDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . PHPSECINFO_VIEW_DIR_DEFAULT);
            if (!strcasecmp(PHP_SAPI, 'cli')) {
                $this->setFormat('Cli');
            } else {
                $this->setFormat(PHPSECINFO_FORMAT_DEFAULT);
            }
        }
    }


    /**
     * The tests shipped by this plugin, grouped by their Test/ subdirectory.
     *
     * loadTests() only ever includes files named in this list. The directory is
     * deliberately NOT scanned, new test files should be added here exclusively
     *
     * @var array<string, string[]>
     */
    private static $shippedTests = array(
        'Application' => array('php', 'piwik'),
        'CGI'         => array('force_redirect'),
        'Core'        => array(
            'allow_url_fopen',
            'allow_url_include',
            'display_errors',
            'expose_php',
            'file_uploads',
            'gid',
            'magic_quotes_gpc',
            'memory_limit',
            'open_basedir',
            'post_max_size',
            'register_globals',
            'uid',
            'upload_max_filesize',
            'upload_tmp_dir',
        ),
        'Curl'        => array('file_support'),
        'Session'     => array('save_path', 'use_trans_sid'),
    );

    /**
     * Includes the test classes shipped by this plugin and builds the array of
     * classnames for the tests that will be run.
     *
     * Only the files enumerated in self::$shippedTests are loaded; the Test/
     * directories are never listed, so no file that happens to be present on disk
     * is included unless this plugin ships it under that exact name.
     */
    public function loadTests()
    {
        $testRoot = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Test';

        $classNames = array();

        foreach (self::$shippedTests as $testDir => $testFiles) {
            foreach ($testFiles as $testFile) {
                $path = $testRoot . DIRECTORY_SEPARATOR . $testDir . DIRECTORY_SEPARATOR . $testFile . '.php';

                if (!is_file($path)) {
                    continue;
                }

                include_once $path;
                $classNames[] = "PhpSecInfo_Test_" . $testDir . "_" . $testFile;
            }
        }

        $this->tests_to_run = $classNames;
    }


    /**
     * This runs the tests in the tests_to_run array and
     * places returned data in the following arrays/scalars:
     * - $this->test_results
     * - $this->result_counts
     * - $this->num_tests_run
     * - $this->tests_not_run;
     */
    public function runTests()
    {
        // initialize a bunch of arrays
        $this->test_results = array();
        $this->result_counts = array();
        $this->result_counts[PHPSECINFO_TEST_RESULT_NOTRUN] = 0;
        $this->num_tests_run = 0;

        foreach ($this->tests_to_run as $testClass) {

            /**
             * @var $test PhpSecInfo_Test
             */
            $test = new $testClass();

            if ($test->isTestable()) {
                $test->test();
                $rs = array('result'            => $test->getResult(),
                            'message'           => $test->getMessage(),
                            'value_current'     => $test->getCurrentTestValue(),
                            'value_recommended' => $test->getRecommendedTestValue(),
                            'moreinfo_url'      => $test->getMoreInfoURL(),
                );
                $this->test_results[$test->getTestGroup()][$test->getTestName()] = $rs;

                // initialize if not yet set
                if (!isset($this->result_counts[$rs['result']])) {
                    $this->result_counts[$rs['result']] = 0;
                }

                $this->result_counts[$rs['result']]++;
                $this->num_tests_run++;
            } else {
                $rs = array('result'            => $test->getResult(),
                            'message'           => $test->getMessage(),
                            'value_current'     => null,
                            'value_recommended' => null,
                            'moreinfo_url'      => $test->getMoreInfoURL(),
                );
                $this->result_counts[PHPSECINFO_TEST_RESULT_NOTRUN]++;
                $this->tests_not_run[$test->getTestGroup() . "::" . $test->getTestName()] = $rs;
            }
        }
    }


    /**
     * This is the main output method.  The look and feel mimics phpinfo()
     */
    public function renderOutput($page_title = "Security Information About PHP")
    {
        /**
         * We need to use PhpSecInfo_Test::getBooleanIniValue() below
         * @see PhpSecInfo_Test::getBooleanIniValue()
         */
        if (!class_exists('PhpSecInfo_Test')) {
            include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Test' . DIRECTORY_SEPARATOR . 'Test.php');
        }
        $this->loadView($this->_format);
    }


    /**
     * This is a helper method that makes it easy to output tables of test results
     * for a given test group
     *
     * @param string $group_name
     * @param array $group_results
     * @return bool
     */
    public function _outputRenderTable($group_name, $group_results)
    {

        // exit out if $group_results was empty or not an array.  This sorta seems a little hacky...
        if (!is_array($group_results) || sizeof($group_results) < 1) {
            return false;
        }

        ksort($group_results);

        $this->loadView($this->_format . '/Result', array('group_name' => $group_name, 'group_results' => $group_results));

        return true;
    }


    /**
     * This outputs a table containing a summary of the test results (counts and % in each result type)
     *
     * @see PHPSecInfo::_outputRenderTable()
     * @see PHPSecInfo::_outputGetResultTypeFromCode()
     */
    public function _outputRenderStatsTable()
    {

        foreach ($this->result_counts as $code => $val) {
            if ($code != PHPSECINFO_TEST_RESULT_NOTRUN) {
                $percentage = round($val / $this->num_tests_run * 100, 2);
                $result_type = $this->_outputGetResultTypeFromCode($code);
                $stats[$result_type] = array('count'   => $val,
                                             'result'  => $code,
                                             'message' => "$val out of {$this->num_tests_run} ($percentage%)");
            }
        }

        $this->_outputRenderTable('Test Results Summary', $stats);
    }


    /**
     * This outputs a table containing a summary or test that were not executed, and the reasons why they were skipped
     *
     * @see PHPSecInfo::_outputRenderTable()
     */
    public function _outputRenderNotRunTable()
    {

        $this->_outputRenderTable('Tests Not Run', $this->tests_not_run);
    }


    /**
     * This is a helper function that returns a CSS class corresponding to
     * the result code the test returned.  This allows us to color-code
     * results
     *
     * @param integer $code
     * @return string
     */
    public function _outputGetCssClassFromResult($code)
    {

        switch ($code) {
            case PHPSECINFO_TEST_RESULT_OK:
                return 'value-ok';
                break;

            case PHPSECINFO_TEST_RESULT_NOTICE:
                return 'value-notice';
                break;

            case PHPSECINFO_TEST_RESULT_WARN:
                return 'value-warn';
                break;

            case PHPSECINFO_TEST_RESULT_NOTRUN:
                return 'value-notrun';
                break;

            case PHPSECINFO_TEST_RESULT_ERROR:
                return 'value-error';
                break;

            default:
                return 'value-notrun';
                break;
        }
    }


    /**
     * This is a helper function that returns a label string corresponding to
     * the result code the test returned.  This is mainly used for the Test
     * Results Summary table.
     *
     * @see PHPSecInfo::_outputRenderStatsTable()
     * @param integer $code
     * @return string
     */
    public function _outputGetResultTypeFromCode($code)
    {

        switch ($code) {
            case PHPSECINFO_TEST_RESULT_OK:
                return 'Pass';
                break;

            case PHPSECINFO_TEST_RESULT_NOTICE:
                return 'Notice';
                break;

            case PHPSECINFO_TEST_RESULT_WARN:
                return 'Warning';
                break;

            case PHPSECINFO_TEST_RESULT_NOTRUN:
                return 'Not Run';
                break;

            case PHPSECINFO_TEST_RESULT_ERROR:
                return 'Error';
                break;

            default:
                return 'Invalid Result Code';
                break;
        }
    }


    /**
     * Loads and runs all the tests
     *
     * As loading, then running, is a pretty common process, this saves a extra method call
     *
     * @since 0.1.1
     */
    public function loadAndRun()
    {
        $this->loadTests();
        $this->runTests();
    }


    /**
     * returns an associative array of test data.  Four keys are set:
     * - test_results  (array)
     * - tests_not_run (array)
     * - result_counts (array)
     * - num_tests_run (integer)
     *
     * note that this must be called after tests are loaded and run
     *
     * @since 0.1.1
     * @return array
     */
    public function getResultsAsArray()
    {
        $results = array();

        $results['test_results'] = $this->test_results;
        $results['tests_not_run'] = $this->tests_not_run;
        $results['result_counts'] = $this->result_counts;
        $results['num_tests_run'] = $this->num_tests_run;

        ksort($results['test_results']);
        foreach ($results['test_results'] as &$innerList) {
            if (is_array($innerList)) {
                ksort($innerList);
            }
        }

        return $results;
    }


    /**
     * returns the standard output as a string instead of echoing it to the browser
     *
     * note that this must be called after tests are loaded and run
     *
     * @since 0.1.1
     *
     * @return string
     */
    public function getOutput()
    {
        ob_start();
        $this->renderOutput();
        $output = ob_get_clean();
        return $output;
    }


    /**
     * A very, very simple "view" system
     */
    public function loadView($view_name, $data = null)
    {
        if ($data != null) {
            extract($data);
        }

        $view_file = $this->getViewDirectory() . $view_name . ".php";

        if (file_exists($view_file) && is_readable($view_file)) {
            ob_start();
            include $view_file;
            echo ob_get_clean();
        } else {
            user_error("The view '{$view_file}' either does not exist or is not readable", E_USER_WARNING);
        }
    }


    /**
     * Returns the current view directory
     *
     * @return string
     */
    public function getViewDirectory()
    {
        return $this->_view_directory;
    }


    /**
     * Sets the directory that PHPSecInfo will look in for views
     *
     * @param string $newdir
     */
    public function setViewDirectory($newdir)
    {
        $this->_view_directory = $newdir;
    }


    public function getFormat()
    {
        return $this->_format;
    }


    public function setFormat($format)
    {
        $this->_format = $format;
    }
}


/**
 * A globally-available function that runs the tests and creates the result page
 *
 */
function phpsecinfo()
{
    // modded this to not throw a PHP5 STRICT notice, although I don't like passing by value here
    $psi = new PhpSecInfo();
    $psi->loadAndRun();
    $psi->renderOutput();
}
