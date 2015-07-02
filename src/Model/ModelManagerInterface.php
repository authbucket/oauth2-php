<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Model;

/**
 * OAuth2 model manager interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ModelManagerInterface
{
    public function getClassName();

    public function createModel(ModelInterface $model);

    public function readModelAll();

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    public function readModelOneBy(array $criteria, array $orderBy = null);

    public function updateModel(ModelInterface $model);

    public function deleteModel(ModelInterface $model);
}
