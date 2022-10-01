<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        for ($i = 0; $i < 20; $i++) {
            $category = new Category();
            $category->setName('category '.$i);
            $category->setDate(new \DateTime());
            $category->setSlug($category->getName().' '.uniqid());
            $category->setDescription('A card is a flexible and extensible content container. It includes options for headers and footers, a wide variety of content, contextual background colors, and powerful display options. If you’re familiar with Bootstrap 3, cards replace our old panels, wells, and thumbnails. Similar functionality to those components is available as modifier classes for cards. '.uniqid());
            /*for ($j = 0;$j < 20;$j++)
            {
                $product = new Product();
                $product->setName('Product name'.$j);
                $product->setDescription('Some quick example text to build on the card title and make up the bulk of the card'.$j);
                $product->setPhoto('OIP.jpg');
                $product->setPrice(200);
                $product->setQuantity(10 + $j);
                $product->setCategory($category);
                $manager->persist($product);
            }*/

            $manager->persist($category);
        }
        $manager->flush();
    }
}