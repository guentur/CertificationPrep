<?php

namespace CertificationPrep\AreaCode\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\AreaList;
use \Magento\Framework\App\Area;
use Magento\Framework\App\AreaInterface;
use \Magento\Config\Console\Command\EmulatedAdminhtmlAreaProcessor;
use \CertificationPrep\AreaCode\Model\TestModel;
use \CertificationPrep\AreaCode\Model\TestModelFactory;

// ! Important. How to use this Emulation?
use Magento\Store\Model\App\Emulation;

class NullAreaCodeInCLI extends Command
{
    private $state;

    private $areaList;

    private $testModel;

    private $testModelFactory;

    private $emulatedAdminhtmlAreaProcessor;

    /**
     * @param State $state
     * @param TestModel $testModel
     * @param TestModelFactory $testModelFactory
     * @param EmulatedAdminhtmlAreaProcessor $emulatedAdminhtmlAreaProcessor
     * @param string|null $name
     */
    public function __construct(
        State $state,
        AreaList $areaList,
        TestModel $testModel,
        TestModelFactory $testModelFactory,
        EmulatedAdminhtmlAreaProcessor $emulatedAdminhtmlAreaProcessor,
        string $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->areaList = $areaList;
        $this->testModel = $testModel;
        $this->testModelFactory = $testModelFactory;
        $this->emulatedAdminhtmlAreaProcessor = $emulatedAdminhtmlAreaProcessor;
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
        $output->writeln('------------------------------------------------');
        $output->writeln('TRY/CATCH');
        try {
            $output->writeln($this->state->getAreaCode());
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $output->writeln($exception->getMessage());
        }

        $paramToEmulation = [
            'message' => 'Area code in emulation: ',
//            'diArgumentsData' => $this->testModel->getData(),
//            'diArgumentsData' => [],
            'diArgumentsData' => $this->testModelFactory->create(),
        ];

        // As you can see I use both variables: self and this in the callback function and it works.
        // It is interesting behavior because I expected $this will not be available in function scope
        $self = $this;
        $callback = function(string $message, \CertificationPrep\AreaCode\Model\TestModel $diArgumentsData) use ($output, $input, $self) {
//            $area = $this->areaList->getArea($this->state->getAreaCode());
//            $area->load(\Magento\Framework\App\AreaInterface::PART_CONFIG);

            $output->writeln('-------------From closure-----------------------------------');
            $output->writeln($message . $this->state->getAreaCode());

            $output->writeln('FIRST');
            $output->writeln('-------------From closure-----------------------------------');
            $testModelInstance = $self->testModelFactory->create();
            $result = $testModelInstance->getData();
            var_dump($result);

            $output->writeln('SECOND');
            $output->writeln('-------------From closure-----------------------------------');
            var_dump($self->testModel->getData());

            $output->writeln('THIRD');
            $output->writeln('-------------From closure-----------------------------------');
            var_dump($diArgumentsData->getData());

            return $result;
        };

        $output->writeln($emulationNumber = 'FOURTH');
        $output->writeln('------------------------------------------------');
        $this->state->emulateAreaCode(
            Area::AREA_ADMINHTML,
            $callback,
            $paramToEmulation
        );
        $output->writeln('------------------------------------------------');
        $this->state->emulateAreaCode('customarea', $callback, $paramToEmulation);

        $output->writeln('------------------------------------------------');
        // Why it sets scope->setCurrentScope(Area::AREA_ADMINHTML) in
        $this->emulatedAdminhtmlAreaProcessor->process($callback, $paramToEmulation);

        $this->state->setAreaCode(Area::AREA_ADMINHTML);
        $testModelInstance = $self->testModelFactory->create();
        $output->writeln('-------------$this->state->setAreaCode(Area::AREA_ADMINHTML);-----------------------------------');
        var_dump($testModelInstance->getData());

        return true;
    }

}
