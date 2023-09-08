<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $output = new ConsoleOutput();
        $faker = Factory::create();
        $faker->addProvider(new PicsumPhotosProvider($faker));

        // Create Super Admin
        $superAdmin = new User();
        $superAdmin->setName('Super Admin');
        $superAdmin->setEmail('super-admin@mail.com');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $superAdmin->setAvatar($faker->imageUrl(600,400, true));
        $superAdmin->setPassword($this->hasher->hashPassword($superAdmin, 'password'));
        $manager->persist($superAdmin);
        $output->writeln('Super Admin successfully created');

        // Create Admin
        $admin = new User();
        $admin->setName('Admin');
        $admin->setEmail('admin@mail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setAvatar($faker->imageUrl(600,400, true));
        $admin->setPassword($this->hasher->hashPassword($admin, 'password'));
        $manager->persist($admin);
        $output->writeln('Admin successfully created');

        // Create User
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setName($faker->userName());
            $user->setEmail($faker->email());
            $user->setRoles(['ROLE_USER']);
            $user->setAvatar($faker->imageUrl(600,400, true));
            $user->setPassword($this->hasher->hashPassword($user, $faker->password()));
            $manager->persist($user);
            $users[] = $user;
        }
        $output->writeln(sprintf("%s user successfully created", count($users)));

        // Create Tag
        $tags = [];
        for ($i = 0; $i < 10; $i++) {
            $tag = new Tag();
            $tag->setName($faker->word());
            $manager->persist($tag);
            $tags[] = $tag;
        }
        $output->writeln(sprintf("%s tag successfully created", count($tags)));

        // Create Post
        $posts = [];
        for ($i = 0; $i < 100; $i++) {
            $post = new Post();
            $post->setTitle($faker->text());
            $post->setImage($faker->imageUrl(600,400, true));
            $post->setContent($faker->text());
            $post->setSlug($faker->slug());
            $post->addTag($tags[random_int(0, (count($tags) - 1))]);
            $post->setUser($users[random_int(0, (count($users) - 1))]);
            // Add Comments
            for ($c = 0; $c < random_int(0, 5); $c++) {
                $comment = new Comment();
                $comment->setContent($faker->text());
                $comment->setUser($users[random_int(0, (count($users) - 1))]);
                $manager->persist($comment);
                $post->addComment($comment);
            }
            $manager->persist($post);
            $posts[] = $post;
        }
        $output->writeln(sprintf("%s post successfully created", count($posts)));

        $manager->flush();
    }
}
