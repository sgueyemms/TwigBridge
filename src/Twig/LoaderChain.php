<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Saidou Gueye <sgueye@mmseducation.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Twig;

use Twig_LoaderInterface;
use Twig_Loader_Chain;
use Twig_Error_Loader;

/**
 * Basic loader using absolute paths.
 */
class LoaderChain extends Twig_Loader_Chain implements LoaderInterface
{


    /**
     * Adds a loader instance.
     *
     * @param Twig_LoaderInterface $loader A Loader instance
     */
    public function addLoader(Twig_LoaderInterface $loader)
    {
        if(!$loader instanceof LoaderInterface) {
            throw new \InvalidArgumentException("Loader must be an instance of ".LoaderInterface::class);
        }
        return parent::addLoader($loader);
    }

    /**
     * Return path to template without the need for the extension.
     *
     * @param string $name Template file name or path.
     *
     * @throws \Twig_Error_Loader
     * @return string Path to template
     */
    public function findTemplate($name)
    {
        $exceptions = array();
        foreach ($this->loaders as $loader) {
            try {
                return $loader->findTemplate($name);
            } catch(Twig_Error_Loader $ex) {
                $exceptions[] = $ex->getMessage();
            }
        }
        pyk_printd([
            count($this->loaders),
            $exceptions,
        ]);
        throw new Twig_Error_Loader(sprintf(
            'Template "%s" is not defined%s.',
            $name,
            $exceptions ? ' ('.implode(', ', $exceptions).')' : ''
        ));
    }
}
