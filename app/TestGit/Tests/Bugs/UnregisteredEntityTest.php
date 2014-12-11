<?php
/**
 * User: neiluj
 * Date: 08/12/14
 * Time: 16:34
 */

namespace TestGit\Bugs;

use Fwk\Core\Application;
use TestGit\TestUtils;

require_once __DIR__ .'/../TestUtils.php';

class UnregisteredEntityTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Application
     */
    protected $app;

    public function setUp()
    {
        $this->app = TestUtils::getApplication();
    }

    public function testShowRepository()
    {
        $html = $this->app->run(TestUtils::requestFactory('/fwk/Core/accesses/remove/2'));
        $this->assertEquals(302, $html->getStatusCode());

        TestUtils::authenticate($this->app, $this->app->getServices()->get('usersDao')->getByUsername('neiluj'));

        $html = $this->app->run(TestUtils::requestFactory('/fwk/Core/accesses/remove/2'));
        $this->assertTrue(strpos($html->getContent(), 'Unregistered entity') > 0);
    }
}