<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;

class UserTest extends TestCase
{
    public function test_has_many_repositories()
    {
        //Creo un usuario
        $user = new User;

        //Compruebo si los repositorios de un usuario son una coleccion
        $this->assertInstanceOf(Collection::class, $user->repositories);
    }
}
