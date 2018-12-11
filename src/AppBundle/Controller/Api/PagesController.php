<?php

namespace AppBundle\Controller\Api;

use AppBundle\Normalizer\PageNormalizer;
use Creonit\PageBundle\Model\Page;
use Creonit\RestBundle\Annotation as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Route("/pages")
 */
class PagesController extends Controller
{
    /**
     * Получить информацию о странице по её URL
     *
     * @Rest\QueryParameter("path", type="string", description="URL страницы")
     *
     * @Route("")
     * @Method("GET")
     */
    public function getPagesByUrl(Request $request)
    {
        $handler = $this->get('rest.handler')->setRequest($request);

        $handler->validate([
            'query' => [
                'path' => [new NotBlank()],
            ]
        ]);

        $handler->data->set($this->get('app.page')->getPage($request->query->get('path')));
        $handler->data->addGroup(PageNormalizer::GROUP_DETAIL);

        return $handler->response()->setSharedMaxAge(60);
    }

    /**
     * Получить информацию о странице по её идентификатору
     *
     * @Rest\PathParameter("name", type="string", description="Идентификатор страницы")
     *
     * @Route("/{name}")
     * @Method("GET")
     */
    public function getPageByName(Request $request, $name)
    {
        $handler = $this->get('rest.handler')->setRequest($request);

        $handler->checkFound($page = $this->get('app.page')->getPageByName($name));
        $handler->data->set($page);
        $handler->data->addGroup(PageNormalizer::GROUP_DETAIL);

        return $handler->response()->setSharedMaxAge(60);
    }

    /**
     * Получить информацию о меню по его идентификатору
     *
     * @Rest\PathParameter("name", type="string", description="Идентификатор меню")
     *
     * @Route("/menu/{name}")
     * @Method("GET")
     */
    public function getMenuAction(Request $request, $name)
    {
        $handler = $this->get('rest.handler')->setRequest($request);

        $handler->checkFound($page = $this->get('app.page')->getPageByName($name));
        $data = $this->getMenu($page);
        $handler->data->set($data);
        $handler->data->addGroup(PageNormalizer::GROUP_DETAIL);

        return $handler->response()->setSharedMaxAge(60);
    }

    /**
     * @param Page $rootPage
     * @return array
     */
    protected function getMenu(Page $rootPage)
    {
        $routeCollection = $this->get('router')->getRouteCollection();

        $children = [];
        foreach ($rootPage->getChildrenQuery(1)->forList()->find() as $page) {
            $child = [
                'name' => $page->getName(),
                'title' => $page->getTitle(),
            ];

            if ($page->getType() == Page::TYPE_ROUTE) {
                if ($route = $routeCollection->get($page->getName())) {
                    $child['url'] = $this->get('router')->generate($page->getName());
                }

            } else {
                $child['url'] = $page->getUrl();
            }

            $child['children'] = $this->getMenu($page);
            if ($child['children'] === []) {
                unset($child['children']);
            }

            $children[] = $child;
        }

        return $children;
    }

}
