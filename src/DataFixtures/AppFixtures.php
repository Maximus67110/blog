<?php

namespace App\DataFixtures;

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

        // Create Admin
        $admin = new User();
        $admin->setName('Admin');
        $admin->setEmail('admin@mail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setAvatar($faker->imageUrl(600,400, true));
        $admin->setPassword($this->hasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        // Create User
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $users[$i] = new User();
            $users[$i]->setName($faker->userName());
            $users[$i]->setEmail($faker->email());
            $users[$i]->setRoles(['ROLE_USER']);
            $users[$i]->setAvatar($faker->imageUrl(600,400, true));
            $users[$i]->setPassword($this->hasher->hashPassword($users[$i], $faker->password()));
            $manager->persist($users[$i]);
        }
        $output->writeln(sprintf("%s user successfully created", count($users)));

        // Create Tag
        $tags = [];
        for ($i = 0; $i < 10; $i++) {
            $tags[$i] = new Tag();
            $tags[$i]->setName($faker->word());
            $manager->persist($tags[$i]);
        }
        $output->writeln(sprintf("%s tag successfully created", count($tags)));

        // Create Post
        $posts = [];
        for ($i = 0; $i < 100; $i++) {
            $posts[$i] = new Post();
            $posts[$i]->setTitle($faker->text());
            $posts[$i]->setImage($faker->imageUrl(600,400, true));
            $posts[$i]->setContent($faker->text());
            $posts[$i]->setSlug($faker->slug());
            $posts[$i]->addTag($tags[random_int(0, (count($tags) - 1))]);
            $posts[$i]->setUser($users[random_int(0, (count($users) - 1))]);
            $manager->persist($posts[$i]);
        }
        $output->writeln(sprintf("%s post successfully created", count($posts)));

        $manager->flush();
    }
}
