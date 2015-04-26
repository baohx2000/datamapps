<?php
namespace MapperApp\Mappers;

use Synapse\Mapper\AbstractMapper;
use Synapse\Mapper\DeleterTrait;
use Synapse\Mapper\FinderTrait;
use Synapse\Mapper\InserterTrait;
use Synapse\Mapper\UpdaterTrait;

class PhoneMapper extends AbstractMapper
{
    use InserterTrait;
    use FinderTrait;
    use UpdaterTrait;
    use DeleterTrait;

    protected $tableName = 'phones';
    protected $createdDatetimeColumn = 'created';
}
