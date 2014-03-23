<?php

namespace Stamps\PassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('StampsPassBundle:Default:index.html.twig', array('name' => $name));
    }
}
