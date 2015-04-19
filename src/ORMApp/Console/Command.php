<?php
namespace ORMApp\Console;

use Symfony\Component\Console\Command\Command as SymCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymCommand
{

    protected function configure() {
        parent::configure();

        $this->addOption('memcheck', null, InputOption::VALUE_NONE, 'Show memory consumption');
        $this->setCode(function (InputInterface $input, OutputInterface $output) {
            return $this->doExecute($input, $output);
        });
    }

    private function doExecute(InputInterface $input, OutputInterface $output) {
        $this->preExecute();
        $result = $this->execute($input, $output);
        $this->postExecute();
        if ($input->getOption('memcheck')) {
            $output->writeln('Max memory used: ' . floor(memory_get_peak_usage(true) / 1000) / 1000 . 'MB');
        }

        return $result;
    }

    public function preExecute() {
    }

    public function postExecute() {
    }

}