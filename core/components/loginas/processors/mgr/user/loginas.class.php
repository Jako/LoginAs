<?php
/**
 * Login user
 *
 * @package loginas
 * @subpackage processors
 */

use TreehillStudio\LoginAs\LoginAs;

// Compatibility between 2.x/3.x
if (file_exists(MODX_PROCESSORS_PATH . 'security/login.class.php')) {
    require_once MODX_PROCESSORS_PATH . 'security/login.class.php';
} elseif (!class_exists('modSecurityLoginProcessor')) {
    class_alias(\MODX\Revolution\Processors\Security\Login::class, \modSecurityLoginProcessor::class);
}

class LoginAsLoginProcessor extends modSecurityLoginProcessor
{
    public $permission = 'loginas_loginas';

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

    public function initialize()
    {
        $this->setProperty('login_context', $this->loginas->getOption('loginContext'));
        $this->setProperty('add_contexts', $this->loginas->getOption('addContexts'));
        $this->setProperty('password', 'loginas');

        return parent::initialize();
    }

    public function fireOnAuthenticationEvent()
    {
        return true;
    }

}

return 'LoginAsLoginProcessor';
