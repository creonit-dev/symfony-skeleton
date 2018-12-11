<?php

namespace AppBundle\Admin\Users;

use Creonit\AdminBundle\Module;
use Creonit\UserBundle\Admin\ChooseGroupTable;
use Creonit\UserBundle\Admin\ChooseRoleGroupTable;
use Creonit\UserBundle\Admin\GroupEditor;
use Creonit\UserBundle\Admin\GroupRoleTable;
use Creonit\UserBundle\Admin\GroupTable;
use Creonit\UserBundle\Admin\RoleEditor;
use Creonit\UserBundle\Admin\RoleGroupEditor;
use Creonit\UserBundle\Admin\RoleTable;
use Creonit\UserBundle\Admin\UserGroupRelTable;

class UsersModule extends Module
{

    protected function configure()
    {
        $this
            ->setTitle('Пользователи')
            ->setIcon('user')
            ->setTemplate('UserTable')
        ;
    }

    public function initialize()
    {
        $this->addComponent(new UserTable);
        $this->addComponent(new UserEditor);
        $this->addComponent(new UserGroupRelTable);

        $this->addComponent(new GroupTable);
        $this->addComponent(new GroupEditor);
        $this->addComponent(new GroupRoleTable);
        $this->addComponent(new ChooseGroupTable);

        $this->addComponent(new RoleTable);
        $this->addComponent(new RoleEditor);
        $this->addComponent(new RoleGroupEditor);
        $this->addComponent(new ChooseRoleGroupTable);
    }
}