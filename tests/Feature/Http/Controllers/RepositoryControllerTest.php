<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Repository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RepositoryControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_guest()
    {
        //Protejemos las rutas si el usuario no esta logado le redirijo al login
        $this->get('repositories')->assertRedirect('login');        //index
        $this->get('repositories/1')->assertRedirect('login');      //show
        $this->get('repositories/1/edit')->assertRedirect('login'); //edit
        $this->put('repositories/1/')->assertRedirect('login');     //update
        $this->delete('repositories/1')->assertRedirect('login');   //destroy
        $this->get('repositories/create')->assertRedirect('login'); //create
        $this->post('repositories', [])->assertRedirect('login');   //store
    }

    public function test_store()
    {
        //Probamos el registro del formulario para crear el repositorio
        //Datos del formulario
        $data = [
            'url' => $this->faker->url,
            'description' => $this->faker->text,
        ];

        $user = User::factory()->create();

        //Inicia sesión con el usuario creado
        $this
            ->actingAs($user)
            ->post('repositories', $data)
            ->assertRedirect('repositories');

        //Compruebo si se guardo en BD
        $this->assertDatabaseHas('repositories', $data);
    }

    public function test_update()
    {
        $repository = Repository::factory()->create();
        //Probamos el registro del formulario para crear el repositorio
        //Datos del formulario
        $data = [
            'url' => $this->faker->url,
            'description' => $this->faker->text,
        ];

        $user = User::factory()->create();

        //Inicia sesión con el usuario creado
        $this
            ->actingAs($user)
            ->put("repositories/$repository->id", $data)
            ->assertRedirect("repositories/$repository->id/edit");

        //Compruebo si se guardo en BD
        $this->assertDatabaseHas('repositories', $data);
    }
}
