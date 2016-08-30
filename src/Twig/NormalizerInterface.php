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
 * Basic loader using absolute paths.
 */
interface NormalizerInterface
{


    /**
     * Normalize the Twig template name to a name the ViewFinder can use
     *
     * @param  string $name Template file name.
     * @return string The parsed name
     */
    public function normalizeName($name);
}
