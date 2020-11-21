<?php

namespace App\Controller;

use App\Entity\Expenses;
use App\Repository\CategoriesRepository;
use App\Repository\ExpensesRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/expenses")
 */
class ExpensesController extends AbstractController
{

    private $expensesRepository;
    private $usersRepository;
    private $categoriesRepository;

    public function __construct(ExpensesRepository $expensesRepository,
         UsersRepository $usersRepository, 
         CategoriesRepository $categoriesRepository)
    {
        //header("Access-Control-Allow-Origin: *");
        $this->expensesRepository = $expensesRepository;
        $this->usersRepository = $usersRepository;
        $this->categoriesRepository = $categoriesRepository;
    }

    /**
     * @Route("/", name="expenses_index", methods={"POST"})
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

        $expenses = $this->expensesRepository->findAll();
        $data = [];
        foreach ($expenses as $expense) {
            $data[] = [
                'id' => $expense->getId(),
                'qty' => $expense->getQty(),
                'date_time' => $expense->getDateTime()->format('Y-m-d H:i:s'),
                'notes' => $expense->getNotes(),
                'user' => $expense->getUser()->getId(),
                'category' => $expense->getCategory()->getId()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
        
    }

    /**
     * @Route("/add", name="add_expense", methods={"POST"})
     */
    public function addExpense(Request $request): JsonResponse
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
        $category = $this->categoriesRepository->find($data['category']);

        $expense =  new Expenses();
        $entityManager = $this->getDoctrine()->getManager();  

        $expense->setUser($user);
        $expense->setQty($data['qty']);
        $expense->setNotes($data['notes']);
        $expense->setCategory($category);
        $expense->setDateTime(new \DateTime($data['date_time']));

        $entityManager->persist($expense);
        $entityManager->flush();

        return new JsonResponse($expense->getId(), Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="get_one_expense", methods={"POST"})
     */
    public function getOneExpense($id, Request $request): JsonResponse
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

        $expense = $this->expensesRepository->findOneBy(['id' => $id]);
        
        $data = [
            'id' => $expense->getId(),
            'qty' => $expense->getQty(),
            'date_time' => $expense->getDateTime()->format('Y-m-d H:i:s'),
            'notes' => $expense->getNotes(),
            'user' => $expense->getUser()->getId(),
            'category' => $expense->getCategory()->getId()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_expense", methods={"PATCH"})
     */
    public function updateExpense($id, Request $request): JsonResponse
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
        $expense = $entityManager->getRepository(Expenses::class)->find($id);
        $category = $this->categoriesRepository->find($data['category']);
        
        $expense->setQty($data['qty']);
        $expense->setNotes($data['notes']);
        $expense->setCategory($category);
        $expense->setDateTime(new \DateTime($data['date_time']));

        $entityManager->flush();

        return new JsonResponse($expense->getId(), Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_expense", methods={"DELETE"})
     */
    public function deleteExpense($id, Request $request): JsonResponse
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
        $expense = $entityManager->getRepository(Expenses::class)->find($id);
  
        $entityManager->remove($expense);
        $entityManager->flush();

        return new JsonResponse('deleted', Response::HTTP_OK);
    }

    /**
     * @Route("/category/{id}", name="get_category_expenses", methods={"POST"})
     */
    public function getCategoryExpenses($id, Request $request): JsonResponse
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

        $expenses = $this->expensesRepository->findBy(['category' => $id]);
        
        $data = [];
        foreach ($expenses as $expense) {
            $data[] = [
                'id' => $expense->getId(),
                'qty' => $expense->getQty(),
                'date_time' => $expense->getDateTime()->format('Y-m-d H:i:s'),
                'notes' => $expense->getNotes(),
                'user' => $expense->getUser()->getId(),
                'category' => $expense->getCategory()->getName()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/user/{id}", name="get_user_expenses", methods={"POST"})
     */
    public function getUserExpenses($id, Request $request): JsonResponse
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

        $expenses = $this->expensesRepository->findBy(['user' => $id]);
        
        $data = [];
        foreach ($expenses as $expense) {
            $data[] = [
                'id' => $expense->getId(),
                'qty' => $expense->getQty(),
                'date_time' => $expense->getDateTime()->format('Y-m-d H:i:s'),
                'notes' => $expense->getNotes(),
                'user' => $expense->getUser()->getUsername(),
                'category' => $expense->getCategory()->getName()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/user/{id}/count", name="get_user_expenses_count", methods={"POST"})
     */
    public function getUserExpenses_count($id, Request $request): JsonResponse
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
        $count = 0;
        $expenses = $this->expensesRepository->findBy(['user' => $id]);
        $count = count($expenses);
        
        return new JsonResponse($count, Response::HTTP_OK);
    }

    /**
     * @Route("/category/{id}/count", name="get_category_expenses_count", methods={"POST"})
     */
    public function getCategoryExpenses_count($id, Request $request): JsonResponse
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
 
        $count = 0;
        $expenses = $this->expensesRepository->findBy(['category' => $id]);
        $count = count($expenses);
        
        return new JsonResponse($count, Response::HTTP_OK);
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
