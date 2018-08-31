<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


//
use App\Services\Functions;


class TestController extends Controller
{
    /**
     * @Route("/test/redis",name="test_redis")
     * yes it is already working
     */
    public function test_redis()
    {
        $redis = $this->get('redis');
        dump($redis);
        $redis->set('str1','haha symfony');
        $redis->delete('str');
        return new Response('<body>yes</body>');
    }
    /**
     * @Route("/test/email",name="test_email")
     * yes 
     */
    public function test_email()
    {
        
        $transport = $this->get('transport');
        $transport->setUsername($this->getParameter('email.username'))->setPassword($this->getParameter('email.password'));
        $mailer = new \Swift_Mailer($transport);
        $message = $this->get('message');
        $message = $message->setTo('402738923@qq.com')->setBody('您的验证码是: 123456,请勿泄漏');
        $result = $mailer->send($message);
        $redis = $this->get('redis');
        $redis->set('email:402738923@qq.com:code',123456);
        dump($result); 
        return new Response('<body>yes</body>');
    }
    /**
     * @Route("/test/functions",name="test_functions")
     * 测试公共函数
     */
    public function test_functions()
    {
        $function = new Functions();
        $email = '402738923@qq.com';
        $url = $function->get_gravatar($email);
        dump($url);
        return new Response('<body>' . $url . '</body>');
    }
}