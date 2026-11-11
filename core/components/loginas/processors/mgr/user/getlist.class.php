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

    /**
     * The list is used in the LoginAs window, so it needs the loginas_loginas
     * permission instead of the view_user permission of the parent processor.
     *
     * @return bool
     */
    public function checkPermissions()
    {
        return $this->modx->hasPermission('loginas_loginas');
    }

    /**
     * List only active users that can be selected by the current user. If the manager
     * property is set, list only manager users (sudo users and users with a user group
     * access to the manager context), otherwise list only web users.
     *
     * {@inheritDoc}
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c = parent::prepareQueryBeforeCount($c);
        $alias = $c->getAlias();

        $c->where([
            $alias . '.active' => true,
        ]);
        if (!$this->modx->user->get('sudo')) {
            $c->where([
                $alias . '.sudo' => false,
            ]);
        }

        // Users with a user group access to the manager context. Like the MODX ACL loading,
        // members without an existing role don't get the access of the user group.
        $memberTable = $this->modx->getTableName('modUserGroupMember');
        $roleTable = $this->modx->getTableName('modUserGroupRole');
        $aclTable = $this->modx->getTableName('modAccessContext');
        $principalClasses = implode(',', [
            $this->modx->quote('modUserGroup'),
            $this->modx->quote('MODX\Revolution\modUserGroup'),
        ]);
        $managerUsers = "SELECT `Member`.`member` FROM {$memberTable} AS `Member`
            INNER JOIN {$roleTable} AS `Role` ON `Role`.`id` = `Member`.`role`
            INNER JOIN {$aclTable} AS `Acl` ON `Acl`.`principal` = `Member`.`user_group`
                AND `Acl`.`target` = 'mgr'
                AND `Acl`.`principal_class` IN ({$principalClasses})
                AND `Role`.`authority` <= `Acl`.`authority`";

        if (filter_var($this->getProperty('manager', false), FILTER_VALIDATE_BOOLEAN)) {
            // Manager users without the current user
            $c->where("(`{$alias}`.`sudo` = 1 OR `{$alias}`.`id` IN ({$managerUsers}))");
            $c->where([
                $alias . '.id:!=' => $this->modx->user->get('id'),
            ]);
        } else {
            // Web users
            $c->where("(`{$alias}`.`sudo` = 0 AND `{$alias}`.`id` NOT IN ({$managerUsers}))");
        }

        return $c;
    }
}

return 'LoginAsUserGetListProcessor';
