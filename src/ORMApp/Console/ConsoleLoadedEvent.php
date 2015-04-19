<?php

namespace ORMApp\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\EventDispatcher\Event;

class ConsoleLoadedEvent extends Event {

    const EVENT_CLI_LOADED = 'loadCli.post';

    /** @var \Symfony\Component\Console\Application  */
    public $cli;

    function __construct(Application $cli) {
        $this->cli = $cli;
    }
}