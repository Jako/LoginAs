<?php
/**
 * Abstract plugin
 *
 * @package loginas
 * @subpackage plugin
 */

namespace TreehillStudio\LoginAs\Plugins;

use modX;
use LoginAs;

/**
 * Class Plugin
 */
abstract class Plugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var LoginAs $loginas */
    protected $loginas;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    /**
     * Plugin constructor.
     *
     * @param $modx
     * @param $scriptProperties
     */
    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties = &$scriptProperties;
        $this->modx =& $modx;
        $corePath = $this->modx->getOption('loginas.core_path', null, $this->modx->getOption('core_path') . 'components/loginas/');
        $this->loginas = $this->modx->getService('loginas', 'LoginAs', $corePath . 'model/loginas/', [
            'core_path' => $corePath
        ]);
    }

    /**
     * Run the plugin event.
     */
    public function run()
    {
        $init = $this->init();
        if ($init !== true) {
            return;
        }

        $this->process();
    }

    /**
     * Initialize the plugin event.
     *
     * @return bool
     */
    public function init()
    {
        return true;
    }

    /**
     * Process the plugin event code.
     *
     * @return mixed
     */
    abstract public function process();
}
