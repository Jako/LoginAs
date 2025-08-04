<?php
/**
 * @package loginas
 * @subpackage plugin
 */

namespace TreehillStudio\LoginAs\Plugins\Events;

use TreehillStudio\LoginAs\Plugins\Plugin;

class OnWebAuthentication extends Plugin
{
    public function process()
    {
        $password = $this->scriptProperties['password'];

        if ($this->modx->user->hasSessionContext('mgr') && $this->modx->hasPermission('loginas_loginas') && $password === 'loginas') {
            $this->modx->event->output(true);
        }
    }
}
