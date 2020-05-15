<?php


namespace App\Controller;


use App\Model\Test;
use App\Model\TestQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(){

        $test = new Test();
        $test->setTest(111);
        $test->save();

        return new Response('<html><head></head><body>Hello</body></html>');
    }
}