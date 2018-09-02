<?php
namespace App\Controller\Home;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Controller\Home\BaseController;

class ArticleController extends BaseController
{
    /**
     * @Route("/article",name="article_index")
     */
    public function index()
    {
        $this->common_base(); 
        return $this->render('home/article/article_list.html.twig',[
            'article_category' => $this->article_category,
        ]);
    }
}