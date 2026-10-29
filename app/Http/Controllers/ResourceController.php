<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Faq;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function articles(Request $request)
    {
        $query = Article::where('is_published', true);
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }
        $articles = $query->latest('published_at')->paginate(9)->withQueryString();
        $categories = Article::where('is_published', true)->distinct()->pluck('category');

        return view('public.resources.index', compact('articles', 'categories'));
    }

    public function article(Article $article)
    {
        if (! $article->is_published) {
            abort(404);
        }
        $related = Article::where('is_published', true)->where('id', '!=', $article->id)->where('category', $article->category)->limit(3)->get();

        return view('public.resources.show', compact('article', 'related'));
    }

    public function faq()
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get()->groupBy('audience');

        return view('public.faq', compact('faqs'));
    }
}
