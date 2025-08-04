<?php
/**
 * Get list users
 *
 * @package loginas
 * @subpackage processors
 */

use TreehillStudio\LoginAs\LoginAs;

// Compatibility between 2.x/3.x
if (file_exists(MODX_PROCESSORS_PATH . 'security/user/getlist.class.php')) {
    require_once MODX_PROCESSORS_PATH . 'security/user/getlist.class.php';
} elseif (!class_exists('modUserGetListProcessor')) {
    class_alias(\MODX\Revolution\Processors\Security\User\GetList::class, \modUserGetListProcessor::class);
}

class LoginAsUserGetListProcessor extends modUserGetListProcessor
{
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
}

return 'LoginAsUserGetListProcessor';
