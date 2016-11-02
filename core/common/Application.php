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
namespace Phanbook\Common;

use Phalcon\Di;
use Phalcon\DiInterface;
use InvalidArgumentException;
use Phalcon\Http\ResponseInterface;
use Phalcon\Cli\Console as CliApplication;
use Phalcon\Mvc\Application as MvcApplication;
use Phalcon\Application as AbstractApplication;
use Phanbook\Common\Library\Providers\ServiceProviderInterface;

/**
 * \Phanbook\Common\Application
 *
 * @package Phanbook\Common
 */
class Application
{
    /**
     * The Dependency Injector.
     * @var DiInterface
     */
    protected $di;

    /**
     * The Service Providers.
     * @var ServiceProviderInterface[]
     */
    protected $serviceProviders = [];

    /**
     * The Phalcon Application.
     * @var AbstractApplication
     */
    protected $app;

    /**
     * The Application mode
     * @var string
     */
    protected $mode;

    /**
     * Application constructor.
     *
     * @param string $mode The Application mode: either "normal" either "cli" or "api".
     */
    public function __construct($mode = 'normal')
    {
        $this->di = new Di();

        $this->di->setShared('bootstrap', $this);
        Di::setDefault($this->di);

        $providers = require ROOT_DIR . '/core/config/providers.php';
        if (is_array($providers)) {
            $this->initializeServices($providers);
        }

        $this->app = $this->createInternalApplication($mode);
        $this->app->setEventsManager($this->di->getShared('eventsManager'));
        $this->app->setDI($this->di);
    }

    /**
     * Runs the Application.
     *
     * @return string
     */
    public function run()
    {
        return $this->getOutput();
    }

    /**
     * Get Application output.
     *
     * @return ResponseInterface|string
     */
    protected function getOutput()
    {
        if ($this->app instanceof MvcApplication) {
            return $this->app->handle()->getContent();
        }

        return $this->app->handle();
    }

    /**
     * Initialize Services in the Dependency Injector Container.
     *
     * @param  string[] $providers
     * @return $this
     */
    protected function initializeServices(array $providers)
    {
        foreach ($providers as $name => $class) {
            $this->initializeService(new $class($this->di));
        }

        return $this;
    }

    /**
     * Initialize the Service in the Dependency Injector Container.
     *
     * @param  ServiceProviderInterface $serviceProvider
     * @return $this
     */
    protected function initializeService(ServiceProviderInterface $serviceProvider)
    {
        $serviceProvider->register();
        $this->serviceProviders[$serviceProvider->getName()] = $serviceProvider;

        return $this;
    }

    /**
     * Create internal Application to handle requests.
     *
     * @param  string $mode The Application mode.
     * @return CliApplication|MvcApplication
     *
     * @throws InvalidArgumentException
     */
    protected function createInternalApplication($mode)
    {
        $this->mode = $mode;

        switch ($mode) {
            case 'normal':
                return new MvcApplication();
            case 'cli':
                return new CliApplication();
            case 'api':
                throw new InvalidArgumentException(
                    'Not implemented yet.'
                );
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid Application mode. Expected either "normal" either "cli" or "api". Got %s',
                        is_scalar($mode) ? $mode : var_export($mode, true)
                    )
                );
        }
    }
}
