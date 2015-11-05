<?php

namespace Codito\Silex\Console\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ClearCacheCommand clears the cache directory (or its subdirectory)
 *
 * @author Grzegorz Korba <grzegorz.korba@codito.net>
 */
class ClearCacheCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        /* @var $app SilexApplication */
        $app = $this->getSilexApplication();

        if (
            !$app->offsetExists('cache_dir') ||
            !is_dir($app['cache_dir'])
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
        $this->setName('cache:clear')
            ->setDefinition([
                new InputOption('dir', 'd', InputOption::VALUE_OPTIONAL, 'Specified cache sub-directory', 'twig'),
                new InputOption('all', 'a', InputOption::VALUE_NONE, 'Clear whole cache directory'),
            ])
            ->setDescription('Clears cache. As default only Twig cache is cleared, but custom cache sub-directory can be specified with --dir/-d option.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $app SilexApplication */
        $app = $this->getSilexApplication();

        $fs = new Filesystem();
        $all = $input->getOption('all');
        $cacheDir = $app['cache_dir'];

        // Check for sub-directory only if --all/-a option wasn't passed to command
        if(!$all && ($dir = $input->getOption('dir'))) {
            $cacheSubDir = $cacheDir . DIRECTORY_SEPARATOR . $dir;

            if(!$fs->exists($cacheSubDir)) {
                $msg = sprintf('Directory "%s" does not exist under cache directory', $dir);

                // Only explicitely specified and non-existing paths throws exception
                if($dir != 'twig') {
                    throw new \InvalidArgumentException($msg);
                }
                // For default dir (twig) only display message and end command execution
                else {
                    $output->writeln($msg . ', nothing to do!');
                    return;
                }
            }
            $cacheDir = $cacheSubDir;
        }

        $output->writeln(sprintf('Clearing <comment>%s</comment>', realpath($cacheDir)));

        // Create recursive iterator for directory structure, with custom filter (callback) that keeps "dotfiles" (like .gitignore)
        $deleteIterator = new \RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator(
                new \RecursiveDirectoryIterator($cacheDir, \FilesystemIterator::SKIP_DOTS),
                function ($fileInfo, $key, $iterator) {
                    // Only accept entries that do NOT start with an .
                    return substr($fileInfo->getFilename(), 0, 1) != '.';
                }
            ),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        $toDelete = \iterator_count($deleteIterator);

        if($toDelete > 0) {
            $fs->remove($deleteIterator);
            $output->writeln(sprintf('Deleted <info>%s</info> file%s', $toDelete, $toDelete > 1 ? 's' : null));
        }
        else {
            $output->writeln('No files to delete!');
        }
    }
}