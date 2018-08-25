<?php
namespace App\Controller\Home;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\Home\UserType;
use App\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//注册
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
//登陆
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends Controller
{
    /**
     * @Route("/register",name="register")
     */
    public function register(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //验证邮箱验证码是否正确
            $code = $form->get('code')->getData();
            $redis = $this->get('redis');
            $code_ = $redis->get('register:' .$user->getEmail() . ':code');

            if($code != $code_){
                $this->addFlash('alert','邮箱验证码不正确');
                return $this->redirectToRoute('register');
            }
            
            //下面是正式的流程
            $plainPassword = $user->getPlainPassword();
            //$plainPassword = $form->get('plainPassword')->getData();
            $password = $passwordEncoder->encodePassword($user,$plainPassword);
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success','注册成功');
            $redis->delete('register:' .$user->getEmail() . ':code');
            return $this->redirectToRoute('register');
        }
        return $this->render('home/auth/register.html.twig',[
            'form'=> $form->createView(),
        ]);
    }
    /**
     * @Route("/register/send_email",name="send_email")
     */
    public function send_email(Request $request)
    {
        $email = $request->request->get('email');
        if(!$email){
            return new JsonResponse(['code'=>1,'msg'=>'邮箱不能为空']);
        }
        $code = mt_rand(100000,999999);
        $transport = $this->get('transport');
        $transport->setUsername($this->getParameter('email.username'))->setPassword($this->getParameter('email.password'));
        $mailer = new \Swift_Mailer($transport);
        $message = $this->get('message');
        $message = $message->setTo('402738923@qq.com')->setBody('您的验证码是: ' . $code . ',请勿泄漏');
        $result = $mailer->send($message);
        $redis = $this->get('redis');        
        $redis->set('register:' . $email . ':code',$code);
        return new JsonResponse(['code'=>0,'msg'=>'发送成功']);
    }


    /**
     * @Route("/login",name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils,Request $request)
    {
        //
        dump($this->getUser());
        $session = $request->getSession();
        dump($session);
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
       
        return $this->render('home/auth/login.html.twig',[            
            'last_username'=>$lastUsername,
            'error' => $error,
        ]);
    }
    /**
     * @Route("/")
     */
    public function index()
    {
        return new Response('<body>ddd</body>');
    }
}