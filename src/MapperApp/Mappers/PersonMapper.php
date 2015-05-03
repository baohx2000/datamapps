<?php
namespace MapperApp\Mappers;

use Synapse\Mapper\AbstractMapper;
use Synapse\Mapper\DeleterTrait;
use Synapse\Mapper\FinderTrait;
use Synapse\Mapper\InserterTrait;
use Synapse\Mapper\UpdaterTrait;
use Synapse\Stdlib\Arr;
use Zend\Db\Sql\Select;

class PersonMapper extends AbstractMapper
{
    use InserterTrait;
    use FinderTrait;
    use UpdaterTrait;
    use DeleterTrait;

    protected $tableName = 'person';
    protected $createdDatetimeColumn = 'created';

    protected function addJoins(Select $query, $wheres, $options = [])
    {
        $address_id = Arr::get($wheres, 'address_id');
        if ($address_id !== null) {
            unset($wheres['address_id']);
            $wheres['addresses.id'] = $address_id;
            $query->join(
                'addresses',
                'addresses.person_id = person.id',
                ['address_id' => 'id', 'line1' => 'line1'],
                $query::JOIN_INNER
            );
        }
        return $wheres;
    }
}
