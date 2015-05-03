<?php

namespace MapperApp\Console;

use MapperApp\Entities\AddressEntity;
use MapperApp\Entities\PersonEntity;
use MapperApp\Entities\PhoneEntity;
use MapperApp\Mappers\AddressMapper;
use MapperApp\Mappers\PersonMapper;
use MapperApp\Mappers\PhoneMapper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Db\Adapter\Adapter;

class TestCommand extends Command
{
    const DO_CREATE = 'create';
    const DO_READ = 'read';
    const DO_UPDATE = 'update';
    const DO_DELETE = 'delete';
    const DO_JOINREAD = 'joinread';

    /**
     * @var PersonMapper
     */
    protected $personMapper;
    /**
     * @var PhoneMapper
     */
    protected $phoneMapper;
    /**
     * @var AddressMapper
     */
    protected $addressMapper;

    public function __construct(
        $name = null,
        PersonMapper $personMapper,
        PhoneMapper $phoneMapper,
        AddressMapper $addressMapper
    ) {
        parent::__construct($name);
        $this->personMapper = $personMapper;
        $this->phoneMapper = $phoneMapper;
        $this->addressMapper = $addressMapper;
    }
    protected function configure()
    {
        parent::configure();
        $this->setName('test:mapperapp')
            ->setDescription('Do a test')
            ->addArgument(
                'do',
                InputArgument::REQUIRED,
                'create, read, joinread, update, or delete'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

    private function doCreate(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Inserting 5000 records (1000 persons, each with 2 phones and 2 addresses');

        $start = microtime(true);

        // something the orm does automatically for data consistency
        $this->personMapper->getDbAdapter()->query('START TRANSACTION', Adapter::QUERY_MODE_EXECUTE);

        for($i = 0; $i < 1000; $i++) {
            $person = (new PersonEntity())
                ->exchangeArray([
                    'first_name' => 'Bob'.$i,
                    'last_name' => 'McBob',
                    'modified' => date('Y-m-d H:i:s'),
                ]);
            $this->personMapper->persist($person);
            for ($j = 0; $j < 2; $j++) {
                $a = (new AddressEntity())
                    ->exchangeArray([
                        'modified' => date('Y-m-d H:i:s'),
                        'person_id' => $person->getId(),
                        'line1' => '1233 N Mill Ave',
                        'city' => 'Tempe',
                        'state' => 'AZ',
                        'postal_code' => '85281',
                    ]);
                $this->addressMapper->persist($a);

                $ph = (new PhoneEntity())
                    ->exchangeArray([
                        'modified' => date('Y-m-d H:i:s'),
                        'person_id' => $person->getId(),
                        'phone_number' => '111-222-3333'
                    ]);
                $this->phoneMapper->persist($ph);
            }
        }
        try {
            $this->personMapper->getDbAdapter()->query('COMMIT', Adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            $this->personMapper->getDbAdapter()->query('ROLLBACK', Adapter::QUERY_MODE_EXECUTE);
            throw $e;
        }

        $end = microtime(true);
        $diff = $end-$start;
        $output->writeln('Took '.$diff.'s');
    }

    private function doRead(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Reading all people with corresponding addresses and phone numbers');
        $start = microtime(true);

        $people = $this->personMapper->findAll();
        $output->writeln('Loaded '.$people->count().' records.');
        $peopleMap = [];
        $ids = [];
        // EntityIterators really should implement ArrayAccess so we can use array_map
        foreach ($people as $p) {
            $peopleMap[$p->getId()] = $p;
            $ids[] = $p->getId();
        }

        $addresses = $this->addressMapper->findAllBy(['person_id' => $ids]);
        foreach ($addresses as $address) {
            $a = $peopleMap[$address->getPersonId()]->getAddresses();
            $a[] = $address;
            $peopleMap[$address->getPersonId()]->setAddresses($a);
        }

        $phones = $this->addressMapper->findAllBy(['person_id' => $ids]);
        foreach ($phones as $address) {
            $a = $peopleMap[$address->getPersonId()]->getPhones();
            $a[] = $address;
            $peopleMap[$address->getPersonId()]->setPhones($a);
        }

        $end = microtime(true);
        $diff = $end-$start;
        $output->writeln('Took '.$diff.'s');
    }

    private function doJoinRead(InputInterface $input, OutputInterface $output)
    {
        $ids = $this->personMapper
            ->getDbAdapter()
            ->query('SELECT id FROM addresses', Adapter::QUERY_MODE_EXECUTE)
            ->toArray();
        $start = microtime(true);
        $people = $this->personMapper->findAllBy(['address_id' => array_column($ids, 'id')]);
        $end = microtime(true);

        $output->writeln('Read '.$people->count().' people in '.($end-$start).'s');
    }

    private function doUpdate(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Updating person name & address line 1');

        $this->personMapper->getDbAdapter()->query('START TRANSACTION', Adapter::QUERY_MODE_EXECUTE);

        // this is a horrible way to force the mapper to do a join.
        $results = $this->personMapper->findAllBy(['address_id' => ['addresses.id', '>', 0]]);

        // make map to inject addresses
        $map = [];
        foreach ($results as $r) {
            $map[$r->getAddressId()] = $r;
        }
        $addresses = $this->addressMapper->findAll();
        foreach ($addresses as $address) {
            $map[$address->getId()]->setAddress($address);
        }

        $namePrefix = sha1(microtime());

        $start = microtime(true);
        foreach ($results as $p) {
            $p->setFirstName($namePrefix.$p->getId());
            $this->personMapper->update($p);
            $a = $this->addressMapper->findById($p->getAddressId());
            $a->setLine1($namePrefix.$a->getId());
            $this->addressMapper->update($a);
        }
        $this->personMapper->getDbAdapter()->query('COMMIT', Adapter::QUERY_MODE_EXECUTE);

        $end = microtime(true);
        $diff = $end-$start;
        $output->writeln('Took '.$diff.'s');
    }

    private function doDelete(InputInterface $input, OutputInterface $output)
    {

    }
}
