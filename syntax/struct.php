<?php

use \dokuwiki\plugin\bez\mdl\Model;
use \dokuwiki\plugin\bez\struct\BezSearch;

use dokuwiki\plugin\struct\meta\ConfigParser;
use dokuwiki\plugin\struct\meta\AggregationTable;
use dokuwiki\plugin\struct\meta\StructException;

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class syntax_plugin_bez_struct extends DokuWiki_Syntax_Plugin {

    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }

    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'block';
    }

    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 155;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('----+ *struct bez *-+\n.*?\n----+', $mode, 'plugin_bez_struct');
    }

    /**
     * Handle matches of the struct syntax
     *
     * @param string $match The match of the syntax
     * @param int $state The state of the handler
     * @param int $pos The position in the document
     * @param Doku_Handler $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler) {
        global $conf;

        $lines = explode("\n", $match);
        array_shift($lines);
        array_pop($lines);

        try {
            $parser = new ConfigParser($lines);
            $config = $parser->getConfig();
            return $config;
        } catch(StructException $e) {
            msg($e->getMessage(), -1, $e->getLine(), $e->getFile());
            if($conf['allowdebug']) msg('<pre>' . hsc($e->getTraceAsString()) . '</pre>', -1);
            return null;
        }
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string $mode Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer $renderer The renderer
     * @param array $data The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        if($mode != 'xhtml') return true;
        if(!$data) return false;
        global $INFO;
        global $conf;
        global $auth;

        return true;

        try {
            $schema = $data['schemas'][0][0];
            /** @var Model $model */
            $model = new Model($auth, $INFO['client'], $this, $conf);

            $factory = $model->factory($schema);
            $search = new BezSearch($data, $factory);

            /** @var AggregationTable $table */
            $table = new AggregationTable($INFO['id'], $mode, $renderer, $search);
            $table->render();

        } catch(Exception $e) {
            msg($e->getMessage(), -1, $e->getLine(), $e->getFile());
            if($conf['allowdebug']) msg('<pre>' . hsc($e->getTraceAsString()) . '</pre>', -1);
        }

        return true;
    }
}
