<?php

/**
 * This file is part of the authbucket/oauth2-php package.
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
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * Authorize endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizeController
{
    protected $validator;
    protected $serializer;
    protected $modelManagerFactory;

    public function __construct(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        ModelManagerFactoryInterface $modelManagerFactory
    ) {
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->modelManagerFactory = $modelManagerFactory;
    }

    public function createAction(Request $request)
    {
        $format = $request->getRequestFormat();

        $modelManager = $this->modelManagerFactory->getModelManager('authorize');
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

    public function readAction(Request $request, $id)
    {
        $format = $request->getRequestFormat();

        $modelManager = $this->modelManagerFactory->getModelManager('authorize');
        $model = $modelManager->readModelOneBy(array('id' => (int) $id));

        return new Response($this->serializer->serialize($model, $format), 200, array(
            "Content-Type" => $request->getMimeType($format),
        ));
    }

    public function updateAction(Request $request, $id)
    {
        $format = $request->getRequestFormat();

        $modelManager = $this->modelManagerFactory->getModelManager('authorize');
        $model = $modelManager->readModelOneBy(array('id' => (int) $id));

        $values = $this->serializer->decode($request->getContent(), $format);
        foreach ($values as $key => $value) {
            $setter = 'set'.ucfirst($key);
            if (method_exists($model, $setter)) {
                $model->$setter($value);
            }
        }

        $model = $modelManager->updateModel($model);

        return new Response($this->serializer->serialize($model, $format), 200, array(
            "Content-Type" => $request->getMimeType($format),
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        $format = $request->getRequestFormat();

        $modelManager = $this->modelManagerFactory->getModelManager('authorize');
        $model = $modelManager->readModelOneBy(array('id' => (int) $id));

        $model = $modelManager->deleteModel($model);

        return new Response($this->serializer->serialize($model, $format), 200, array(
            "Content-Type" => $request->getMimeType($format),
        ));
    }

    public function listAction(Request $request)
    {
        $format = $request->getRequestFormat();

        $modelManager = $this->modelManagerFactory->getModelManager('authorize');
        $model = $modelManager->readModelAll();

        return new Response($this->serializer->serialize($model, $format), 200, array(
            "Content-Type" => $request->getMimeType($format),
        ));
    }
}
