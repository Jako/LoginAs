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
    public $permissionManager = 'loginas_loginas_manager';

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
     * The base processor class does not check $this->permission, so it has to be checked here.
     * A login into the manager context needs an additional permission.
     *
     * @return bool
     */
    public function checkPermissions()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return false;
        }
        if ($this->isManagerLogin() && !$this->modx->hasPermission($this->permissionManager)) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritDoc}
     * @return array
     */
    public function getLanguageTopics()
    {
        return array_merge(parent::getLanguageTopics(), ['loginas:default']);
    }

    /**
     * {@inheritDoc}
     * @return bool|string
     */
    public function initialize()
    {
        if ($this->isManagerLogin()) {
            $loginContext = 'mgr';
            $addContexts = [];
        } else {
            // A frontend login never adds the manager context.
            $loginContext = trim((string)$this->loginas->getOption('loginContext'));
            if ($loginContext === '' || $loginContext === 'mgr') {
                $loginContext = 'web';
            }
            $addContexts = array_filter(array_map('trim', explode(',', (string)$this->loginas->getOption('addContexts'))), function ($context) use ($loginContext) {
                return $context !== '' && $context !== 'mgr' && $context !== $loginContext;
            });
        }

        $this->setProperty('login_context', $loginContext);
        $this->setProperty('add_contexts', implode(',', $addContexts));
        $this->setProperty('password', 'loginas');
        $this->setProperty('rememberme', false);
        $this->setProperty('returnUrl', '');

        return parent::initialize();
    }

    /**
     * Don't fire OnBeforeManagerLogin for a login into the manager context. Plugins on this
     * event check the credentials of the selected user (i.e. a two-factor code), but the
     * login is authenticated by the current manager user and the loginas_loginas_manager permission.
     *
     * @return string
     */
    public function fireOnBeforeLoginEvent()
    {
        if ($this->isMgr) {
            return '';
        }
        return parent::fireOnBeforeLoginEvent();
    }

    /**
     * Load the user and deny logging in as a sudo user, unless the current user is a sudo user.
     * A login into the manager context needs a user with manager access.
     *
     * @return bool|null|string
     */
    public function getUser()
    {
        $result = parent::getUser();
        if (!empty($result)) {
            return $result;
        }
        if ($this->user->get('sudo') && !$this->modx->user->get('sudo')) {
            return $this->modx->lexicon('permission_denied');
        }
        if ($this->isMgr) {
            if ($this->user->get('id') == $this->modx->user->get('id')) {
                return $this->modx->lexicon('loginas.err_login_as_self');
            }
            $managerContext = $this->modx->getContext('mgr');
            if (!$managerContext || !$managerContext->checkPolicy('frames', null, $this->user)) {
                return $this->modx->lexicon('loginas.err_no_manager_access', [
                    'username' => $this->user->get('username')
                ]);
            }
        }
        return false;
    }

    /**
     * Check if the user is inactive or blocked. The parent method would count the
     * dummy password as failed login attempt and could block the user.
     *
     * @return bool|null|string
     */
    public function checkIsBlocked()
    {
        if (!$this->user->get('active')) {
            return $this->modx->lexicon('login_user_inactive');
        }

        /** @var modUserProfile $profile */
        $profile = $this->user->Profile;
        if ($profile->get('blocked')) {
            return $this->modx->lexicon('login_blocked_admin');
        }
        if ($profile->get('blockeduntil') > time()) {
            return $this->modx->lexicon('login_blocked_error');
        }
        if ($profile->get('blockedafter') > 0 && $profile->get('blockedafter') < time()) {
            return $this->modx->lexicon('login_blocked_error');
        }

        return false;
    }

    /**
     * The user is authenticated by the loginas_loginas permission of the current manager user.
     *
     * @return bool
     */
    public function fireOnAuthenticationEvent()
    {
        return true;
    }

    /**
     * Log the login as the current manager user. If a login context currently belongs
     * to another user, that user is logged out of the context, before the selected user
     * is logged in. For a login into the manager context, this is the current manager user.
     *
     * @return array|string
     */
    public function afterLogin()
    {
        // The action has no namespace prefix with a dot, since the manager log would load an xPDO package for that namespace
        $this->modx->logManagerAction($this->isMgr ? 'loginas_login_as_manager' : 'loginas_login_as', 'modUser', $this->user->get('id'));

        // Remove the session values (i.e. the cached user settings) of the previous user of each login context
        $contextTokens = $_SESSION['modx.user.contextTokens'] ?? [];
        foreach (array_merge([$this->loginContext], $this->addContexts) as $context) {
            if (isset($contextTokens[$context]) && (int)$contextTokens[$context] !== (int)$this->user->get('id')) {
                $this->user->removeSessionContextVars($context);
            }
        }

        return parent::afterLogin();
    }

    /**
     * Is a login into the manager context requested?
     *
     * @return bool
     */
    private function isManagerLogin()
    {
        return filter_var($this->getProperty('manager', false), FILTER_VALIDATE_BOOLEAN);
    }
}

return 'LoginAsLoginProcessor';
