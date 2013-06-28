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
    protected $modelManagers;

    public function __construct()
    {
        $this->modelManagers = array();
    }

    public function addModelManager($type, ModelManagerInterface $manager)
    {
        $this->modelManagers[$type] = $manager;
    }

    public function getModelManager($type)
    {
        if (!isset($this->modelManagers[$type])) {
            throw new ServerErrorException();
        }

        return $this->modelManagers[$type];
    }

    public function removeModelManager($type)
    {
        if (!isset($this->modelManagers[$type])) {
            return;
        }

        unset($this->modelManagers[$type]);
    }
}
