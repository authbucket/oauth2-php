<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Entity;

use Doctrine\ORM\EntityRepository;
use Pantarei\OAuth2\Model\ModelInterface;
use Pantarei\OAuth2\Model\ModelManagerInterface;

abstract class AbstractModelManager extends EntityRepository implements ModelManagerInterface
{
    public function getModelName()
    {
        return $this->getClassName();
    }

    public function createModel()
    {
        $class = $this->getModelName();
        return new $class();
    }

    public function deleteModel(ModelInterface $model)
    {
        $this->getEntityManager()->remove($model);
        $this->getEntityManager()->flush();
    }

    public function reloadModel(ModelInterface $model)
    {
        $this->getEntityManager()->refresh($model);
    }

    public function updatemodel(ModelInterface $model)
    {
        $this->getEntityManager()->persist($model);
        $this->getEntityManager()->flush();
    }
}
