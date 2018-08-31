<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BaseController extends Controller 
{
    protected $admin;
    public function __construct(SessionInterface $session)
    {
        /**
         * 需要学习查找如何在构造函数里访问像Request Route redirect Response等 TODOOOOOOOOOOO
         */
        $admin = $session->get('admin');
        $this->admin = $admin;        
        if(!$this->admin){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo json_encode(['code'=>1,'msg'=>'请先登录系统']);
            }else{
                echo "<script>location.href='/admin-login'</script>";
            }
            
        }
    }
}
