<?php

namespace BiberLtd\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BiberLtdBlogBundle:Default:index.html.twig', array('name' => $name));
    }
}
