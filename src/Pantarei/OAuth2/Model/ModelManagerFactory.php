<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Model;

use Pantarei\OAuth2\Exception\ServerErrorException;

class ModelManagerFactory implements ModelManagerFactoryInterface
{
    private $modelManagers;

    public function __construct()
    {
        $this->modelManagers = array();
    }

    public function addModelManager($type, $manager)
    {
        if (!$manager instanceof ModelManagerInterface) {
            throw new ServerErrorException();
        }

        $this->modelManagers[$type] = $manager;
    }

    public function getModelManager($type)
    {
        if (!isset($this->modelManagers[$type])) {
            throw new ServerErrorException();
        }

        if (!$this->modelManagers[$type] instanceof ModelManagerInterface) {
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
