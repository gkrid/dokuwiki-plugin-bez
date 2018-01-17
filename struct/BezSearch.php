<?php

namespace dokuwiki\plugin\bez\struct;

use dokuwiki\plugin\bez\mdl\Factory;
use dokuwiki\plugin\struct\meta\SearchConfig;

class BezSearch extends SearchConfig {

    protected $factory;

    public function __construct($data, Factory $factory) {
        parent::__construct($data);

        $this->factory = $factory;
    }
}