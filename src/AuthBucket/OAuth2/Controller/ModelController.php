<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Controller;

use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * OAuth2 model endpocontroller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ModelController
{
    protected $validator;
    protected $serializer;
    protected $modelManagerFactory;

    public function __construct(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->modelManagerFactory = $modelManagerFactory;
    }

    public function createModelAction(Request $request, $type)
    {
        $format = $request->getRequestFormat();

        $modelManager = $this->modelManagerFactory->getModelManager($type);
        $model = $this->serializer->deserialize(
            $request->getContent(),
            $modelManager->getClassName(),
            $format
        );
        $model = $modelManager->createModel($model);

        return new Response($this->serializer->serialize($model, $format), 200, array(
            "Content-Type" => $request->getMimeType($format),
        ));
    }

    public function readModelAction(Request $request, $type, $id)
    {
        $format = $request->getRequestFormat();

        $modelManager = $this->modelManagerFactory->getModelManager($type);
        $model = $modelManager->readModelOneBy(array('id' => $id));

        return new Response($this->serializer->serialize($model, $format), 200, array(
            "Content-Type" => $request->getMimeType($format),
        ));
    }

    public function readModelAllAction(Request $request, $type)
    {
        $format = $request->getRequestFormat();

        $modelManager = $this->modelManagerFactory->getModelManager($type);
        $model = $modelManager->readModelAll();

        return new Response($this->serializer->serialize($model, $format), 200, array(
            "Content-Type" => $request->getMimeType($format),
        ));
    }

    public function updateModelAction(Request $request, $type, $id)
    {

    }

    public function deleteModelAction($type, $id)
    {

    }
}
