<?php
/**
 * Resolve access permissions
 *
 * @package loginas
 * @subpackage build
 *
 * @var array $options
 * @var xPDOTransport $transport
 */

$accessTemplate = [
    'name' => 'LoginAsTemplate',
    'description' => 'LoginAs Policy Template with all attributes.',
    'lexicon' => $options['namespace'] . ':permissions',
    'template_group' => '1',
    'permissions' => [
        'loginas_loginas', // modx->lexicon('loginas.permission.loginas_loginas_desc');
        'loginas_loginas_manager', // modx->lexicon('loginas.permission.loginas_loginas_manager_desc');
    ]
];

/**
 * The permission values of the access policies. A permission that is missing in an
 * existing policy is added with this value, an existing value is not changed. So the
 * loginas_loginas_manager permission is not granted by an upgrade of the LoginAs policy.
 */
$accessPolicies = [
    [
        'name' => 'LoginAs',
        'description' => 'LoginAs Access Policy to log into frontend accounts.',
        'lexicon' => $options['namespace'] . ':permissions',
        'permissions' => [
            'loginas_loginas' => true,
            'loginas_loginas_manager' => false,
        ]
    ],
    [
        'name' => 'LoginAs Manager',
        'description' => 'LoginAs Access Policy to log into frontend and manager accounts.',
        'lexicon' => $options['namespace'] . ':permissions',
        'permissions' => [
            'loginas_loginas' => true,
            'loginas_loginas_manager' => true,
        ]
    ]
];

if (!function_exists('loginas_createAccessPolicyTemplate')) {
    /**
     * Create or update the access policy template and its permissions.
     *
     * @param modX $modx
     * @param array $template
     * @return modAccessPolicyTemplate|null
     */
    function loginas_createAccessPolicyTemplate($modx, $template)
    {
        /** @var modAccessPolicyTemplate $accessPolicyTemplate */
        if (!$accessPolicyTemplate = $modx->getObject('modAccessPolicyTemplate', [
            'name' => $template['name']
        ])) {
            $accessPolicyTemplate = $modx->newObject('modAccessPolicyTemplate');
            $accessPolicyTemplate->fromArray([
                'name' => $template['name'],
                'description' => $template['description'],
                'lexicon' => $template['lexicon'],
                'template_group' => $template['template_group']
            ]);
            if (!$accessPolicyTemplate->save()) {
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'Access Policy Template "' . $template['name'] . '" could not be created.');
                return null;
            }
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Access Policy Template "' . $template['name'] . '" created.');
        }

        foreach ($template['permissions'] as $permission) {
            /** @var modAccessPermission $accessPermission */
            if (!$accessPermission = $modx->getObject('modAccessPermission', [
                'name' => $permission,
                'template' => $accessPolicyTemplate->get('id')
            ])) {
                $accessPermission = $modx->newObject('modAccessPermission');
                $accessPermission->fromArray([
                    'name' => $permission,
                    'value' => '1'
                ]);
                $accessPermission->addOne($accessPolicyTemplate, 'Template');
                $modx->log(xPDO::LOG_LEVEL_INFO, 'Access Permission "' . $permission . '" created.');
            } else {
                $modx->log(xPDO::LOG_LEVEL_INFO, 'Access Permission "' . $permission . '" updated.');
            }
            $accessPermission->set('description', 'loginas.permission.' . $permission . '_desc');
            $accessPermission->save();
        }

        return $accessPolicyTemplate;
    }
}

if (!function_exists('loginas_createAccessPolicy')) {
    /**
     * Create or update an access policy. Existing permission values are not changed.
     *
     * @param modX $modx
     * @param modAccessPolicyTemplate $accessPolicyTemplate
     * @param array $policy
     * @return bool
     */
    function loginas_createAccessPolicy($modx, $accessPolicyTemplate, $policy)
    {
        /** @var modAccessPolicy $accessPolicy */
        if (!$accessPolicy = $modx->getObject('modAccessPolicy', [
            'name' => $policy['name']
        ])) {
            $accessPolicy = $modx->newObject('modAccessPolicy');
            $accessPolicy->fromArray([
                'name' => $policy['name'],
                'description' => $policy['description'],
                'data' => $policy['permissions'],
                'lexicon' => $policy['lexicon']
            ]);
            $accessPolicy->addOne($accessPolicyTemplate, 'Template');
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Access Policy "' . $policy['name'] . '" created.');
        } else {
            $data = $accessPolicy->get('data');
            $data = is_array($data) ? $data : [];
            $accessPolicy->set('data', array_merge($policy['permissions'], $data));
            $accessPolicy->set('description', $policy['description']);
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Access Policy "' . $policy['name'] . '" updated.');
        }
        return $accessPolicy->save();
    }
}

if (!function_exists('loginas_removeAccessPolicies')) {
    /**
     * Remove the access policies, the access policy template and its permissions.
     *
     * @param modX $modx
     * @param array $template
     * @param array $policies
     * @return bool
     */
    function loginas_removeAccessPolicies($modx, $template, $policies)
    {
        foreach ($policies as $policy) {
            /** @var modAccessPolicy $accessPolicy */
            if ($accessPolicy = $modx->getObject('modAccessPolicy', ['name' => $policy['name']])) {
                $accessPolicy->remove();
                $modx->log(xPDO::LOG_LEVEL_INFO, 'Access Policy "' . $policy['name'] . '" removed.');
            }
        }
        /** @var modAccessPolicyTemplate $accessPolicyTemplate */
        if ($accessPolicyTemplate = $modx->getObject('modAccessPolicyTemplate', ['name' => $template['name']])) {
            foreach ($template['permissions'] as $permission) {
                /** @var modAccessPermission $accessPermission */
                if ($accessPermission = $modx->getObject('modAccessPermission', [
                    'name' => $permission,
                    'template' => $accessPolicyTemplate->get('id')
                ])) {
                    $accessPermission->remove();
                    $modx->log(xPDO::LOG_LEVEL_INFO, 'Access Permission "' . $permission . '" removed.');
                }
            }
            $accessPolicyTemplate->remove();
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Access Policy Template "' . $template['name'] . '" removed.');
        }
        return true;
    }
}

/** @var modX $modx */
$modx = $transport->xpdo;

$success = true;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $accessPolicyTemplate = loginas_createAccessPolicyTemplate($modx, $accessTemplate);
        if (!$accessPolicyTemplate) {
            $success = false;
            break;
        }
        foreach ($accessPolicies as $accessPolicy) {
            $success = loginas_createAccessPolicy($modx, $accessPolicyTemplate, $accessPolicy) && $success;
        }
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        $success = loginas_removeAccessPolicies($modx, $accessTemplate, $accessPolicies);
        break;
}
return $success;
