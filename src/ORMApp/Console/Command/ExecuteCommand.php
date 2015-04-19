<?php

namespace ORMApp\Console\Command;

use ORMApp\Security\UserIdentity;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExecuteCommand extends \Doctrine\DBAL\Tools\Console\Command\ImportCommand
{
    /**
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getHelper('app')->getApplication();
        $app['Identity']->setIdentity(new UserIdentity($input->getOption('runAsUser'), $app));
    }

    protected function configure() {
        parent::configure();

        $this->setName('contatta:migrations:execute');
        $this->addOption('runAsUser', 'U', InputOption::VALUE_OPTIONAL, 'User Identity to run as', 0);
    }

} 