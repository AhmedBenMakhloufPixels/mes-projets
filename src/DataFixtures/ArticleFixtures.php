<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i=1; $i <= 50; $i++) { 
        	$article= new Article();
        	$article->setTitle("le titre de l'article n $i")
        			->setContent("<p>le contenu de l'article $i</p>")
        			->setImage("http://via.placeholder.com/350x150")
        			->setCreatedAt(new \dateTime());
        	$manager->persist($article);
        	
        }

        $manager->flush();
    }
}
