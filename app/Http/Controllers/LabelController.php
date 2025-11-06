<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class LabelController extends Controller
{
    public function index()
    {
        $labels = Label::paginate();
        return view('labels.index', compact('labels'));
    }

    public function show(Label $label)
    {
        // Редирект на список меток или на редактирование
        return redirect()->route('labels.index');
        
        // Или если хотите на редактирование
        // return redirect()->route('labels.edit', $label);
    }

    public function create()
    {
        if (Auth::guest()) {
            return abort(403);
        }
        return view('labels.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:labels',
            'description' => "max:1000"
        ]);

        $label = new Label();
        $label->fill($data);
        $label->save();
        flash(__('controllers.label_create'))->success();

        return redirect()->route('labels.index');
    }

    public function edit(Label $label)
    {
        if (Auth::guest()) {
            return abort(403);
        }
        return view('labels.edit', compact('label'));
    }

    public function update(Request $request, Label $label)
    {
        $data = $request->validate([
            'name' => "required|unique:labels,name,{$label->id}",
            'description' => "max:1000"
        ]);
        $label->fill($data);
        $label->save();

        flash(__('controllers.label_update'))->success();
        return redirect()->route('labels.index');
    }

    public function destroy(Label $label)
    {
        if (Auth::guest()) {
            return abort(403);
        }
        if ($label->tasks()->exists()) {
            flash(__('controllers.label_statuses_destroy_failed'))->error();
            return back();
        }
        $label->delete();

        flash(__('controllers.label_destroy'))->success();
        return redirect()->route('labels.index');
    }
}
