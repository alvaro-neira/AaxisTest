<?php

namespace App\Controller;

use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;

#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    /**
     * Load the records of the created entity. The endpoints will receive a JSON payload
     *  that may contain 1 or more records to load. In case of any error you must report it
     *  in the response.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param ProductService $productService
     * @return JsonResponse
     */
    #[Route('/products', name: 'product_create', methods: ['post'])]
    public function create(ManagerRegistry $doctrine, Request $request, ProductService $productService): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $parameter = json_decode($request->getContent(), true);

        if (count($parameter) < 1) {
            return $this->json(['message' => 'No products were added']);
        }

        foreach ($parameter as $product) {
            if (!is_array($product)) {
                return $this->json([
                    'message' => 'Not all products were created successfully',
                    'error' => 'Malformed JSON'
                ]);
            }
            $productService->createOne($entityManager, $product);
        }

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Not all products were created successfully',
                'error' => $e->getMessage()
            ]);
        }
        return $this->json(['message' => 'Products created successfully']);
    }

    /**
     * List of products. The endpoint simply brings a list of all the products with their
     * data.
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/products', name: 'product_index', methods: ['get'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $products = $doctrine
            ->getRepository(Product::class)
            ->findAll();

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'product_name' => $product->getProductName(),
                'description' => $product->getDescription(),
                'created_at' => $product->getCreatedAt(),
                'update_at' => $product->getUpdateAt()
            ];
        }

        return $this->json($data);
    }


    /**
     * Update of existing records. The endpoint will receive a JSON payload with a list
     *  that may contain 1 or more records to be modified. The product identification will
     *  be through the SKU field. In the response it must inform if it was updated correctly
     *  or in case of any error inform with which SKU it occurred.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param ProductService $productService
     * @return JsonResponse
     */
    #[Route('/products', name: 'product_update', methods: ['put'])]
    public function update(ManagerRegistry $doctrine, Request $request, ProductService $productService): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $parameter = json_decode($request->getContent(), true);

        if (count($parameter) < 1) {
            return $this->json(['message' => 'No products were updated']);
        }

        foreach ($parameter as $product) {
            if (!is_array($product)) {
                return $this->json([
                    'message' => 'Not all products were updated successfully',
                    'error' => 'Malformed JSON'
                ]);
            }
            if (!$productService->updateOne($entityManager, $product)) {
                return $this->json([
                    'message' => 'Not all products were updated successfully',
                    'error' => "Could not update product with sku '" . $product['sku'] . "'"
                ]);
            }
        }

        return $this->json(['message' => 'Products updated successfully']);
    }
}
