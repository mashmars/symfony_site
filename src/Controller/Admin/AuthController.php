<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\Admin;

class AuthController extends Controller
{
    /**
     * @Route("/admin-login",name="admin_login")
     */
    public function login(Request $request,UserPasswordEncoderInterface $passwordEncoder,SessionInterface $session)
    {
        if($request->getMethod() == 'POST'){
            $error = false;
            $username = $request->request->get('username');
            $plainPassword = $request->request->get('password');
            $csrfToken = $request->request->get('token');
            if(empty($username)){
                $this->addFlash('alert','用户名不能为空');
                $error = true;
            }
            if(empty($plainPassword) ){
                $this->addFlash('alert','登录密码不能为空');
                $error = true;
            }
            if($error){                
                return $this->redirectToRoute('admin_login');
            }
            //
            $entityManager = $this->getDoctrine()->getManager();
            $repository = $entityManager->getRepository(Admin::class);
            $admin = $repository->findOneBy(['username'=>$username]);
            if(!$admin){
                $this->addFlash('alert','用户名或密码不正确');
                return $this->redirectToRoute('admin_login');
            }
            //验证密码
            $password = $passwordEncoder->encodePassword($admin,$plainPassword);
            if($password != $admin->getPassword())
            {
                $this->addFlash('alert','用户名或密码不正确');
                return $this->redirectToRoute('admin_login');
            }

            //开启session
            $session->set('admin',$admin);

            //记录最后登录时间和ip
            $admin->setLastLoginTime(new \Datetime());
            $admin->setLastLoginIp($request->getClientIp());
            $entityManager->flush();            

            return $this->redirectToRoute('admin_index');
        }
        return $this->render('admin/auth/login.html.twig');
    }

    /**
     * @Route("/admin-register",name="admin_register")
     */
    public function register(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        if($request->getMethod() == 'POST'){
            $this->addFlash('alert','没有开放注册');
            return $this->redirectToRoute('admin_register');
            $error = false;
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $password2 = $request->request->get('password2');
            $csrfToken = $request->request->get('token');
            if(empty($username)){
                $this->addFlash('alert','用户名不能为空');
                $error = true;
            }
            if(empty($password) || empty($password2)){
                $this->addFlash('alert','初始密码和确认密码不能为空');
                $error = true;
            }
            if($password != $password2){
                $this->addFlash('alert','初始密码和确认密码不一致');
                $error = true;
            }
            if($this->isCsrfTokenValid('admin-register',$csrfToken)){
                $this->addFlash('alert','非法请求');
                $error = true;
            }
            if($error){                
                return $this->redirectToRoute('admin_register');
            }
            //
            $admin = new Admin();
            $entityManager = $this->getDoctrine()->getManager();
            $repository = $entityManager->getRepository(Admin::class);
            $is_exist = $repository->findOneBy(['username'=>$username]);
            if($is_exist){
                $this->addFlash('alert','用户名已存在');
                return $this->redirectToRoute('admin_register');
            }
            $password = $passwordEncoder->encodePassword($admin,$password);
            $admin->setUsername($username);
            $admin->setPassword($password);
            $entityManager->persist($admin);
            $entityManager->flush();
            $this->addFlash('success','注册成功');
            return $this->redirectToRoute('admin_register');
        }
        return $this->render('admin/auth/register.html.twig');
    }

    
}