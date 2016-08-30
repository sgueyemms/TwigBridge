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

use Twig_Error_Loader;
use Illuminate\Filesystem\Filesystem;

/**
 * Template name normalizer for files like *.html.twig.
 */
class SymfonyNormalizer extends Normalizer
{
    /**
     * @var string Twig file extension.
     */
    protected $extension;

    /**
     * @var string Twig file extension.
     */
    protected $extensionLength;

    /**
     * @param string                                $extension Twig file extension.
     */
    public function __construct($extension = 'twig')
    {
        $this->extension = '.'.$extension;
        $this->extensionLength = strlen($this->extension);
    }

    /**
     * Normalize the Twig template name to a name the ViewFinder can use
     *
     * @param  string $name Template file name.
     * @return string The parsed name
     */
    public function normalizeName($name)
    {
        if (substr($name, -$this->extensionLength) === $this->extension) {
            $name = substr($name, 0, -$this->extensionLength);
        }

        return $name;
    }
}
