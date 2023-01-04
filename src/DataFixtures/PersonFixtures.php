<?php

namespace App\DataFixtures;

use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PersonFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Person();
        $movie->setName("aqib");
        $movie->setName("ajhds");
        $manager->persist($movie);
        $manager->flush();
    }
}
