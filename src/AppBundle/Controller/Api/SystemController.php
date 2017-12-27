<?php

namespace AppBundle\Controller\Api;

use Creonit\PageBundle\Model\Page;
use Creonit\PageBundle\Model\PageQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SystemController extends Controller
{

    /**
     * @Route("/pages/")
     * @Method("GET")
     */
    public function getPagesAction(Request $request)
    {
        $path = $request->query->get('path');

        $route = $this->get('router')->match($path);

        if (preg_match('/^_page_(\d+)$/', $route['_route'], $match)) {
            $activePage = PageQuery::create()->findPk($match[1]);

        } else {
            $activePage = PageQuery::create()->findOneByName($route['_route']);
        }

        if (!$activePage) {
            throw $this->createNotFoundException('Страница не найдена');

        } else {
            $data = [
                'title' => $activePage->getTitle(),
                'meta' => [
                    'title' => $activePage->getMetaTitle(),
                    'description' => $activePage->getMetaDescription(),
                    'keywords' => $activePage->getMetaKeywords(),
                ],
            ];
        }

        return $this->json($data)->setSharedMaxAge(60);
    }

    /**
     * @Route("/test/")
     * @Method("GET")
     */
    public function getTestAction(Request $request)
    {
        return $this->jsodn(['test' => 'привет как дела?'])->setSharedMaxAge(60);
    }



}
