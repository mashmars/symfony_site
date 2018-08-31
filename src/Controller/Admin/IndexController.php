<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Controller\Admin\BaseController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class IndexController extends BaseController
{
    /**
     * @Route("/admin/",name="admin_index")
     */
    public function index()
    {       
        return $this->render('admin/index/index.html.twig');
    }
}