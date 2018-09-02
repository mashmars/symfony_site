<?php
namespace App\Controller\Home;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Home\BaseController;

//

/**
 * @Route("/",host="dev.site.com")
 */
class IndexController extends BaseController
{
    
    /**
     * @Route("/",name="home")
     */
    public function index()
    {     
        $this->common_base();        
        return $this->render('home/index.html.twig',[
            'article_category' => $this->article_category,
        ]);
    }
}