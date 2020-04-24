/*!
 * Matomo - free/libre analytics platform
 *
 * Screenshot test for SecurityInfo main page.
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

describe("SecurityInfoReport", function () {
    this.timeout(0);

    it('should load the security info admin page correctly', async function () {
        await page.goto("?idSite=1&period=year&date=2012-08-09&module=SecurityInfo&tests_hide_piwik_version=1");
        expect(await page.screenshotSelector('#content')).to.matchImage('admin_security_info');
    });
});