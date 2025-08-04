<?php
/**
 * LoginAs connector
 *
 * @package loginas
 * @subpackage connector
 *
 * @var modX $modx
 */

require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('loginas.core_path', null, $modx->getOption('core_path') . 'components/loginas/');
/** @var LoginAs $loginas */
$loginas = $modx->getService('loginas', 'LoginAs', $corePath . 'model/loginas/', [
    'core_path' => $corePath
]);

// Handle request
$modx->request->handleRequest([
    'processors_path' => $loginas->getOption('processorsPath'),
    'location' => ''
]);
