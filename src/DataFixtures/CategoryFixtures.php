<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['name' => 'Закуски', 'slug' => 'snacks', 'icon' => 'icons/snacks.png'],
            ['name' => 'Салаты', 'slug' => 'salads', 'icon' => 'icons/salad.png'],
            ['name' => 'Первые блюда', 'slug' => 'soups', 'icon' => 'icons/soup.png'],
            ['name' => 'Вторые блюда', 'slug' => 'main', 'icon' => 'icons/spaghetti.png'],
            ['name' => 'Гарниры', 'slug' => 'garnishes', 'icon' => 'icons/rice.png'],
            ['name' => 'Десерты', 'slug' => 'desserts', 'icon' => 'icons/dessert.png'],
            ['name' => 'Выпечка', 'slug' => 'bakery', 'icon' => 'icons/bakery.png'],
            ['name' => 'Напитки', 'slug' => 'drinks', 'icon' => 'icons/lemonade.png'],
            ['name' => 'Заготовки', 'slug' => 'preserves', 'icon' => 'icons/tomatoes.png'],
            ['name' => 'Соусы', 'slug' => 'sauces', 'icon' => 'icons/sauce.png'],
        ];

        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $category->setSlug($categoryData['slug']);
            $category->setIcon($categoryData['icon']);
            $manager->persist($category);
        }

        $tags = [
            ['name' => 'Завтрак', 'slug' => 'breakfast'],
            ['name' => 'Обед', 'slug' => 'lunch'],
            ['name' => 'Ужин', 'slug' => 'dinner'],
        ];

        foreach ($tags as $tagData) {
            $tag = new Tag();
            $tag->setName($tagData['name']);
            $tag->setSlug($tagData['slug']);
            $manager->persist($tag);
        }

        $manager->flush();
    }
}
