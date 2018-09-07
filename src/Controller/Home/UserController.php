<?php
namespace App\Controller\Home;
use App\Controller\Home\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//个人资料
use App\Entity\User;
use App\Form\Home\UserProfileType;
//blog分类
use App\Entity\Category;
use App\Form\Home\CategoryAddType;
//blog标签
use App\Entity\Tag;
//blog文章
use App\Entity\Post;
use App\Form\Home\PostType;
//upload
use App\Services\FileUploader;
use Symfony\Component\HttpFoundation\File\File;
//album图片专辑
use App\Entity\Album;
use App\Entity\AlbumCategory;
use App\Entity\AlbumComment;
//话题
use App\Entity\Topic;
use App\Entity\TopicCategory;
use App\Entity\TopicComment;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;


/**
 * @Route("/",host="dev.site.com")
 */
class UserController extends BaseController
{
    
    /**
     * @Route("/user-index", name="user_index")
     */
    public function index(Request $request)
    {
        $this->common_base();
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success','修改个人信息成功');
            return $this->redirectToRoute('user_index');
        }
        return $this->render('home/user/index.html.twig',[
            'form'=>$form->createView(),
            'article_category' => $this->article_category,
        ]);
    }


    /**
     * @Route("/user-blog-category",name="user_blog_category")
     */
    public function category_index()
    {
        $this->common_base();
        $user = $this->getUser();
        $category = $user->getCategories();
        
        return $this->render('home/user/blog_category.html.twig',[
            'category'=>$category,
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-blog-category-add",name="user_blog_category_add")
     */
    public function category_add(Request $request)
    {
        $this->common_base();
        $user = $this->getUser();
        $category = new Category();
        $form = $this->createForm(CategoryAddType::class,$category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $category->setUserid($user);
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success','新增成功');
            return $this->redirectToRoute('user_blog_category_add');
        }
       
        return $this->render('home/user/blog_category_add.html.twig',[
            'form'=>$form->createView(),
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-blog-category-{slug<\d+>}-edit",name="user_blog_category_edit")
     */
    public function category_edit($slug,Request $request)
    {
        $this->common_base();
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Category::class);
        $category = $repository->find($slug);
        if(!$category || $category->getUserid()->getId() != $user->getId()){
            throw $this->createNotFoundException('没有找到相关分类');
        }
        $form = $this->createForm(CategoryAddType::class,$category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash('success','编辑成功');
            return $this->redirectToRoute('user_blog_category');
        }
        return $this->render('home/user/blog_category_edit.html.twig',[
            'form'=>$form->createView(),
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-blog-category-{slug<\d+>}-delete",name="user_blog_category_delete")
     */
    public function category_delete($slug)
    {
        $this->common_base();
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Category::class);
        $category = $repository->find($slug);
        if(!$category || $category->getUserid()->getId() != $user->getId()){
            throw $this->createNotFoundException('没有找到相关分类');
        }
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success','删除成功');
        return $this->redirectToRoute('user_blog_category');   
    }
    /**
     * @Route("/user-blog-tag",name="user_blog_tag")
     */
    public function tag_index()
    {
        $this->common_base();
        $user = $this->getUser();
        $tags = $user->getTags();
        return $this->render('home/user/blog_tag.html.twig',[
            'tags'=>$tags,
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-blog-tag-add",name="user_blog_tag_add")
     */
    public function tag_add(Request $request)
    {
        $this->common_base();
        $user = $this->getUser();
        $tag = new Tag();
        $form = $this->createForm(CategoryAddType::class,$tag); //公用表单
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $tag->setUserid($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();
            $this->addFlash('success','新增成功');
            return $this->redirectToRoute('user_blog_tag');
        }
        return $this->render('home/user/blog_tag_add.html.twig',[
            'form'=>$form->createView(),
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-blog-tag-{slug<\d+>}-edit",name="user_blog_tag_edit")
     */
    public function tag_edit(Request $request,$slug)
    {        
        $this->common_base();
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Tag::class);
        $tag = $repository->find($slug);
        if(!$tag || $tag->getUserid()->getId() != $user->getId()){
            throw $this->createNotFoundException('没有找到相关标签');
        }
        $form = $this->createForm(CategoryAddType::class,$tag);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash('success','编辑成功');
            return $this->redirectToRoute('user_blog_tag');
        }
        return $this->render('home/user/blog_tag_edit.html.twig',[
            'form'=>$form->createView(),
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-blog-tag-{slug<\d+>}-delete",name="user_blog_tag_delete")
     */
    public function tag_delete($slug)
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Tag::class);
        $tag = $repository->find($slug);
        if(!$tag || $tag->getUserid()->getId() != $user->getId())
        {
            throw $this->createNotFoundException('没有找到相关标签');
        }
        $entityManager->remove($tag);
        $entityManager->flush();
        $this->addFlash('success','删除成功');
        return $this->redirectToRoute('user_blog_tag');
    }

    /**
     * @Route("/user-blog-post-{page<\d+>?1}-list",name="user_blog_post")
     */
    public function blog_post($page)
    {        
        $this->common_base();
        $limit = 5;
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->getRepository(Post::class)->createQueryBuilder('u')
                    ->where('u.userid = :userid')
                    ->setParameter('userid',$user->getId())
        ;
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$limit);

        return $this->render('home/user/blog_post.html.twig',[
            'pagination'=>$pagination,
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-blog-post-add",name="user_blog_post_add")
     */
    public function blog_post_add(Request $request,FileUploader $fileUploader)
    {        
        $this->common_base();
        $user = $this->getUser();
        $post = new Post();
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $post->setUserid($user);
            
            $file = $form->get('thumb')->getData();
            if($file){
                $filename = $fileUploader->upload($file);
                $post->setThumb($request->getSchemeAndHttpHost().'/'.$this->getParameter('upload.directory').$filename); 
            }else{
                $ss = mt_rand(1000000,9999999);                
                $post->setThumb('https://www.gravatar.com/avatar/'.md5($ss).'?s=150&d=wavatar&r=g');
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $user->setCountPost($user->getCountPost() + 1);
            $entityManager->persist($user);
            $entityManager->flush(); 

            $this->addFlash('success','新增博客文章成功');
            return $this->redirectToRoute('user_blog_post_add');
        }
        return $this->render('home/user/blog_post_add.html.twig',[
            'form'=> $form->createView(),
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-blog-post-{slug<\d+>}-edit",name="user_blog_post_edit")
     */
    public function blog_post_edit(Request $request,$slug,FileUploader $fileUploader)
    {
        $this->common_base();
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Post::class);
        $post = $repository->find($slug);
        if(!$post || $post->getUserid()->getId() != $user->getId())
        {
            throw $this->createNotFoundException('没有找到相关文章');
        }
        $oldimg = $post->getThumb();
        if($oldimg){
            $post->setThumb($oldimg);
        }
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form->get('thumb')->getData();
            if($file){
                $filename = $fileUploader->upload($file);
                $filename = $request->getSchemeAndHttpHost() . '/' . $this->getParameter('upload.directory') . $filename;
                @unlink($oldimg);
            }else{
                if(!$oldimg){
                    $ss = mt_rand(1000000,9999999);                
                    $oldimg = 'https://www.gravatar.com/avatar/'.md5($ss).'?s=150&d=wavatar&r=g';
                }
                $filename = $oldimg;
            }
            
            $post->setThumb($filename);
            $entityManager->flush();
            $this->addFlash('success','编辑成功');
            return $this->redirectToRoute('user_blog_post');
        }
        return $this->render('home/user/blog_post_edit.html.twig',[
            'form'=> $form->createView(),
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-blog-post-{slug<\d+>}-delete",name="user_blog_post_delete")
     */
    public function blog_post_delete($slug)
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Post::class);
        $post = $repository->find($slug);
        if(!$post || $post->getUserid()->getId() != $user->getId())
        {
            throw $this->createNotFoundException('没有找到相关文章');
        }
        $entityManager->remove($post);
        $entityManager->flush();

        $user->setCountPost($user->getCountPost() - 1);
        $entityManager->persist($user);
        $entityManager->flush(); 
        @unlink($post->getThumb());
        $this->addFlash('success','删除成功');
        return $this->redirectToRoute('user_blog_post');
    }


    //////////图片上传
    /**
     * @Route("/user-album-image-index-{page<\d+>?1}",name="user_album_image_index")
     */
    public function album_index($page)
    {
        $this->common_base();
        $limit = 6;
        $repository = $this->getDoctrine()->getManager()->getRepository(Album::class);
        $queryBuilder = $repository->createQueryBuilder('u')
                        ->where('u.userid = :userid')
                        ->setParameter('userid',$this->user->getId())
                        ->orderBy('u.id','desc')
                        ->getQuery()
        ;
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$limit);
        return $this->render('home/user/album_index.html.twig',[
            'pagination'    =>  $pagination,
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-album-image-{id<\d+>}-show-{page<\d+>?1}",name="user_album_image_show")
     */
    public function album_show($id,$page)
    {
        $this->common_base();
        $limit = 5;
        $repository = $this->getDoctrine()->getManager()->getRepository(Album::class);
        $album = $repository->find($id);
        if(!$album || $album->getUserid()->getId() != $this->user->getId())
        {
            throw $this->createNotFoundException('没有找到相关信息');
        }
        //评论
        $repsoritory_album_comment = $this->getDoctrine()->getManager()->getRepository(AlbumComment::class);
        $queryBuilder = $repsoritory_album_comment->createQueryBuilder('u')
                        ->where("u.album = :album")->setParameter('album',$id)->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$limit);
        return $this->render('home/user/album_detail.html.twig',[
            'album' =>  $album,
            'pagination'    =>  $pagination,
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-album-comment-delete",name="user_album_comment_delete",methods={"POST"})
     * 删除评论
     */
    public function album_comment_delete(Request $request)
    {
        $id = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(AlbumComment::class);
        $comment = $repository->find($id);
        if(!$comment || $comment->getUserid()->getId() != $this->user->getId())
        {
            return $this->json(['code'=>1,'msg'=>'非法请求']);
        }
        $entityManager->remove($comment);
        $entityManager->flush();
        return $this->json(['code'=>0,'msg'=>'删除成功']);
    }
    /**
     * @Route("/user-album-image-add",name="user_album_image_add")
     */
    public function album_add()
    {
        $this->common_base();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(AlbumCategory::class);
        $categorys = $repository->findAll();
        return $this->render('home/user/album_add.html.twig',[
            'categorys' =>  $categorys,
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-album-image-upload",name="user_album_upload",methods={"POST"})
     */
    public function album_upload(Request $request,FileUploader $fileUploader)
    {
        $filenames = '';
        $tmp = []; //放redis 以便上传过程中通过文件名删除对应的上传文件， 当提交结束后删除redis键
        $files = $request->files->get('file');
        foreach($files as $file){    
            $filename = $fileUploader->upload($file);        
            $filenames .= $filename . ',';
            //源文件名 $file->getClientOriginalName() 
            $tmp[$file->getClientOriginalName()] = $filename;
        }
        $redis = $this->get('redis');
        $redis->setex('album:'.$this->user->getId() . ':upload',600,serialize($tmp));       
        return $this->json(rtrim($filenames,','));
    }
    /**
     * @Route("/user-album-image-upload-delete",name="user_album_upload_delete",methods={"POST"})
     */
    public function album_upload_delete(Request $request)
    {
        $filename = $request->request->get('filename');
        $redis = $this->get('redis');
        $userid = $this->user->getId();
        $uploads = unserialize($redis->get('album:'. $userid . ':upload'));
        @unlink($this->getParameter('upload.directory') . $uploads[$filename]);
        return $this->json(['code'=>0,'msg'=>'删除成功','data'=>$uploads[$filename]]);
    }
    /**
     * @Route("/user-album-image-upload-save",name="user_album_upload_save",methods={"POST"})
     */
    public function album_upload_save(Request $request)
    {
        $category = $request->request->get('category');
        $title = $request->request->get('title');
        $descript = $request->request->get('descript');
        $images   = $request->request->get('images');
        $csrf_token = $request->request->get('csrf_token');
        
        if(!$this->isCsrfTokenValid('album-upload',$csrf_token)){
            return $this->json(['code'=>1,'msg'=>'非法请求']);
        }

        if(!$images) return $this->json(['code'=>1,'msg'=>'请上传图片']);
        $images = explode(',',$images);

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(AlbumCategory::class);
        

        $category = $repository->find($category);
        if(!$category) return $this->json(['code'=>1,'msg'=>'分类不存在']);

        $album = new Album();
        $album->setUserid($this->user);
        $album->setCategory($category);
        $album->setTitle($title);
        $album->setDescript($descript);
        $album->setImages($images);
        $album->setThumb($images[0]);

        $entityManager->persist($album);
        $entityManager->flush();

        $redis = $this->get('redis');
        $userid = $this->user->getId();
        $redis->delete('album:'. $userid . ':upload');
        return $this->json(['code'=>0,'msg'=>'提交成功']);
    }


    ////////////topic  话题
    /**
     * @Route("/user-topic-index-{page<\d+>?1}",name="user_topic_index")
     */
    public function topic_index($page)
    {
        $this->common_base();
        $limit = 5;
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Topic::class);
        $queryBuilder = $repository->createQueryBuilder('u')
                        ->where("u.userid = :userid")->setParameter('userid',$this->user->getId())->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$limit);
        return $this->render('home/user/topic_index.html.twig',[
            'pagination' =>  $pagination,
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-topic-add",name="user_topic_add")
     */
    public function topic_add(Request $request)
    {
        $this->common_base();
        $topic = new Topic();
        $form = $this->createFormBuilder($topic)
                ->add('category',EntityType::class,[
                    'label' =>  '分类名称',
                    'choice_label'=>'name',                   
                    'class'=>TopicCategory::class,
                    'query_builder'=>function(EntityRepository $er){
                        return $er->createQueryBuilder('u');
                    }
                ])
                ->add('title',TextType::class,['label'=>'标题'])
                ->add('content',TextareaType::class,['label'=>'内容','attr'=>['rows'=>7]])
                ->add('submit',SubmitType::class,['label'=>'提交','attr'=>['class'=>'button hollow small']])
                ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $topic->setUserid($this->user);
            $entityManager->persist($topic);
            $entityManager->flush();
            $this->addFlash('success','提交成功');
            return $this->redirectToRoute("user_topic_add");
        }
        return $this->render('home/user/topic_add.html.twig',[
            'form'  =>$form->createView(),
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-topic-edit-{id<\d+>}",name="user_topic_edit")
     */
    public function topic_edit(Request $request,$id)
    {
        $this->common_base();
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Topic::class);
        $topic = $repository->find($id);
        if(!$topic || $topic->getUserid()->getId() != $this->user->getId())
        {
            throw $this->createNotFoundException('没有找到相关信息');
        }
        $form = $this->createFormBuilder($topic)
                ->add('category',EntityType::class,[
                    'label' =>  '分类名称',
                    'choice_label'=>'name',                    
                    'class'=>TopicCategory::class,
                    'query_builder'=>function(EntityRepository $er){
                        return $er->createQueryBuilder('u');
                    }
                ])
                ->add('title',TextType::class,['label'=>'标题'])
                ->add('content',TextareaType::class,['label'=>'内容','attr'=>['rows'=>7]])
                ->add('submit',SubmitType::class,['label'=>'提交','attr'=>['class'=>'button hollow small']])
                ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ 
            $entityManager->flush();
            $this->addFlash('success','提交成功');
            return $this->redirectToRoute("user_topic_edit",['id'=>$id]);
        }
        return $this->render('home/user/topic_edit.html.twig',[
            'form'  =>$form->createView(),
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-topic-delete-{id<\d+>}",name="user_topic_delete")
     */
    public function topic_delete(Request $request,$id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Topic::class);
        $topic = $repository->find($id);
        if(!$topic || $topic->getUserid()->getId() != $this->user->getId())
        {
            throw $this->createNotFoundException('没有找到相关信息');
        }
        $entityManager->remove($topic);
        $entityManager->flush();
        $this->addFlash('success','删除成功');
        return $this->redirectToRoute("user_topic_index");
    }
    /**
     * @Route("/user-topic-detail-{id<\d+>}-{page<\d+>?1}",name="user_topic_detail")
     */
    public function topic_show(Request $request,$id,$page)
    {
        $this->common_base();
        $limit = 5;
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Topic::class);
        $topic = $repository->find($id);
        if(!$topic || $topic->getUserid()->getId() != $this->user->getId())
        {
            throw $this->createNotFoundException('没有找到相关信息');
        }
        $repository_comment = $entityManager->getRepository(TopicComment::class);
        $queryBuilder = $repository_comment->createQueryBuilder('u')
                        ->where("u.topic=:id")->setParameter('id',$id)->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryBuilder,$page,$limit);
        return $this->render('home/user/topic_detail.html.twig',[
            'pagination'=>$pagination,
            'topic'=>$topic,
            'article_category' => $this->article_category,
        ]);
    }
    /**
     * @Route("/user-topic-comment-delete",name="user_topic_comment_delete",methods={"POST"})
     * 删除评论
     */
    public function topic_comment_delete(Request $request)
    {
        $id = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(TopicComment::class);
        $comment = $repository->find($id);
        if(!$comment || $comment->getUserid()->getId() != $this->user->getId())
        {
            return $this->json(['code'=>1,'msg'=>'非法请求']);
        }
        $entityManager->remove($comment);
        $entityManager->flush();
        return $this->json(['code'=>0,'msg'=>'删除成功']);
    }
}