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
    public function isEqualTo(UserInterface $user)
    {
        return true;
    }

    public function getTitle()
    {
        return $this->name;
    }
}
