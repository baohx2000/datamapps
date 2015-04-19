<?php

namespace ORMApp\Console\Symfony\Helpers;

use Silex\Application;
use Symfony\Component\Console\Helper\Helper;

class SilexApplicationHelper extends Helper {

    /** @var  Application */
    protected $application;

    function __construct(Application $app) {
        $this->application = $app;
    }

    /**
     * @return \Pimple\Container
     */
    public function getApplication() {
        return $this->application;
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName() {
        return "silexApplication";
    }
}