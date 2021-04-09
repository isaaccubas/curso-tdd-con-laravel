<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;
use App\Http\Requests\RepositoryRequest;

class RepositoryController extends Controller
{
    public function index() {
        return view('repositories.index', [
            'repositories' => auth()->user()->repositories
        ]);
    }

    public function show(Request $request, Repository $repository)
    {
        //Si un usuario intenta editar un repositorio que no es suyo
        $this->authorize('pass', $repository);

        return view('repositories.show', compact('repository'));
    }

    public function create()
    {
        return view('repositories.create');
    }

    public function store(RepositoryRequest $request)
    {

        $request->user()->repositories()->create($request->all());

        return redirect()->route('repositories.index');
    }

    public function edit(Repository $repository)
    {
        //Si un usuario intenta editar un repositorio que no es suyo
        $this->authorize('pass', $repository);

        return view('repositories.edit', compact('repository'));
    }

    public function update(RepositoryRequest $request, Repository $repository)
    {

        //Si un usuario intenta editar un repositorio que no es suyo
        $this->authorize('pass', $repository);

        $repository->update($request->all());

        return redirect()->route('repositories.edit', $repository);
    }

    public function destroy(Repository $repository)
    {
        //Si un usuario intenta editar un repositorio que no es suyo ver fichero de politica
        $this->authorize('pass', $repository);

        $repository->delete();

        return redirect()->route('repositories.index');
    }
}
