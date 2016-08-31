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


/**
 * Basic template name normalizer.
 */
class Normalizer
{

    /**
     * @var string Twig file extension.
     */
    protected $extensions = [];

    /**
     * @param array                                $extension Twig file extension.
     */
    public function __construct(array $extensions)
    {
        foreach (array_unique($extensions) as $ext) {
            $this->extensions[$ext] = [
                'value' => '.' . $ext,
                'position' => -(strlen($ext) + 1)
            ];
        }
    }

    /**
     * Normalize the Twig template name to a name the ViewFinder can use
     *
     * @param  string $name Template file name.
     * @return string The parsed name
     */
    public function normalizeName($name)
    {
        foreach ($this->extensions as $config) {
            if (substr($name, $config['position']) === $config['value']) {
                return substr($name, 0, $config['position']);
            }
        }

        return $name;
    }
}