<?php

namespace AppBundle\Controller\Api;

use AppBundle\Model\User;
use Creonit\RestBundle\Annotation as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UsersController extends Controller
{

    /**
     * Проверить валидность токена
     *
     * @Rest\QueryParameter("token", required=true, description="Токен полученный при переходе по ссылке из письма")
     *
     * @Route("/users/restore_password")
     * @Method("GET")
     */
    public function getRestorePassword(Request $request)
    {
        $handler = $this->get('rest.handler')->setRequest($request);
        $handler->validate([
            'query' => [
                'token' => [new NotBlank()],
            ]
        ]);

        $userRestoreService = $this->get('app.user_restore');
        $token = $userRestoreService->getToken($request->query->get('token'));

        return $handler->response([
            'valid' => $token and !$userRestoreService->isExpired($token) and !$token->isUsed()
        ]);
    }

    /**
     * Отправить письмо на емейл для восстановления пароля
     *
     * @Rest\RequestParameter("email", required=true)
     *
     * @Route("/users/restore_password")
     * @Method("POST")
     */
    public function postRestorePassword(Request $request)
    {
        $handler = $this->get('rest.handler')->setRequest($request);
        $handler->validate([
            'request' => [
                'email' => [new NotBlank(), new Email()]
            ]
        ]);


        $authorization = $this->get('app.authorization');
        $userSign = $authorization->findUserSign('email', $request->request->get('email'));

        if (!$userSign) {
            $handler->error->set('request/email', 'Указанный email не найден на сайте')->send();
        }

        $userRestoreService = $this->get('app.user_restore');
        $token = $userRestoreService->createToken($userSign->getUser());
        $userRestoreService->sendMail($userSign->getUser(), $token);

        return $handler->response([
            'email' => $token->getUser()->getEmail()
        ]);
    }

    /**
     * Установить новый пароль
     *
     * @Rest\RequestParameter("token", required=true, description="Токен полученный при переходе по ссылке из письма")
     * @Rest\RequestParameter("password", required=true)
     *
     * @Route("/users/restore_password")
     * @Method("PUT")
     */
    public function putRestorePassword(Request $request)
    {
        $handler = $this->get('rest.handler')->setRequest($request);
        $handler->validate([
            'request' => [
                'token' => [new NotBlank()],
                'password' => [new NotBlank(), new Length(['min' => 6])],
            ]
        ]);

        $authorization = $this->get('app.authorization');
        $userRestoreService = $this->get('app.user_restore');
        $token = $userRestoreService->getToken($request->request->get('token'));

        if (!$token) {
            $handler->error->send('Токен подтверждения пароля не найден');
        }

        if ($token->isUsed()) {
            $handler->error->send('Токен подтверждения пароля уже использован');
        }

        if ($userRestoreService->isExpired($token)) {
            $handler->error->send('Токен подтверждения пароля истек');
        }

        if ($userSign = $authorization->findUserSign('email', $token->getUser()->getEmail(), $token->getUser())) {
            $authorization->enableUserSign($userSign);
        }

        $authorization->changePassword($token->getUser(), $request->request->get('password'));
        $authorization->authorizeUser($token->getUser());
        $userRestoreService->useToken($token);


        $response = [
            'redirect' => $this->generateUrl('creonit_admin_index')
        ];

        return $handler->response($response);
    }

    /**
     * Авторизация пользователя
     *
     * @Rest\RequestParameter("email", required=true)
     * @Rest\RequestParameter("password", required=true)
     *
     * @Route("/users/authorize")
     * @Method("POST")
     */
    public function postAuthorize(Request $request)
    {
        $handler = $this->get('rest.handler')->setRequest($request);
        $handler->checkCsrfToken('authorize');

        $handler->validate([
            'email' => [new NotBlank(), new Email()],
            'password' => [new NotBlank(), new Length(['min' => 6])],
        ]);

        $authorization = $this->get('app.authorization');

        /** @var User $user */
        if (!$sign = $authorization->findUserSign('email', $request->request->get('email')) or !$user = $sign->getUser()) {
            $handler->error->set('request/email', 'Пользователь не найден')->send();
        }

        $encoder = $this->get('security.password_encoder');
        if (!$encoder->isPasswordValid($user, $request->request->get('password'))) {
            $handler->error->set('request/password', 'Неправильный пароль')->send();
        }

        if (!$sign->getEnabled()) {
            $handler->error->set('request/email', 'Вы не подтвердили почту')->send();
        }

        $this->get('app.authorization')->authorizeUser($user);

        return $handler->response();
    }

    /**
     * Выход из системы
     *
     * @Route("/users/logout")
     * @Method("POST")
     */
    public function postLogout(Request $request)
    {
        $handler = $this->get('rest.handler')->setRequest($request);

        if ($handler->isAuthenticated()) {
            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
        }

        return $handler->response();
    }

    /**
     * Регистрация нового пользователя
     *
     * @Rest\RequestParameter("email", required=true)
     * @Rest\RequestParameter("password", required=true)
     * @Rest\RequestParameter("password_confirmation", required=true)
     * @Rest\RequestParameter("name", required=true)
     *
     * @Route("/users/register")
     * @Method("POST")
     */
    public function postRegister(Request $request)
    {
        $handler = $this->get('rest.handler')->setRequest($request);

        $handler->validate([
            'request' => [
                'email' => [new NotBlank(['message' => 'Укажите email']), new Email()],
                'password' => [new NotBlank(['message' => 'Введите пароль']), new Length(['min' => 6])],
                'password_confirmation' => [
                    new NotBlank(['message' => 'Введите подтверждение']),
                    new Length(['min' => 6]),
                    new EqualTo(['value' => $request->request->get('password', ''), 'message' => 'Введенные пароли не совпадают'])
                ],
                'name' => [
                    new NotBlank(['message' => 'Укажите имя'])
                ]
            ]
        ]);

        $authorization = $this->get('app.authorization');


        if ($sign = $authorization->findUserSign('email', $request->request->get('email'))) {
            $handler->error->set('request/email', 'Этот email уже используется')->send();
        }

        $handler->error->send();

        $user = $authorization->processNewUser(
            $request->request->get('email'),
            $request->request->get('password'),
            $request->request->get('name')
        );

        $authorization->authorizeUser($user);

        return $handler->response();
    }
}
