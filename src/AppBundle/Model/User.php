<?php

namespace AppBundle\Model;

use AppBundle\Model\Base\User as BaseUser;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Skeleton subclass for representing a row from the 'user' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class User extends BaseUser
{
    const PROVIDER_EMAIL = 'email';

    public static $providers = [
        self::PROVIDER_EMAIL => 'Email',
    ];


    public function isEqualTo(UserInterface $user)
    {
        if($this !== $user) {
            return true;
        }

        $roles = $this->getRoles();
        $newRoles = $user->getRoles();
        return !(count($roles) != count($newRoles) || array_diff($roles, $newRoles));
    }

    public function getTitle()
    {
        return $this->name;
    }

    public function getTitleFull()
    {
        return $this->getTitle();
    }
}
