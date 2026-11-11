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
        $password = $this->scriptProperties['password'] ?? '';
        if ($password !== 'loginas') {
            return;
        }

        // Never authenticate a login into the manager context
        $loginContext = $this->scriptProperties['loginContext'] ?? '';
        $addContexts = $this->scriptProperties['addContexts'] ?? [];
        $addContexts = is_array($addContexts) ? $addContexts : explode(',', (string)$addContexts);
        if ($loginContext === 'mgr' || in_array('mgr', array_map('trim', $addContexts), true)) {
            return;
        }

        // The current request has to belong to an active manager user with the loginas_loginas permission in the manager context
        $managerUser = $this->getManagerUser();
        if (!$managerUser) {
            return;
        }
        $managerContext = $this->modx->getContext('mgr');
        if (!$managerContext || !$managerContext->checkPolicy('loginas_loginas', null, $managerUser)) {
            return;
        }

        // Only sudo users are allowed to log in as a sudo user
        $user = $this->scriptProperties['user'] ?? null;
        if (!is_object($user) || ($user->get('sudo') && !$managerUser->get('sudo'))) {
            return;
        }

        $this->modx->event->output(true);
    }

    /**
     * Get the active manager user of the current session.
     *
     * @return \modUser|null
     */
    private function getManagerUser()
    {
        $userId = (int)($_SESSION['modx.user.contextTokens']['mgr'] ?? 0);
        if (!$userId) {
            return null;
        }
        /** @var \modUser $user */
        $user = $this->modx->getObject('modUser', $userId);
        if (!$user || !$user->get('active') || !$user->hasSessionContext('mgr')) {
            return null;
        }
        $profile = $user->getOne('Profile');
        if (!$profile || $profile->get('blocked') || $profile->get('blockeduntil') > time() ||
            ($profile->get('blockedafter') > 0 && $profile->get('blockedafter') < time())
        ) {
            return null;
        }
        return $user;
    }
}
