<?php

namespace App\Http\Controllers;

use App\Http\Requests\LabelStoreRequest;
use App\Http\Requests\LabelUpdateRequest;
use App\Models\Label;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Traits\AuthorizesResources;

class LabelController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Label::class, 'label', [
            'except' => ['destroy'],
        ]);
    }

    public function index()
    {
        $labels = Label::paginate();
        return view('labels.index', compact('labels'));
    }

    public function show()
    {
        return redirect()->route('labels.index');
    }

    public function create()
    {
        return view('labels.create');
    }

    public function store(LabelStoreRequest $request)
    {
        Label::create($request->validated());
        flash(__('controllers.label_create'))->success();
        return redirect()->route('labels.index');
    }

    public function edit(Label $label)
    {
        return view('labels.edit', compact('label'));
    }

    public function update(LabelUpdateRequest $request, Label $label)
    {
        $label->update($request->validated());
        flash(__('controllers.label_update'))->success();
        return redirect()->route('labels.index');
    }

    public function destroy(Label $label)
    {
        $result = Gate::inspect('delete', $label);

        if ($result->denied()) {
            flash($result->message())->error();
            return redirect()->route('labels.index');
        }

        $label->delete();
        flash(__('controllers.label_destroy'))->success();

        return redirect()->route('labels.index');
    }
}
