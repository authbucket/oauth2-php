<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Model;

use Pantarei\OAuth2\Model\ModelManagerFactory;
use Pantarei\OAuth2\Model\ModelManagerInterface;

class FooModelManager
{
}

class BarModelManager implements ModelManagerInterface
{
}

class ModelManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testBadAddModelManager()
    {
        $modelManagerFactory = new ModelManagerFactory();
        $modelManager = new FooModelManager();
        $modelManagerFactory->addModelManager('foo', $modelManager);
    }

    public function testGoodAddModelManager()
    {
        $modelManagerFactory = new ModelManagerFactory();
        $modelManager = new BarModelManager();
        $modelManagerFactory->addModelManager('foo', $modelManager);
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\ServerErrorException
     */
    public function testBadGetModelManager()
    {
        $modelManagerFactory = new ModelManagerFactory();
        $modelManager = new BarModelManager();
        $modelManagerFactory->addModelManager('bar', $modelManager);
        $modelManagerFactory->getModelManager('foo');
    }

    public function testGoodGetModelManager()
    {
        $modelManagerFactory = new ModelManagerFactory();
        $modelManager = new BarModelManager();
        $modelManagerFactory->addModelManager('bar', $modelManager);
        $modelManagerFactory->getModelManager('bar');
    }

    public function testBadRemoveModelManager()
    {
        $modelManagerFactory = new ModelManagerFactory();
        $modelManager = new BarModelManager();
        $modelManagerFactory->addModelManager('bar', $modelManager);
        $modelManagerFactory->getModelManager('bar');
        $modelManagerFactory->removeModelManager('foo');
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\ServerErrorException
     */
    public function testGoodRemoveModelManager()
    {
        $modelManagerFactory = new ModelManagerFactory();
        $modelManager = new BarModelManager();
        $modelManagerFactory->addModelManager('bar', $modelManager);
        $modelManagerFactory->getModelManager('bar');
        $modelManagerFactory->removeModelManager('bar');
        $modelManagerFactory->getModelManager('bar');
    }
}
