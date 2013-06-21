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

interface ModelManagerInterface
{
    public function getModelName();

    public function createModel();

    public function deleteModel(ModelInterface $model);

    public function reloadModel(ModelInterface $model);

    public function updatemodel(ModelInterface $model);
}
