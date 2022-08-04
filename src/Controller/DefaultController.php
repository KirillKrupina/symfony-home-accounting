<?php


namespace App\Controller;


use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class DefaultController
 * @package App\Controller
 */
#[Route('/default', name: 'app_default_')]
class DefaultController extends AbstractController
{

    #[Route('/check-db', name: 'checkDb')]
    public function checkDb(EntityManagerInterface $entityManager): JsonResponse
    {
        $isConnected = false;
        try {
            $entityManager->getConnection()->connect();
            $isConnected = $entityManager->getConnection()->isConnected();
        } catch (Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
                'isConnected' => $isConnected
            ]);
        }
        return $this->json([
            'message' => 'Check connection to DB!',
            'isConnected' => $isConnected
        ]);
    }

}