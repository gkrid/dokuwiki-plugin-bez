<?php

namespace dokuwiki\plugin\bez\mdl;

class LabelFactory extends Factory {
    protected function select_query() {
        return 'SELECT * FROM label';
    }
}