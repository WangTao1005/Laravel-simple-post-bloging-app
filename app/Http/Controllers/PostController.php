<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->with(['user', 'likes'])->paginate(5);

        return view('posts.index', [
            'posts' => $posts
        ]);
    }

    public function show(Post $post)
    {
        return view('posts.show', [
            'post' => $post
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user()) {
            $this->validate($request, [
                'body' => 'required'
            ]);

            $request->user()->posts()->create([
                'body' => $request->body
            ]);

            return back();
        } else {
            return redirect('login');
        }
    }

    public function destroy(Post $post, Request $request)
    {
        if (!$post->ownedBy($request->user())) {
            return response(null, 409);
        }

        $post->delete();

        return back();
    }
}
