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

interface CodeManagerInterface extends ModelManagerInterface
{
    public function createCode();

    public function deleteCode(CodeInterface $code);

    public function findCodeByCode($code);

    public function reloadCode(CodeInterface $code);

    public function updateCode(CodeInterface $code);
}
