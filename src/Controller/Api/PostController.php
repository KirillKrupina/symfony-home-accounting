<?php


namespace App\Controller\Api;


use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package App\Controller\Api
 */
#[Route('/api/post', name: 'api_post_')]
class PostController extends AbstractController
{

    /**
     * @param PostRepository $postRepository
     * @return JsonResponse
     */
    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getPosts(PostRepository $postRepository): JsonResponse
    {
        $data = $postRepository->findAll();
        return $this->response($data);
    }

    public function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PostRepository $postRepository
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addPost(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository): JsonResponse
    {
        try {
            $request = $this->transformJsonBody($request);

            if (!$request) {
                throw new \Exception();
            }

            $post = new Post();
            $post->setName($request->get('name'));
            $post->setDescription($request->get('description'));

            $entityManager->persist($post);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => 'Post added successfully'
            ];
            return $this->response($data);
        } catch (\Exception $exception) {
            $data = [
                'status' => 422,
                'success' => 'Data no valid'
            ];
            return $this->response($data, 422);
        }
    }

    public function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

    /**
     * @param Request $request
     * @param PostRepository $postRepository
     * @return JsonResponse
     */
    #[Route('/item', name: 'item', methods: ['POST'])]
    public function getPost(Request $request, PostRepository $postRepository)
    {
        $request = $this->transformJsonBody($request);
        $id = $request->get('id');

        $post = $postRepository->find($id);
        if (!$post) {
            $data = [
                'status' => 404,
                'errors' => 'Post not found'
            ];
            return $this->response($data, 404);
        }
        return $this->response($post);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PostRepository $postRepository
     * @return JsonResponse
     */
    #[Route('/edit', name: 'edit', methods: ['PUT'])]
    public function editPost(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository)
    {
        try {
            $request = $this->transformJsonBody($request);
            if (!$request) {
                throw new \Exception();
            }

            $id = $request->get('id');
            $post = $postRepository->find($id);
            if (!$post) {
                $data = [
                    'status' => 404,
                    'errors' => 'Post not found'
                ];
                return $this->response($data, 404);
            }

            $post->setName($request->get('name'));
            $post->setDescription($request->get('description'));

            $entityManager->persist($post);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => 'Post edited successfully'
            ];
            return $this->response($data);
        } catch (\Exception $exception) {
            $data = [
                'status' => 422,
                'success' => 'Data no valid'
            ];
            return $this->response($data, 422);
        }
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PostRepository $postRepository
     * @return JsonResponse
     */
    #[Route('/delete', name: 'delete', methods: ['POST'])]
    public function deletePost(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository)
    {
        try {
            $request = $this->transformJsonBody($request);
            if (!$request) {
                throw new \Exception();
            }

            $id = $request->get('id');
            $post = $postRepository->find($id);
            if (!$post) {
                $data = [
                    'status' => 404,
                    'errors' => 'Post not found'
                ];
                return $this->response($data, 404);
            }

            $entityManager->remove($post);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => 'Post deleted successfully'
            ];
            return $this->response($data);
        } catch (\Exception $exception) {
            $data = [
                'status' => 422,
                'success' => 'Data no valid'
            ];
            return $this->response($data, 422);
        }
    }
}