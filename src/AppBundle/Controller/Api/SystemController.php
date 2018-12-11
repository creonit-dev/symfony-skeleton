<?php

namespace AppBundle\Controller\Api;

use Creonit\RestBundle\Annotation as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SystemController extends Controller
{
    /**
     * Получить CSRF токен
     *
     * @Rest\PathParameter("id", type="string", description="Идентификатор")
     *
     * @Route("/csrf/{id}")
     * @Method("GET")
     */
    public function getCsrfToken(Request $request, $id)
    {
        $handler = $this->get('rest.handler')->setRequest($request);

        return $handler->response([
            'token' => $this->get('security.csrf.token_manager')->getToken($id)->getValue()
        ]);
    }

}
