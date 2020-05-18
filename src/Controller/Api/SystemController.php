<?php

namespace App\Controller\Api;

use Creonit\RestBundle\Annotation\Parameter\PathParameter;
use Creonit\RestBundle\Handler\RestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class SystemController extends AbstractController
{
    /**
     * Получить CSRF токен
     *
     * @PathParameter("id", type="string", description="Идентификатор")
     *
     * @Route("/csrf/{id}", methods={"GET"})
     */
    public function getCsrfToken(RestHandler $handler, CsrfTokenManagerInterface $csrfTokenManager, $id)
    {
        return $handler->response([
            'token' => $csrfTokenManager->getToken($id)->getValue()
        ]);
    }

}
