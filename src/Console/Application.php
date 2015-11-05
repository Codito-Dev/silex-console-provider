<?php

namespace Codito\Silex\Console;

use Silex\Application as SilexApplication;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * Application
 *
 * @author Grzegorz Korba <grzegorz.korba@codito.net>
 * @copyright 2015 Codito.net
 */
class Application extends BaseApplication
{
    /**
     * @var SilexApplication
     */
    private $silexApplication;

    /**
     * Constructor.
     * 
     * @param SilexApplication $application
     * @param string $name
     * @param string $version
     */
    public function __construct(SilexApplication $application, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->silexApplication = $application;
        $application->boot();
    }

    /**
     * Returns main Silex application in which console service is registered
     * 
     * @return SilexApplication
     */
    public function getSilexApplication()
    {
        return $this->silexApplication;
    }
}