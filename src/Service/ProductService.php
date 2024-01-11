<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;

class ProductService
{
    /**
     * NOTE: it doesn't flush. You have to call flush() outside
     * @param ObjectManager $entityManager
     * @param array $data
     * @return true
     */
    public function createOne(ObjectManager $entityManager, array $data): bool
    {
        if (!array_key_exists("sku", $data)) {
            return false;
        }
        $product = new Product();
        $product->setSku($data['sku']);
        $product->setProductName($data['product_name']);
        $product->setDescription($data['description']);

        $entityManager->persist($product);

        return true;
    }

    /**
     * @param ObjectManager $entityManager
     * @param array $data
     * @return true
     */
    public function updateOne(ObjectManager $entityManager, array $data): bool
    {
        if (!array_key_exists("sku", $data)) {
            return false;
        }

        $productEntity = $entityManager->getRepository(Product::class)->findOneBy(array("sku" => $data['sku']));

        if (!$productEntity) {
            return false;
        }

        $productEntity->setSku($data['sku']);
        $productEntity->setProductName($data['product_name']);
        $productEntity->setDescription($data['description']);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}