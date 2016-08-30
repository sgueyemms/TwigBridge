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
 * Basic template name normalizer.
 */
class Normalizer implements NormalizerInterface
{

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * @var string Twig file extension.
     */
    protected $extension;

    /**
     * @param \Illuminate\Filesystem\Filesystem     $files     The filesystem
     * @param string                                $extension Twig file extension.
     */
    public function __construct(Filesystem $files, $extension = 'twig')
    {
        $this->files     = $files;
        $this->extension = $extension;
    }

    /**
     * Normalize the Twig template name to a name the ViewFinder can use
     *
     * @param  string $name Template file name.
     * @return string The parsed name
     */
    public function normalizeName($name)
    {
        if ($this->files->extension($name) === $this->extension) {
            $name = substr($name, 0, -(strlen($this->extension) + 1));
        }

        return $name;
    }
}
