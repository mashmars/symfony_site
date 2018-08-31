<?php
namespace App\Controller\Home;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Controller\Home\BaseController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
/**
 * @Route("/",host="dev.site.com")
 */
class IndexController extends BaseController
{
    /**
     * @Route("/",name="home")
     */
    public function index(SessionInterface $session)
    {       
        return $this->render('home/index.html.twig');
    }
}