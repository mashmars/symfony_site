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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//注册
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
//登陆
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
//公共函数
use App\Services\Functions;

/**
 * @Route("/",host="dev.site.com")
 */
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
            //设置头像
            $common = new Functions();
            $headimg = $common->get_gravatar($user->getEmail());
            $user->setHeadimg($headimg);
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
        $act = $request->request->get('act');
        if($act == 'register'){
            //add logic
        }elseif($act == 'findPassword'){
            //add logic
        }else{ 
            return new JsonResponse(['code'=>1,'msg'=>'非法请求']);
        }
        $code = mt_rand(100000,999999);
        $transport = $this->get('transport');
        $transport->setUsername($this->getParameter('email.username'))->setPassword($this->getParameter('email.password'));
        $mailer = new \Swift_Mailer($transport);
        $message = $this->get('message');
        $message = $message->setTo($email)->setBody('您的验证码是: ' . $code . ',请勿泄漏');
        $result = $mailer->send($message);
        $redis = $this->get('redis');        
        $redis->set($act . ':' . $email . ':code',$code);
        return new JsonResponse(['code'=>0,'msg'=>'发送成功']);
    }


    /**
     * @Route("/login",name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils,Request $request)
    {       
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
       
        return $this->render('home/auth/login.html.twig',[            
            'last_username'=>$lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/find-password",name="find_password")
     */
    public function findPassword(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createFormBuilder() //这儿没有传递$user,如果传递就按新增处理了， 
            ->add('email',TextType::class,['label'=>'邮箱','attr'=>['class'=>'email']])
            ->add('sendEmail',ButtonType::class,['label'=>'发送邮件','attr'=>['class'=>'float-right send_email']])
            ->add('code',TextType::class,['label'=>'邮箱验证码','mapped'=>false])
            ->add('plainPassword',RepeatedType::class,[
                'type'  => PasswordType::class,
                'first_options'=> ['label'=>'新密码'],
                'second_options'=>['label'=>'确认密码'],
            ])
            ->add('submit',SubmitType::class,['label'=>'找回密码','attr'=>['class'=>'button hollow primary small']])
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $email = $form->get('email')->getData();
            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);
            if(!$user){
                $this->addFlash('alert','邮箱地址不正确');
                return $this->redirectToRoute('find_password');
            }
            //验证邮箱验证码
            $code = $form->get('code')->getData();
            $redis = $this->get('redis');
            $code_ = $redis->get('findPassword:'.$user->getEmail() . ':code');
            if($code != $code_){
                $this->addFlash('alert','邮箱验证码不正确');
                return $this->redirectToRoute('find_password');
            }
            $password = $passwordEncoder->encodePassword($user,$form->get('plainPassword')->getData());
            $user->setPassword($password);
            
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success','修改密码成功,请重新登录');
            $redis->delete('findPassword:'.$user->getEmail() . ':code');
           // return new Response('<body>sdf</body>');
            return $this->redirectToRoute('find_password');
        }
        return $this->render('home/auth/find_password.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    

}