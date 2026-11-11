<?php
/**
 * LoginAs
 *
 * Copyright 2025-2026 by Thomas Jakobi <office@treehillstudio.com>
 *
 * @package loginas
 * @subpackage classfile
 */

namespace TreehillStudio\LoginAs;

use modX;

/**
 * Class LoginAs
 */
class LoginAs
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'loginas';

    /**
     * The package name
     * @var string $packageName
     */
    public $packageName = 'LoginAs';

    /**
     * The version
     * @var string $version
     */
    public $version = '1.1.0';

    /**
     * The class options
     * @var array $options
     */
    public $options = [];

    /**
     * LoginAs constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    public function __construct(modX &$modx, $options = [])
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/' . $this->namespace . '/');
        $modxversion = $this->modx->getVersionData();

        // Load some default paths for easier management
        $this->options = array_merge([
            'namespace' => $this->namespace,
            'version' => $this->version,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ], $options);

        $lexicon = $this->modx->getService('lexicon', 'modLexicon');
        $lexicon->load($this->namespace . ':default');

        $this->packageName = $this->modx->lexicon('loginas');

        // Add default options
        $this->options = array_merge($this->options, [
            'debug' => (bool)$this->getOption('debug', $options, false),
            'modxversion' => $modxversion['version'],
            'targetUrl' => $this->getTargetUrl($options),
            'loginContext' => $this->getOption('login_context', $options, 'web'),
            'addContexts' => $this->getOption('add_contexts', $options, ''),
        ]);
    }

    /**
     * Get the URL of the resource the frontend login is redirected to.
     *
     * The context of the resource is passed to makeUrl, otherwise MODX logs a
     * warning, if the URL is generated in the manager context.
     *
     * @param array $options An array of options that override local options.
     * @return string
     */
    private function getTargetUrl($options = [])
    {
        // Fall back to the site start, if the login_id setting is empty or 0
        $loginId = (int)$this->getOption('login_id', $options, 0);
        if (!$loginId) {
            $loginId = (int)$this->modx->getOption('site_start');
        }
        if (!$loginId) {
            return '';
        }

        $c = $this->modx->newQuery('modResource', ['id' => $loginId]);
        $c->select('context_key');
        $contextKey = (string)$this->modx->getValue($c->prepare());
        if ($contextKey === '') {
            return '';
        }

        return $this->modx->makeUrl($loginId, $contextKey, '', 'full');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = [], $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("$this->namespace.$key", $this->modx->config)) {
                $option = $this->modx->getOption("$this->namespace.$key");
            }
        }
        return $option;
    }
}
