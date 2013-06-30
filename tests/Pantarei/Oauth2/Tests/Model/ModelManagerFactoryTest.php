<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests\Model;

use Pantarei\Oauth2\Model\ModelInterface;
use Pantarei\Oauth2\Model\ModelManagerFactory;
use Pantarei\Oauth2\Model\ModelManagerInterface;

class FooModelManager
{
}

class BarModelManager implements ModelManagerInterface
{
    public function getClass()
    {
    }
}

class ModelManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Pantarei\Oauth2\Exception\ServerErrorException
     */
    public function testBadAddModelManager()
    {
        $modelManagerFactory = new ModelManagerFactory(array(
            'foo' => new FooModelManager(),
        ));
        $modelManagerFactory->addModelManager('foo', $modelManager);
    }

    /**
     * @expectedException \Pantarei\Oauth2\Exception\ServerErrorException
     */
    public function testBadGetModelManager()
    {
        $modelManagerFactory = new ModelManagerFactory(array(
            'bar' => new BarModelManager(),
        ));
        $modelManagerFactory->getModelManager('foo');
    }

    public function testGoodGetModelManager()
    {
        $modelManagerFactory = new ModelManagerFactory(array(
            'bar' => new BarModelManager(),
        ));
        $modelManagerFactory->getModelManager('bar');
    }
}
