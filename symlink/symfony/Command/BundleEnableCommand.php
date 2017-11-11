<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BundleEnableCommand
 *
 * @package AppBundle\Command
 */
class BundleEnableCommand extends Command
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
            ->setName('symfonypress:enable')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Exact string for bundle to enable. USE QUOTES!');

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
        if (!file_exists($this->kernel)) {
            throw new \ErrorException(sprintf("Could not locate file %s", $this->kernel));
        }
        if (!is_writable($this->kernel)) {
            throw new \ErrorException(sprintf('Cannot write into AppKernel (%s)', $this->kernel));
        }

        $array_open = '[';
        $array_close = ']';

        $namespace = $input->getArgument('namespace');
        $appContent = file_get_contents($this->kernel);
        $newBundle = "new {$namespace}(),";
        $pattern = '/\$bundles\s?=\s?\\' . $array_open . '(.*?)\\' . $array_close . ';/is';

        preg_match($pattern, $appContent, $matches);

        $bList = rtrim($matches[1], "\n ");
        $e = explode(",", $bList);
        $firstBundle = array_shift($e);
        $tabs = substr_count($firstBundle, '    ');

        $newBList = "\$bundles = $array_open"
            . $bList . "\n"
            . str_repeat('    ', $tabs) . $newBundle . "\n"
            . str_repeat('    ', $tabs - 1) . "$array_close;";

        file_put_contents($this->kernel, preg_replace($pattern, $newBList, $appContent));
    }
}