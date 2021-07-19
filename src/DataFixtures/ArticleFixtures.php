<?php

namespace App\DataFixtures;
use DateTime;
use Faker\Factory;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en_FR');
        for($i=1;$i<5;$i++){

            $category= new Category();
            $category->setTitle("Theme $i");
            $category->setDescription("Description of theme $i");
            $manager->persist($category);
    
            for($j=1;$j <=2; $j ++){
                $article= new Article();
                $article->setTitle("Title $j")
                ->setContent("Why is Symfony better than just opening up a file and writing flat PHP?
                If you’ve never used a PHP framework, aren’t familiar with the Model-View-Controller (MVC) philosophy, or just wonder what all the hype is around Symfony, this article is for you. Instead of telling you that Symfony allows you to develop faster and better software than with flat PHP, you’ll see for yourself.
                In this article,")
                ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                ->setImage("https://picsum.photos/id/1/300/150")
                ->setCategory($category);

                $manager->persist($article);

                $today= new DateTime();
                $difference= $today->diff($article->getCreatedAt());
                
                $days =$difference->days;

                $comment_maximum = '-'.$days.'days';

                for($k=0;$k <= mt_rand(4,6) ;$k++)
                {
                    $comment = new Comment();
                    $comment->setAuthor($faker->name)
                            ->setContent("Why is Symfony better than just opening up a file and writing flat PHP?
                            If you’ve never used a PHP framework, aren’t familiar with the Model-View-Controller (MVC) philosophy, or just wonder what all the hype is around Symfony, this article is for you. Instead of telling you that Symfony allows you to develop faster and better software than with flat PHP, you’ll see for yourself.
                            In this article,")
                            ->setCreatedAt($faker->dateTimeBetween($comment_maximum))
                            ->setArticle($article);
                    $manager->persist($comment);
                }
            }
          }

        $manager->flush();
    }
}
