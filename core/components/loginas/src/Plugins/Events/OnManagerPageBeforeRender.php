<?php
/**
 * @package loginas
 * @subpackage plugin
 */

namespace TreehillStudio\LoginAs\Plugins\Events;

use TreehillStudio\LoginAs\Plugins\Plugin;

class OnManagerPageBeforeRender extends Plugin
{
    public function process()
    {
        if ($this->modx->user && $this->modx->user->hasSessionContext('mgr')) {
            $assetsUrl = $this->loginas->getOption('assetsUrl');
            $jsUrl = $this->loginas->getOption('jsUrl') . 'mgr/';
            $jsSourceUrl = $assetsUrl . '../../../source/js/mgr/';

            $this->modx->controller->addLexiconTopic('loginas:default');

            if ($this->loginas->getOption('debug') && ($this->loginas->getOption('assetsUrl') != MODX_ASSETS_URL . 'components/loginas/')) {
                $this->modx->controller->addJavascript($jsSourceUrl . 'loginas.js?v=v' . $this->loginas->version);
                $this->modx->controller->addJavascript($jsSourceUrl . 'helper/combo.js?v=v' . $this->loginas->version);
                $this->modx->controller->addJavascript($jsSourceUrl . 'helper/menu.js?v=v' . $this->loginas->version);
            } else {
                $this->modx->controller->addJavascript($jsUrl . 'loginas.min.js?v=v' . $this->loginas->version);
            }
            $this->modx->controller->addHtml(
                '<script type="text/javascript">
                    Ext.onReady(function() {
                        LoginAs.config = ' . json_encode($this->loginas->options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ';
                    });
                </script>'
            );
        }
    }
}
