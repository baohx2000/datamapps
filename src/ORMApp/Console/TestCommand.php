<?php
namespace ORMApp\Console;

use B2k\Doc\Helper\ManagerRegistryHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ORMApp\Entities;

class TestCommand extends Command {
    const DO_CREATE = 'create';
    const DO_READ = 'read';
    const DO_UPDATE = 'update';
    const DO_DELETE = 'delete';
    const DO_JOINREAD = 'joinread';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    protected function configure()
    {
        parent::configure();

        $this->setName('test:ormapp')
            ->addArgument('do', InputArgument::OPTIONAL, 'Which option? [create, read, update, delete]', 'create');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ManagerRegistryHelper $emHelper */
        $emHelper = $this->getHelper('doctrine');
        $this->em = $emHelper->getManager('default');

        switch ($input->getArgument('do')) {
            case self::DO_CREATE:
                $this->doCreate($input, $output);
                break;
            case self::DO_READ:
                $this->doRead($input, $output);
                break;
            case self::DO_JOINREAD:
                $this->doJoinRead($input, $output);
                break;
            case self::DO_UPDATE:
                $this->doUpdate($input, $output);
                break;
            case self::DO_DELETE:
                $this->doDelete($input, $output);
                break;
            default:
                $output->writeln('<error>Unknown do argument: '.$input->getArgument('do').'</error>');
                break;
        }

    }

    protected function doUpdate(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Updating person name & address line 1');

        $q = $this->em->createQuery('SELECT p FROM ORMApp\Entities\Person p INNER JOIN p.addresses a INNER JOIN p.phones ph');
        $results = $q->setMaxResults(1000)->getResult();

        $namePrefix = sha1(microtime());

        $start = microtime(true);
        /** @var Entities\Person $p */
        foreach ($results as $p) {
            $p->setFirstName($namePrefix.$p->getId());
            /** @var Entities\Address $a */
            foreach ($p->getAddresses() as $a) {
                $a->setLine1($namePrefix.$a->getId());
            }
        }
        $this->em->flush();

        $end = microtime(true);
        $diff = $end-$start;
        $output->writeln('Took '.$diff.'s');
    }

    protected function doDelete(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Removing them');
        $start = microtime(true);

        $people = $this->em->getRepository(Entities\Person::class)
            ->findAll();
        foreach ($people as $p) {
            $this->em->remove($p);
        }
        $this->em->flush();

        $end = microtime(true);
        $diff = $end-$start;
        $output->writeln('Took '.$diff.'s');
    }

    protected function doJoinRead(InputInterface $input, OutputInterface $output)
    {
        $ids = $this->em->getConnection()->fetchColumn('SELECT id FROM addresses');
        $start = microtime(true);
        $people = $this->em
            ->createQuery('SELECT p, a FROM ORMApp\Entities\Person p INNER JOIN p.addresses a')
            ->getResult();
        $end = microtime(true);

        $output->writeln('Read '.count($people).' people in '.($end-$start).'s');
    }

    protected function doRead(InputInterface $input, OutputInterface $output)
    {
//        $output->writeln('Loading them back up');
        $memStart = memory_get_usage(true);
        $start = microtime(true);
        $people = $this->em->getRepository(Entities\Person::class)
            ->findAll();
        $end = microtime(true);
        $diff = $end-$start;
        $memEnd = memory_get_usage(true);

        $output->writeln('Loaded '.count($people).' records');
//        $output->writeln('Used '.$memEnd - $memStart.' bytes');
        $output->writeln('Took '.$diff.'s');

        $output->writeln('Loading via DQL: SELECT p, a, ph FROM ORMApp\Entities\Person p INNER JOIN p.addresses a INNER JOIN p.phones ph');
        $start = microtime(true);
        $q = $this->em->createQuery('SELECT p, a, ph FROM ORMApp\Entities\Person p INNER JOIN p.addresses a INNER JOIN p.phones ph');
        $results = $q->setMaxResults(1000)->getResult(Query::HYDRATE_ARRAY);
//        var_dump($results[0]);
        $end = microtime(true);
        $diff = $end-$start;
        $output->writeln('Took '.$diff.'s');
    }

    protected function doCreate(InputInterface $input, OutputInterface $output)
    {
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
            $this->em->persist($p);
        }
        $this->em->flush();

        $end = microtime(true);
        $diff = $end-$start;
        $output->writeln('Took '.$diff.'s');
    }
}
