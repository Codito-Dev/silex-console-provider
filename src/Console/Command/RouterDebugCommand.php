<?php

namespace Codito\Silex\Console\Command;

use Codito\Silex\Console\Helper\DescriptorHelper;

use Silex\Application as SilexApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 * A console command for retrieving information about routes.
 * Command taken from SymfonyFrameworkBundle and modified for Silex usage.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 * @author Grzegorz Korba <grzegorz.korba@codito.net>
 */
class RouterDebugCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        /* @var $app SilexApplication */
        $app = $this->getSilexApplication();

        if (
            !$app->offsetExists('routes') ||
            !$app['routes'] instanceof RouteCollection ||
            $app['routes']->count() === 0
        ) {
            return false;
        }

        return parent::isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('debug:router')
            ->setAliases([
                'router:debug',
            ])
            ->setDefinition([
                new InputArgument('name', InputArgument::OPTIONAL, 'A route name'),
                new InputOption('show-controllers', null, InputOption::VALUE_NONE, 'Show assigned controllers in overview'),
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'To output route(s) in other formats', 'txt'),
                new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw route(s)'),
            ])
            ->setDescription('Displays current routes for an application')
            ->setHelp(<<<EOF
The <info>%command.name%</info> displays the configured routes:

  <info>php %command.full_name%</info>
  
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException When route does not exist
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $app SilexApplication */
        $app = $this->getSilexApplication();

        $name = $input->getArgument('name');
        /* @var $descriptor DescriptorHelper */
        $descriptor = $this->getHelper('descriptor');

        if ($name) {
            /* @var $route Route */
            $route = $app['routes']->get($name);

            if (!$route) {
                throw new \InvalidArgumentException(sprintf('The route "%s" does not exist.', $name));
            }

            $descriptor->describe($output, $route, [
                'format' => $input->getOption('format'),
                'raw_text' => $input->getOption('raw'),
                'name' => $name,
            ]);
        } else {
            if ($app['routes']->count() === 0) {
                $output->writeln('<error>No routes defined.</error>');
                return;
            }

            $descriptor->describe($output, $app['routes'], [
                'format' => $input->getOption('format'),
                'raw_text' => $input->getOption('raw'),
                'show_controllers' => $input->getOption('show-controllers'),
            ]);
        }
    }
}