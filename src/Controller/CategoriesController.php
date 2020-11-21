<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categories")
 */
class CategoriesController extends AbstractController
{

    private $categoriesRepository;
    private $usersRepository;

    public function __construct(CategoriesRepository $categoriesRepository, UsersRepository $usersRepository)
    {
        //parent->__construct();
        //header("Access-Control-Allow-Origin: *");
        $this->categoriesRepository = $categoriesRepository;
        $this->usersRepository = $usersRepository;
    }

    /**
     * @Route("/", name="categories_index", methods={"POST"})
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
        $id = $this->checkUser($username, $password);

        if($id === null) {
            return new JsonResponse(null, Response::HTTP_OK);
        }

        $categories = $this->categoriesRepository->findBy(['user'=>$id]);
        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'notes' => $category->getNotes(),
                'user' => $category->getUser()->getId()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
        
    }

    /**
     * @Route("/add", name="add_category", methods={"POST"})
     */
    public function addCategory(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($data === null ) {
            $data['username'] = $request->get('username');
            $data['password'] = $request->get('password');
        }
        $username = $data['username'];
        $password = $data['password'];
        $userId = $this->checkUser($username, $password);

        if($userId === null) {
            return new JsonResponse(null, Response::HTTP_OK);
        }
        $user = $this->usersRepository->find($userId);

        $category =  new Categories();
        $entityManager = $this->getDoctrine()->getManager();  

        $category->setUser($user);
        $category->setName($data['name']);
        $category->setNotes($data['notes']);

        $entityManager->persist($category);
        $entityManager->flush();

        return new JsonResponse($category->getId(), Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="get_one_category", methods={"POST"})
     */
    public function getOneCategory($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($data === null ) {
            $data['username'] = $request->get('username');
            $data['password'] = $request->get('password');
        }
        $username = $data['username'];
        $password = $data['password'];
        $userId = $this->checkUser($username, $password);

        if($userId === null) {
            return new JsonResponse(null, Response::HTTP_OK);
        }

        $category = $this->categoriesRepository->findOneBy(['id' => $id]);
        
        $data = [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'notes' => $category->getNotes(),
            'user' => $category->getUser()->getId()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_category", methods={"PATCH"})
     */
    public function updateCategory($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($data === null ) {
            $data['username'] = $request->get('username');
            $data['password'] = $request->get('password');
        }
        $username = $data['username'];
        $password = $data['password'];
        $userId = $this->checkUser($username, $password);

        if($userId === null) {
            return new JsonResponse(null, Response::HTTP_OK);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Categories::class)->find($id);
        //$category = $this->categoriesRepository->findOneBy(['id' => $id]);
        
  
        
        $category->setName($data['name']);
        $category->setNotes($data['notes']);

        $entityManager->flush();

        return new JsonResponse($category->getId(), Response::HTTP_OK);
    }

    

    /**
     * @Route("/delete/{id}", name="delete_category", methods={"DELETE"})
     */
    public function deleteCategory($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($data === null ) {
            $data['username'] = $request->get('username');
            $data['password'] = $request->get('password');
        }
        $username = $data['username'];
        $password = $data['password'];
        $userId = $this->checkUser($username, $password);

        if($userId === null) {
            return new JsonResponse(null, Response::HTTP_OK);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Categories::class)->find($id);
  
        $entityManager->remove($category);
        $entityManager->flush();

        return new JsonResponse('deleted', Response::HTTP_OK);
    }

    /**
     * @Route("/user/{id}", name="get_user_categories", methods={"POST"})
     */
    public function getUserCategories($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($data === null ) {
            $data['username'] = $request->get('username');
            $data['password'] = $request->get('password');
        }
        $username = $data['username'];
        $password = $data['password'];
        $userId = $this->checkUser($username, $password);

        if($userId === null) {
            return new JsonResponse(null, Response::HTTP_OK);
        }
        if($userId != $id) {
            return new JsonResponse(null, Response::HTTP_OK);
        }

        $categories = $this->categoriesRepository->findBy(['user' => $id]);
        
        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'notes' => $category->getNotes(),
                'user' => $category->getUser()->getusername()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
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
}
