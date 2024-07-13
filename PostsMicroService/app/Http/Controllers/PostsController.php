<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\AuthenticationMicroServiceConnection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    /**
     * @throws ConnectionException
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $bearerToken = $request->bearerToken();
        $user = AuthenticationMicroServiceConnection::getInstance()->getUser($bearerToken);

        return Post::query()->create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $user->id
        ]);
    }


    public function getUserPosts(Request $request)
    {
        $bearerToken = $request->bearerToken();
        $user = AuthenticationMicroServiceConnection::getInstance()->getUser($bearerToken);

        return Post::query()->where('user_id', $user->id)->get();
    }

    public function deletePost(Request $request, $postId)
    {
        $bearerToken = $request->bearerToken();
        $user = AuthenticationMicroServiceConnection::getInstance()->getUser($bearerToken);

        $post = Post::query()
            ->where('id', $postId)
            ->first();

        if ($post && $post->user_id == $user->id) {
            $post->delete();
            return response()->json("success, post with ID:.'$postId'. deleted successfully");
        }
        abort(400, $post ? 'Post does not belong to this user!' : 'The post with this ID does not exist!');
    }
}
