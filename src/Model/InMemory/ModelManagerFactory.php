<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Model\InMemory;

use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Model\ModelManagerInterface;

/**
 * OAuth2 model manager factory in-memory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ModelManagerFactory implements ModelManagerFactoryInterface
{
    protected $managers;

    public function __construct(array $models = array())
    {
        $managers = array();

        foreach ($models as $type => $model) {
            $className = $model.'Manager';
            $manager = new $className();
            if (!$manager instanceof ModelManagerInterface) {
                throw new ServerErrorException();
            }
            $managers[$type] = $manager;
        }

        $this->managers = $managers;
    }

    public function getModelManager($type)
    {
        if (!isset($this->managers[$type])) {
            throw new ServerErrorException();
        }

        return $this->managers[$type];
    }
}
