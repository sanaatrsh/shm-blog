<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Article::paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:65'],
            'text' => ['required', 'string'],
            'image' => ['image', 'nullable'],
        ]);

        $data = $request->except('image');
        $data['image'] = $this->uploadImage($request);
        $article = Article::create($data);

        return $article;

        // return redirect()->route('article.show', $article->id)
        // ->with('success', 'Article Created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return Article::findOrFail($article->id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $reservedArticle)
    {
        $request->validate([
            'title' => ['sometimes','required', 'string', 'max:65'],
            'text' => ['sometimes','required', 'string'],
            'image' => ['image', 'nullable'],
        ]);

        $article = Article::find($reservedArticle);
        $oldImage = $article->image;

        $data = $request->except('image');
        $path = $this->uploadImage($request);

        if ($path) {
            $data['image'] = $path;
        }

        $article->update($data);

        if ($oldImage && $path) {
            Storage::disk('public')->delete($oldImage);
        }

        return Response::json($article);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);

        Article::destroy($id);

        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        return [
            'message' => 'Article deleted.'
        ];
    }

    protected function uploadImage(Request $request)
    {
        if (!$request->hasFile('image')) {
            return;
        }
        $file = $request->file('image');
        $path = $file->store('uploads', [
            'disk' => 'public'
        ]);
        return $path;
    }
}
