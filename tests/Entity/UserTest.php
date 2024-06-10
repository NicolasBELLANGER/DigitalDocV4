<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testPrenom()
    {
        $user = new User();

        $prenom = 'nicolas';
        $user->setPrenom($prenom);

        $this->assertEquals($prenom, $user->getPrenom());
    }
}