<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
 */
class UsersController extends AbstractController
{

    private $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        /* header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET,POST,OPTIONS,DELETE,PUT');
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");
        header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
        header('Access-Control-Allow-Headers: *, origin, content-type, accept');
        header('Access-Control-Max-Age: 86400');
        header('Content-Type: application/json'); */
        $this->usersRepository = $usersRepository;
    }

    /**
     * @Route("/", name="users_index", methods={"POST"})
     */
    public function index(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($data === null ) {
            $data['username'] = $request->get('username');
            $data['password'] = $request->get('password');
        }
        $username = $data['username'];
        $password = $data['password'];
        if(!$this->checkUser($username,$password)) {
            return new JsonResponse(null, Response::HTTP_OK);
        }

        $users = $this->usersRepository->findAll();
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
                'active' => $user->getActive(),
                'notes' => $user->getNotes(),
                'categories' => $this->getCategoryByUser($user->getId())
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
        
    }

    /**
     * @Route("/login", name="users_login", methods={"GET"})
     */
    public function login(): Response
    {        
        return new response('<form method="post" action="http://personal-expense.local/users/signin"><input type="text" name="username" /><input type="password" name="password" /><input type="submit"></form>');
    }
    
    /**
     * @Route("/signin", name="user_signin", methods={"POST"})
     */
    public function signin(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if($data === null ) {
            $data['username'] = $request->get('username');
            $data['password'] = $request->get('password');
        }
        $username = $data['username'];
        $password = $data['password'];

        $id = $this->checkUser($username, $password);

        if($id !== null) {
            return new JsonResponse($id, Response::HTTP_OK);
        }
        
        return new JsonResponse(null, Response::HTTP_OK);
    }

    protected function checkUser($username, $password)
    {
        if($username == '' || $password == '') {
            return null;
        } 

        $user = $this->usersRepository->findOneBy([
            'username'=>$username,
            'password'=>md5($password),
            'active'=>1
        ]);

        if($user) {
            return $user->getId();
        }
        return null;
    }

    /**
     * @Route("/{id}", name="get_one_user", methods={"GET","POST"})
     */
    public function getOneUser($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($data === null ) {
            $data['username'] = $request->get('username');
            $data['password'] = $request->get('password');
        }
        $username = $data['username'];
        $password = $data['password'];
        
        if(!$this->checkUser($username,$password) || $id != $this->checkUser($username,$password)) {
            return new JsonResponse(null, Response::HTTP_OK);
        }
        $user = $this->usersRepository->findOneBy(['id' => $id]);
        /* $categories = $this->getDoctrine()
        ->getRepository(Categories::class)
        ->findBy(['user' => $id]); */
        $cate = $this->getCategoryByUser($id);

        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'active' => $user->getActive(),
            'notes' => $user->getNotes(),
            'categories' => $cate
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    

    protected function getCategoryByUser($id) 
    {
        $categories = $this->getDoctrine()
        ->getRepository(Categories::class)
        ->findBy(['user' => $id]);
        $cate = [];
        foreach($categories as $category) {
            $cate[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'notes' => $category->getNotes()
            ];
        }
        return $cate;
    }
}
