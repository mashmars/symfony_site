<?php
namespace App\Controller\Home;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user",name="user_")
 */
class UserController extends Controller
{
    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        return new Response('<body>sdfsdf</body>');
    }
}