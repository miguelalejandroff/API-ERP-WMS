<?php

namespace App\Http\Controllers;

use App\Models\mongodb\Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TrackingController extends Controller
{

    /**
     * GET /resource → index()
     */
    public function index()
    {
        // Recuperar todos los registros de seguimiento
        $trackings = Tracking::all();

        // Devolver los registros como una respuesta JSON
 
        return response()->json($trackings, 200, [], JSON_PRETTY_PRINT);

        foreach($trackings as $row){
            if(!is_null($row->errors)){
                return response()->json($row,200,[], JSON_PRETTY_PRINT);
            }
            if($row->status != 200){
                return response()->json($row,200,[], JSON_PRETTY_PRINT);
            }
        }
        return response()->json($trackings[0], 200, [], JSON_PRETTY_PRINT);

        return response()->json(count($trackings), 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * POST /resource → store()
     */
    public function store(Request $request)
    {

        return response()->json("store");
        /*
        $post = Post::create($request->all());
        return redirect()->route('posts.index');
        */
    }

    /**
     * GET /resource/{id} → show()
     */
    public function show(Tracking $post)
    {
        return response()->json("show");
        /*
        return view('posts.show', compact('post'));
        */
    }

    /**
     * GET /resource/{id}/edit → edit()
     */
    public function edit(Tracking $post)
    {
        return response()->json("edit");
        /*
        return view('posts.edit', compact('post'));
        */
    }

    /**
     * PUT /resource/{id} → update()
     */
    public function update(Request $request, Tracking $post)
    {
        return response()->json("update");
        /*
        $post->update($request->all());
        return redirect()->route('posts.index');
        */
    }

    /**
     * DELETE /resource/{id} → destroy()
     */
    public function destroy($id)
    {
        Tracking::truncate();
        $model = Tracking::find($id);

        if ($model) {
            $model->delete();
            return response()->json('Modelo eliminado exitosamente');
        }

        return response()->json("Modelo no encontrado {$id}");
    }
}
