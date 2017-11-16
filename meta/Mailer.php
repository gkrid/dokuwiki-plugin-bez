<?php
//require_once DOKU_INC.'inc/Mailer.class.php';
namespace dokuwiki\plugin\bez\meta;

class Mailer extends \Mailer {
    public function __construct() {
        global $conf;
        
        parent::__construct();
        
        $reps = array(
            'wiki_name' => $conf['title'],
            'doku_url' => DOKU_URL 
        );
        
        $this->replacements['text'] = array_merge($this->replacements['text'], $reps);
        $this->replacements['html'] = array_merge($this->replacements['html'], $reps);
    }
}
