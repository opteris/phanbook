<?php
/**
 * Phanbook : Delightfully simple forum software
 *
 * Licensed under The GNU License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link    http://phanbook.com Phanbook Project
 * @since   1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
namespace Phanbook\Controllers;

use Phalcon\Mvc\Controller;

/**
 * Frontend Error Controller
 */
class ErrorController extends Controller
{
    public function indexAction()
    {
    }
    /**
     * 404 paga
     */
    public function show404Action()
    {
        // Send a HTTP 404 response header
        $this->response->setStatusCode(404, 'Page Not Found');
        $this->view->pick('error/404');
    }
    /**
     * 503 page
     */
    public function show503Action()
    {
        $this->response->setStatusCode(503, 'Site is Down for Maintenance');
        $this->view->pick('error/503');
    }
}
