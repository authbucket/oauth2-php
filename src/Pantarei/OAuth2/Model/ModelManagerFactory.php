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

    public function __construct(array $modelManagers)
    {
        $this->modelManagers = $modelManagers;
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
}
