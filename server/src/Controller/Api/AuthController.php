<?php


namespace App\Controller\Api;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AuthController
 * @package App\Controller\Api
 */
class AuthController extends ApiController
{
    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @return JsonResponse
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): JsonResponse
    {
        try {
            $request = $this->transformJsonBody($request);
            $username = $request->get('username');
            $password = $request->get('password');
            $email = $request->get('email');

            if (
                empty($username) ||
                empty($password) ||
                empty($email)
            ) {
                return new JsonResponse([
                    'success' => false
                ], 200);
            }

            $user = new User();
            $user->setUsername($username);
            $user->setEmail($password);

            $hashedPassword = $userPasswordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();


            return new JsonResponse([
                'success' => true,
                'id' => $user->getId()
            ], 200);
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
            return new JsonResponse([
                'success' => true,
                'message' => $error
            ]);
        }
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTTokenManager
     * @return JsonResponse
     */
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTTokenManager): JsonResponse
    {
        return new JsonResponse([
            'token' => $JWTTokenManager->create($user)
        ]);
    }
}