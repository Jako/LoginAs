<?php
/**
 * Abstract remove processor
 *
 * @package loginas
 * @subpackage processors
 */

namespace TreehillStudio\LoginAs\Processors;

use TreehillStudio\LoginAs\LoginAs;
use modObjectRemoveProcessor;
use modX;

/**
 * Class ObjectRemoveProcessor
 */
class ObjectRemoveProcessor extends modObjectRemoveProcessor
{
    public $languageTopics = ['loginas:default'];

    /** @var LoginAs $loginas */
    public $loginas;

    /**
     * {@inheritDoc}
     * @param modX $modx A reference to the modX instance
     * @param array $properties An array of properties
     */
    public function __construct(modX &$modx, array $properties = [])
    {
        parent::__construct($modx, $properties);

        $corePath = $this->modx->getOption('loginas.core_path', null, $this->modx->getOption('core_path') . 'components/loginas/');
        $this->loginas = $this->modx->getService('loginas', 'LoginAs', $corePath . 'model/loginas/');
    }

    /**
     * Get a boolean property.
     * @param string $k
     * @param mixed $default
     * @return bool
     */
    public function getBooleanProperty($k, $default = null)
    {
        return ($this->getProperty($k, $default) === 'true' || $this->getProperty($k, $default) === true || $this->getProperty($k, $default) === '1' || $this->getProperty($k, $default) === 1);
    }
}
