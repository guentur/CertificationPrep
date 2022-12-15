<?php

namespace CertificationPrep\AreaCode\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\AreaList;
use \Magento\Framework\App\Area;

class NullAreaCodeInCLI extends Command
{
    private $state;

    /**
     * @param State $state
     * @param string|null $name
     */
    public function __construct(
        State $state,
        string $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
    }

    /**
     * Initialization of the command.
     */
    protected function configure()
    {
        $this->setName('certprep:area:test');
        $this->setDescription('==However, as areas are tied to URLs==, a command line scripts **has no area**.
You’ll need to use Magento’s App State object to manually set an area code.');
        parent::configure();
    }

    /**
     * CLI command description.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output): bool
    {
        try {
            $output->writeln($this->state->getAreaCode());
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $output->writeln($exception->getMessage());
        }

        $paramToEmulation = [''];

        $this->state->emulateAreaCode(Area::AREA_ADMINHTML, function() use ($output, $input) {
            $output->writeln('Area code in emulation: ' . $this->state->getAreaCode());
        }, $paramToEmulation);

        return true;
    }
}
