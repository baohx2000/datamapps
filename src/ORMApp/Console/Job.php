<?php

namespace ORMApp\Console;

use ORMApp\Job\InlineJob;

/**
 * Mock job class to expose features to report status, completion, logging back to Contatta
 * @package Email
 */
class Job extends InlineJob
{
    function __construct() {
        parent::__construct('cli.logger');
    }
}