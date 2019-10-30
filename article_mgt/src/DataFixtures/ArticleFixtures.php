<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\entity\Article;
use App\entity\Category;
use App\entity\Comment;
use App\entity\Contact;
use App\entity\ArticleLikes;
use App\entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ArticleFixtures extends Fixture
{
    /**
     * Encodeur de mot de passe
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
    	$faker = \Faker\Factory::create('fr_FR');
        $users = [];

        $user = new User();
        $user->setEmail('user@dejaLu.com')
            ->setPassword($this->encoder->encodePassword($user, 'password'))
            ->setUsername($faker->UserName);

        $manager->persist($user);
        $users[] = $user;

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($user, "password"))
                ->setUsername($faker->UserName);

            $manager->persist($user);
            $users[] = $user;
        }

    	/*Créer 3 categ fakées*/
    	for($i=1; $i<=3; $i++) {
    		$category = new Category();
    		$category->setTitle($faker->sentence())
    				->setDescription($faker->paragraph());
    		$manager->persist($category);

    		/*Créer entre 4 et 6 articles*/
	    	for($j=1; $j<= mt_rand(4, 6); $j++){
	    		$content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

	    		$article = new Article();
	    		$article->setTitle($faker->sentence())
	    				->setContent($content)
	    				->setImage($faker->imageUrl())
	    				->setCreatedAt($faker->dateTimeBetween('-6 months'))
	    				->setCategory($category);
	    		$manager->persist($article);

	    		/*On donne entre 4 à 10 commentaires pour chaque article*/
	    		for($k=1; $k <= mt_rand(4, 10); $k++){

	    			$content .= '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

	    			$days = (new \DateTime())->diff($article->getCreatedAt())->days;

	    			$comment = new Comment();
	    			$comment->setAuthor($faker->name)
	    					->setContent($content)
	    					->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
	    					->setArticle($article);

	    			$manager->persist($comment);
	    		}

                /*On donne 0 à 10 likes pour chaque article*/
                for($k = 0; $k < mt_rand(1, 10); $k++){
                    $articleLikes = new ArticleLikes();
                    $articleLikes->setArticle($article)
                            ->setUser($faker->randomElement($users));

                    $manager->persist($articleLikes);
                }
	    	}
    	}

    	/*Créer entre 10 et 20 fake  contact*/
    	for($i = 1; $i <= mt_rand(10, 20); $i++){
    		$contact = new Contact();
    		$contact->setNom($faker->name)
    				->setPrenom($faker->name)
    				->setTelephone($faker->phoneNumber)
    				->setEmail($faker->email);
    		$manager->persist($contact);
    	}

        $manager->flush();
    }
}
