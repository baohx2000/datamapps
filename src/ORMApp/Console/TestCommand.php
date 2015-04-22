<?php
/**
 * Created by PhpStorm.
 * User: gordon
 * Date: 4/21/15
 * Time: 9:27 PM
 */

namespace ORMApp\Console;


use B2k\Doc\Helper\ManagerRegistryHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ORMApp\Entities;

class TestCommand extends Command {
    protected function configure()
    {
        parent::configure();

        $this->setName('test:ormapp');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ManagerRegistryHelper $emHelper */
        $emHelper = $this->getHelper('doctrine');
        $em = $emHelper->getManager('default');

        $output->writeln('Inserting 5000 records (1000 persons, each with 2 phones and 2 addresses');

        $start = microtime(true);
        for($i = 0; $i < 1000; $i++) {
            $p = (new Entities\Person())
                ->setCreated(new \DateTime())
                ->setModified(new \DateTime())
                ->setFirstName('Bob'.$i)
                ->setLastName('McBob')
                ->addPhone(
                    (new Entities\Phone())
                        ->setCreated(new \DateTime())
                        ->setModified(new \DateTime())
                        ->setPhoneNumber('111-222-3333')
                )
                ->addPhone(
                    (new Entities\Phone())
                        ->setCreated(new \DateTime())
                        ->setModified(new \DateTime())
                        ->setPhoneNumber('111-222-3333')
                )
                ->addAddress(
                    (new Entities\Address())
                        ->setCreated(new \DateTime())
                        ->setModified(new \DateTime())
                        ->setLine1('1233 N Mill Ave')
                        ->setCity('Tempe')
                        ->setState('AZ')
                        ->setPostalCode('85281')
                )
                ->addAddress(
                    (new Entities\Address())
                        ->setCreated(new \DateTime())
                        ->setModified(new \DateTime())
                        ->setLine1('1234 N Mill Ave')
                        ->setCity('Tempe')
                        ->setState('AZ')
                        ->setPostalCode('85281')
                );
            $em->persist($p);
        }
        $em->flush();
        $end = microtime(true);
        $diff = $end-$start;
        $output->writeln('Took '.$diff.'s');

        $em->clear();

        $output->writeln('Loading them back up');
        $memStart = memory_get_usage();
        $start = microtime(true);
        $people = $em->getRepository(Entities\Person::class)
            ->findAll();
        $end = microtime(true);
        $diff = $end-$start;
        $memEnd = memory_get_usage();
        $output->writeln('Loaded '.count($people).' records');
        $output->writeln('Used '.$memEnd - $memStart.' bytes');
        $output->writeln('Took '.$diff.'s');

        $output->writeln('Removing them');
        $start = microtime(true);
        foreach($people as $person) {
            $em->remove($person);
        }
        $em->flush();
        $end = microtime(true);
        $diff = $end-$start;
        $output->writeln('Took '.$diff.'s');
    }
}
