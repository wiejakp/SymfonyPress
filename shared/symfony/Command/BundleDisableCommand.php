<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BundleDisableCommand
 *
 * @package AppBundle\Command
 */
class BundleDisableCommand extends Command
{
    /**
     * @var string
     */
    private $kernel;

    /**
     * configure command
     */
    protected function configure(): void
    {
        $this
            ->setName('symfonypress:disable')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Exact string for bundle to disable. USE QUOTES!');

        $this->kernel = dirname($_SERVER['SCRIPT_FILENAME']) . '/../app/AppKernel.php';
    }

    /**
     * execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \ErrorException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /**
         * TODO
         */
    }
}