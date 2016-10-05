<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge;

use Twig_Environment;
use Twig_LoaderInterface;
use Illuminate\Contracts\Container\Container; 
use Illuminate\View\ViewFinderInterface;
use InvalidArgumentException;
use Twig_Error;
use TwigBridge\Twig\Normalizer;

/**
 * Bridge functions between Laravel & Twig
 */
class Bridge extends Twig_Environment
{
    /**
     * @var string TwigBridge version
     */
    const BRIDGE_VERSION = '0.10.0';

    /**
     * @var Normalizer
     */
    protected $normalizer;

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * @var array
     */
    private $extensionAliases = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(Normalizer $normalizer, Twig_LoaderInterface $loader, $options = [], Container $app = null)
    {
        // Twig 2.0 doesn't support `true` anymore
        if (isset($options['autoescape']) && $options['autoescape'] === true) {
            $options['autoescape'] = 'html';
        }
        
        parent::__construct($loader, $options);

        $this->normalizer = $normalizer;
        $this->app        = $app;
    }

    /**
     * Get the Laravel app.
     *
     * @return \Illuminate\Contracts\Container\Container
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Set the Laravel app.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    public function setApplication(Container $app)
    {
        $this->app = $app;
    }

    public function loadTemplate($name, $index = null)
    {
        $template = parent::loadTemplate($name, $index);

        $template->setName($this->normalizeName($name));

        return $template;
    }

    /**
     * Lint (check) the syntax of a file on the view paths.
     *
     * @param string $file File to check. Supports dot-syntax.
     *
     * @return bool Whether the file passed or not.
     */
    public function lint($file)
    {
        $template = $this->app['twig.loader.viewfinder']->getSource($file);

        if (!$template) {
            throw new InvalidArgumentException('Unable to find file: '.$file);
        }

        try {
            $this->parse($this->tokenize($template, $file));
        } catch (Twig_Error $e) {
            return false;
        }

        return true;
    }

    /**
     * Merges a context with the shared variables, same as mergeGlobals()
     *
     * @param array $context An array representing the context
     *
     * @return array The context merged with the globals
     */
    public function mergeShared(array $context)
    {
        // we don't use array_merge as the context being generally
        // bigger than globals, this code is faster.
        foreach ($this->app['view']->getShared() as $key => $value) {
            if (!array_key_exists($key, $context)) {
                $context[$key] = $value;
            }
        }

        return $context;
    }

    /**
     * Normalize a view name.
     *
     * @param  string $name
     *
     * @return string
     */
    protected function normalizeName($name)
    {
        $name = $this->normalizer->normalizeName($name);

        // Normalize namespace and delimiters
        $delimiter = ViewFinderInterface::HINT_PATH_DELIMITER;
        if (strpos($name, $delimiter) === false) {
            return str_replace('/', '.', $name);
        }

        list($namespace, $name) = explode($delimiter, $name);

        return $namespace.$delimiter.str_replace('/', '.', $name);
    }

    /**
     * @param $extensionName
     * @param $aliasName
     * @return $this
     */
    public function addExtensionAlias($extensionName, $aliasName)
    {
        $this->extensionAliases[$aliasName] = $extensionName;
        return $this;
    }

    /**
     * @param string $class
     * @return \Twig_ExtensionInterface
     */
    public function getExtension($class)
    {
        if(!empty($this->extensionAliases[$class])) {
            $class = $this->extensionAliases[$class];
        }
        return parent::getExtension($class);
    }
}
