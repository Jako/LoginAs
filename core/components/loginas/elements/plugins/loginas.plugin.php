<?php
/**
 * LoginAs Plugin
 *
 * @package loginas
 * @subpackage plugin
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

$className = 'TreehillStudio\LoginAs\Plugins\Events\\' . $modx->event->name;

$corePath = $modx->getOption('loginas.core_path', null, $modx->getOption('core_path') . 'components/loginas/');
/** @var LoginAs $loginas */
$loginas = $modx->getService('loginas', 'LoginAs', $corePath . 'model/loginas/', [
    'core_path' => $corePath
]);

if ($loginas) {
    if (class_exists($className)) {
        $handler = new $className($modx, $scriptProperties);
        if (get_class($handler) == $className) {
            $handler->run();
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' could not be initialized!', '', 'LoginAs Plugin');
        }
    } else {
        $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' was not found!', '', 'LoginAs Plugin');
    }
}

return;
