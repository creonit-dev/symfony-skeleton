<?php


namespace AppBundle\Service;

use AppBundle\Model\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserService
{
    const NAME_REGEX = '/^([a-zа-я]+)(?: *([a-zа-я]+))?$/usi';

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function checkName($name)
    {
        if (preg_match(self::NAME_REGEX, $name)) {
            return true;
        }
        return false;
    }

    /**
     * @return User|mixed|void
     */
    public function getCurrentUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }
}
