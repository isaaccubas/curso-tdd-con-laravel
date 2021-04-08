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

    public function test_index_empty()
    {
        Repository::factory()->create(); //user_id = 1

        $user = User::factory()->create(); //id = 2

        $this
            ->actingAs($user)
            ->get('repositories')
            ->assertStatus(200)
            ->assertSee('No hay repositorios creados');

    }

    public function test_index_with_data()
    {
        $user = User::factory()->create(); //id = 1
        $repository = Repository::factory()->create(['user_id' => $user->id]); //user_id = 1


        $this
            ->actingAs($user)
            ->get('repositories')
            ->assertStatus(200)
            ->assertSee($repository->id)
            ->assertSee($repository->url);
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
        $user = User::factory()->create();

        $repository = Repository::factory()->create(['user_id' => $user->id]);
        //Probamos el registro del formulario para crear el repositorio
        //Datos del formulario
        $data = [
            'url' => $this->faker->url,
            'description' => $this->faker->text,
        ];

        //Inicia sesión con el usuario creado
        $this
            ->actingAs($user)
            ->put("repositories/$repository->id", $data)
            ->assertRedirect("repositories/$repository->id/edit");

        //Compruebo si se guardo en BD
        $this->assertDatabaseHas('repositories', $data);
    }

    /**
     *  Valido que un usuario no pueda actualizar un repo que no sea suyo
     * @return void
     */

    public function test_update_policy()
    {
        $user = User::factory()->create(); //user id = 1

        $repository = Repository::factory()->create(); //user id = 2

        $data = [
            'url' => $this->faker->url,
            'description' => $this->faker->text,
        ];

        $this
            ->actingAs($user)
            ->put("repositories/$repository->id", $data)
            ->assertStatus(403);
    }

    /**
     * Validamos si el formulario viene vacio
     *
     * @return void
     */
    public function test_validate_store()
    {
        //Validamos si el formulario llega vacio
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post('repositories', [])
            ->assertStatus(302)
            ->assertSessionHasErrors(['url', 'description']);
    }

    public function test_validate_update()
    {
        $repository = Repository::factory()->create();

        $user = User::factory()->create();

        //Inicia sesión con el usuario creado
        $this
            ->actingAs($user)
            ->put("repositories/$repository->id", [])
            ->assertStatus(302)
            ->assertSessionHasErrors(['url', 'description']);

    }

    public function test_destroy()
    {
        $user = User::factory()->create();
        $repository = Repository::factory()->create(['user_id' => $user->id]);


        //Inicia sesión con el usuario creado
        $this
            ->actingAs($user)
            ->delete("repositories/$repository->id")
            ->assertRedirect('repositories');

        //Compruebo si se guardo en BD
        $this->assertDatabaseMissing('repositories', [
            'id' => $repository->id,
            'url' => $repository->url,
            'description' => $repository->description,
        ]);
    }

    public function test_destroy_policy()
    {

        $user = User::factory()->create(); //id = 1
        $repository = Repository::factory()->create(); //id = 2

        //Inicia sesión con el usuario creado
        $this
            ->actingAs($user)
            ->delete("repositories/$repository->id")
            ->assertStatus(403);

    }

    /**
     * Comprobamos si el repositorio se visualiza correctamente
     *
     * @return void
     */
    public function test_show()
    {
        $user = User::factory()->create();

        $repository = Repository::factory()->create(['user_id' => $user->id]);

        //Inicia sesión con el usuario creado
        $this
            ->actingAs($user)
            ->get("repositories/$repository->id")
            ->assertStatus(200);

    }

    /**
     *  Valido que un usuario no pueda ver un repo que no sea suyo
     * @return void
     */

    public function test_show_policy()
    {
        $user = User::factory()->create(); //user id = 1

        $repository = Repository::factory()->create(); //user id = 2

        $this
            ->actingAs($user)
            ->get("repositories/$repository->id")
            ->assertStatus(403);
    }

    /**
     * Comprobamos si el repositorio se puede editar correctamente
     *
     * @return void
     */
    public function test_edit()
    {
        $user = User::factory()->create();

        $repository = Repository::factory()->create(['user_id' => $user->id]);

        //Inicia sesión con el usuario creado
        $this
            ->actingAs($user)
            ->get("repositories/$repository->id/edit")
            ->assertStatus(200)
            ->assertSee($repository->description)
            ->assertSee($repository->url);
    }

    /**
     *  Valido que un usuario no pueda editar un repo que no sea suyo
     * @return void
     */

    public function test_edit_policy()
    {
        $user = User::factory()->create(); //user id = 1

        $repository = Repository::factory()->create(); //user id = 2

        $this
            ->actingAs($user)
            ->get("repositories/$repository->id/edit")
            ->assertStatus(403);
    }




}
