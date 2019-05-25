<?php

namespace AppBundle\Controller\Api;

use Creonit\RestBundle\Annotation as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/forms")
 */
class FormsController extends Controller
{

    /**
     * Сохранить заполненную форму обратной связи
     *
     * @Rest\PathParameter("id", type="string", description="Идентификатор формы обратной связи")
     * @Rest\RequestParameter("field_1", type="string", description="Поля формы")
     *
     * @Route("/{id}", name="form")
     * @Method("POST")
     */
    public function postForm(Request $request, $id)
    {
        $handler = $this->get('rest.handler')->setRequest($request);

        if (!$request->isXmlHttpRequest()) {
            $handler->error->send('Форма не найдена', 1, 404);
        }

        $formService = $this->get('app.form_service');
        $handler->checkFound($form = $formService->getForm($id));

        $data = $request->request->all();
        $data['url'] = $request->headers->get('referer');

        foreach ($form->getFields() as $field) {

            $constraints = $formService->getConstraints($field);

            if ($constraints) {
                if (array_key_exists($field->getFieldName(), $data)) {
                    $value = $data[$field->getFieldName()];
                } else {
                    $value = '';
                }
                if (!$handler->isValid($value, $constraints)) {
                    $handler->error->set('request/' . $field->getFieldName(), 'Ошибка заполнения');
                }
            }
        }
        $handler->error->send();

        $result = $formService->saveResult($form, $data);
        $formService->sendNotification($result);

        $handler->data->set(['success_text' => $form->getSuccessText()]);

        return $handler->response();
    }

}
