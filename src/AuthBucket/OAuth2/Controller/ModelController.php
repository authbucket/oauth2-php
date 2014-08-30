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

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * OAuth2 model endpoint controller implementation.
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

        $errors = $this->validator->validateValue($type, new Regex('/^([a-z0-9\_\-\.]+)$/'));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

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

        $errors = $this->validator->validateValue($type, new Regex('/^([a-z0-9\_\-\.]+)$/'));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        $errors = $this->validator->validateValue($id, new Type('numeric'));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        $modelManager = $this->modelManagerFactory->getModelManager($type);
        $model = $modelManager->readModelOneBy(array('id' => $id));

        return new Response($this->serializer->serialize($model, $format), 200, array(
            "Content-Type" => $request->getMimeType($format),
        ));
    }

    public function updateModelAction(Request $request, $type, $id)
    {
        $format = $request->getRequestFormat();

        $errors = $this->validator->validateValue($type, new Regex('/^([a-z0-9\_\-\.]+)$/'));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        $errors = $this->validator->validateValue($id, new Type('numeric'));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        $modelManager = $this->modelManagerFactory->getModelManager($type);
        $model = $modelManager->readModelOneBy(array('id' => $id));

        $values = $this->serializer->decode($request->getContent(), $format);
        foreach ($values as $key => $value) {
            $setter = 'set'.preg_replace_callback(
                '/(^|_|\.)+(.)/', function ($match) {
                    return ('.' === $match[1] ? '_' : '').strtoupper($match[2]);
                }, $key);

            if (method_exists($model, $setter)) {
                $model->$setter($value);
            }
        }

        $model = $modelManager->updateModel($model);

        return new Response($this->serializer->serialize($model, $format), 200, array(
            "Content-Type" => $request->getMimeType($format),
        ));
    }

    public function deleteModelAction(Request $request, $type, $id)
    {
        $format = $request->getRequestFormat();

        $errors = $this->validator->validateValue($type, new Regex('/^([a-z0-9\_\-\.]+)$/'));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        $errors = $this->validator->validateValue($id, new Type('numeric'));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        $modelManager = $this->modelManagerFactory->getModelManager($type);
        $model = $modelManager->readModelOneBy(array('id' => $id));

        $model = $modelManager->deleteModel($model);

        return new Response($this->serializer->serialize($model, $format), 200, array(
            "Content-Type" => $request->getMimeType($format),
        ));
    }

    public function listModelAction(Request $request, $type)
    {
        $format = $request->getRequestFormat();

        $errors = $this->validator->validateValue($type, new Regex('/^([a-z0-9\_\-\.]+)$/'));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        $modelManager = $this->modelManagerFactory->getModelManager($type);
        $model = $modelManager->readModelAll();

        return new Response($this->serializer->serialize($model, $format), 200, array(
            "Content-Type" => $request->getMimeType($format),
        ));
    }
}
