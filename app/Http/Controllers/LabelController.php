<?php

namespace App\Http\Controllers;

use App\Http\Requests\LabelStoreRequest;
use App\Http\Requests\LabelUpdateRequest;
use App\Models\Label;

class LabelController extends Controller
{
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
        $this->authorize('create', Label::class);
        return view('labels.create');
    }

    public function store(LabelStoreRequest $request)
    {
        $this->authorize('create', Label::class);

        Label::create($request->validated());
        flash(__('controllers.label_create'))->success();
        return redirect()->route('labels.index');
    }

    public function edit(Label $label)
    {
        $this->authorize('update', $label);
        return view('labels.edit', compact('label'));
    }

    public function update(LabelUpdateRequest $request, Label $label)
    {
        $this->authorize('update', $label);

        $label->update($request->validated());
        flash(__('controllers.label_update'))->success();
        return redirect()->route('labels.index');
    }

    public function destroy(Label $label)
    {
        try {
            $this->authorize('delete', $label);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            flash(__('controllers.label_statuses_destroy_failed'))->error();
            return redirect()->route('labels.index');
        }

        $label->delete();
        flash(__('controllers.label_destroy'))->success();
        return redirect()->route('labels.index');
    }
}
