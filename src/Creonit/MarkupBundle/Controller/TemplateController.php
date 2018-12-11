<?php

namespace Creonit\MarkupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateController extends Controller
{
    public function renderAction(Request $request)
    {
        $response = new Response(
            $this->renderView($request->attributes->get('template', 'static.twig'))
        );

        return $response;
    }

}