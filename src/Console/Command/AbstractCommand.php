<?php

namespace Codito\Silex\Console\Command;

use Silex\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * AbstractCommand is a base class for all commands
 *
 * @author Grzegorz Korba <grzegorz.korba@codito.net>
 * @copyright 2015 Codito.net
 */
abstract class AbstractCommand extends BaseCommand
{
    /**
     * Returns main Silex application in which console service is registered
     * 
     * @return Application
     */
    public function getSilexApplication()
    {
        return $this->getApplication()->getSilexApplication();
    }
}