<?php
/**
 * DokuWiki Plugin bez (Action Component)
 *
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_bez_maildebug extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('MAIL_MESSAGE_SEND', 'BEFORE', $this, 'handle_debug');
    }

    /**
     * [Custom event handler which performs action]
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function handle_debug(Doku_Event &$event, $param) {
        global $dryrun;

        if ($dryrun) {
            $event->preventDefault();

            $mail = $event->data['mail'];
            $reflection = new \ReflectionClass($mail);
            $property = $reflection->getProperty('html');
            $property->setAccessible(true);
            $html = $property->getValue($mail);
            echo "To: " . $event->data['to'] . "\n";
            echo $html;
            echo "\n\n";
        }
    }
}
