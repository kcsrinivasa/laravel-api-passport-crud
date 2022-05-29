<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $res = [
            'message' => 'Post records',
            'posts' => $posts = auth()->user()->posts
        ];
        return response($res,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
        ]);

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        $res = [
            'message' => 'Post added successfully',
            'post' => $post
        ];
        return response($res,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($post_id)
    {
        $post = auth()->user()->posts()->find($post_id);

        if($post){
            $res = [
                'message' => 'Post fetched successfully',
                'post' => $post
            ];
            return response($res,200);
        }else{
            $res = [ 'message' => 'Post not found'];
            return response($res,404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $post_id)
    {
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
        ]);

        $post = auth()->user()->posts()->find($post_id);

        if($post){
            $post->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);
            $res = [
                'message' => 'Post updated successfully',
                'post' => $post
            ];
            return response($res,200);
        }else{
            $res = [ 'message' => 'Post not found'];
            return response($res,404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($post_id)
    {
        $post = auth()->user()->posts()->find($post_id);

        if($post){
            $post->delete();
            $res = [
                'message' => 'Post deleted successfully',
            ];
            return response($res,200);
        }else{
            $res = [ 'message' => 'Post not found'];
            return response($res,404);
        }
    }
}
