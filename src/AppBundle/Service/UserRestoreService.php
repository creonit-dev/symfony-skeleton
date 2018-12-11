<?php

namespace AppBundle\Service;

use AppBundle\Model\User;
use AppBundle\Model\UserRestoreToken;
use AppBundle\Model\UserRestoreTokenQuery;
use Creonit\MailingBundle\Service\Mailing;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

class UserRestoreService
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function sendMail(User $user, UserRestoreToken $token, $email = null)
    {
        if (null === $email) {
            $email = $user->getEmail();
        }

        /** @var Mailing $mailing */
        $mailing = $this->container->get('creonit_mailing');
        $mailing->send(
            $mailing->createMessage('', [
                'auth.reset' => $this->container->get('serializer')->normalize([
                    'name' => $user->getName(),
                    'link' => $this->container->get('router')->generate('index', ['restore_password_token' => $token->getCode()], RouterInterface::ABSOLUTE_URL)
                ])
            ]),
            $email
        );
    }

    public function getToken($code)
    {
        return UserRestoreTokenQuery::create()
            ->filterByCode($code)
            ->findOne();
    }

    public function createToken(User $user, $expiredAt = '+24 hours', $save = true)
    {
        $token = new UserRestoreToken();
        $token->setUser($user);
        $token->setCode($this->generateCode($user));
        $token->setExpiredAt($expiredAt);
        if ($save) {
            $token->save();
        }

        return $token;
    }

    public function isExpired(UserRestoreToken $token)
    {
        return $token->getExpiredAt() < new \DateTime('now');
    }

    public function useToken(UserRestoreToken $token)
    {
        $token->setUsed(true)->save();
    }

    protected function generateCode(User $user)
    {
        return base_convert($user->getId() . 'g' . md5(uniqid()), 17, 36);
    }

}