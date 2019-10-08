<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminBlogController extends AbstractController
{
    /**
     * @Route("/adminblog", name="admin_blog")
     */
    public function index()
    {
        return $this->render('admin_blog/home.html.twig', [
            'controller_name' => 'AdminBlogController',
        ]);
    }


}
