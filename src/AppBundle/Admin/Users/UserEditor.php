<?php

namespace AppBundle\Admin\Users;

use AppBundle\Model\User;
use AppBundle\Model\UserQuery;
use Creonit\AdminBundle\Component\EditorComponent;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;

class UserEditor extends EditorComponent
{
    protected $emailChanged;

    /**
     * @title Пользователь
     * @entity User
     *
     * @field name {required: true, constraints: [Length({max: 100})]}
     * @field email {required: true, constraints: [Email(), Length({max: 100})]}
     * @field password_raw {constraints: [Length({min: 6})]}
     * @field created_at:date
     *
     * @template
     * {{ name | text | group('Имя') }}
     * {{ email | text | group('Email') }}
     * {{ password_raw | text | group(_key ? 'Изменить пароль' : 'Пароль') }}
     * {{ created_at | input('datetime') | group('Дата создания') }}
     *
     */
    public function schema()
    {
    }

    /**
     * @param ComponentRequest $request
     * @param ComponentResponse $response
     * @param User $entity
     */
    public function validate(ComponentRequest $request, ComponentResponse $response, $entity)
    {
        $authorization = $this->container->get('app.authorization');
        $sign = $authorization->findUserSign('email', $request->data->get('email'));

        if ($entity->isNew() && !$request->data->get('password_raw')) {
            $response->error('Установите пароль пользователю', 'password_raw');
        }

        if ($sign) {
            if ($entity->isNew() or ($entity->getId() != $sign->getId())) {
                $response->error('Данный адрес уже используется', 'email');
            }
        }

        if (!$entity->isNew() and $entity->getEmail() != $request->data->get('email')) {
            $this->emailChanged = $entity->getEmail();
        }
    }

    /**
     * @param ComponentRequest $request
     * @param ComponentResponse $response
     * @param $entity User
     */
    public function preSave(ComponentRequest $request, ComponentResponse $response, $entity)
    {
        $authorization = $this->container->get('app.authorization');
        if (!$entity->isNew() and false !== $this->emailChanged) {
            if ($sign = $authorization->findUserSign('email', $this->emailChanged, $entity)) {
                $sign->delete();
            }
        }

        if ($password = $request->data->get('password_raw')) {
            $authorization->changePassword($entity, $password);
        }
    }

    /**
     * @param ComponentRequest $request
     * @param ComponentResponse $response
     * @param User $entity
     */
    public function postSave(ComponentRequest $request, ComponentResponse $response, $entity)
    {
        $authorization = $this->container->get('app.authorization');

        if (!$authorization->findUserSign('email', $entity->getEmail())) {
            $authorization->createUserSign($entity, 'email', $entity->getEmail(), true);
        }
    }


}