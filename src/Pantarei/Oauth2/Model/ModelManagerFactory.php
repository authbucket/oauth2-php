<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Model;

use Pantarei\Oauth2\Exception\ServerErrorException;

/**
 * Oauth2 model manager factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ModelManagerFactory implements ModelManagerFactoryInterface
{
    protected $classes;

    public function __construct(array $classes = array())
    {
        foreach ($classes as $class) {
            if (!$class instanceof ModelManagerInterface) {
                throw new ServerErrorException();
            }
        }

        $this->classes = $classes;
    }

    public function getModelManager($type)
    {
        if (!isset($this->classes[$type])) {
            throw new ServerErrorException();
        }

        return $this->classes[$type];
    }
}
