/*!
 * Piwik - free/libre analytics platform
 *
 * Screenshot test for SecurityInfo main page.
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

describe("SecurityInfoReport", function () {
    this.timeout(0);

    it('should load the security info admin page correctly', function (done) {
        expect.screenshot('admin_security_info').to.be.captureSelector('#content', function (page) {
            page.load("?" + generalParams + "&module=SecurityInfo&tests_hide_piwik_version=1");
        }, done);
    });
});