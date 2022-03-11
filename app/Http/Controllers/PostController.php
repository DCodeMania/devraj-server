<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return response()->json([
			'error' => false,
			'posts' => Post::all(),
		], 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$validator = Validator::make($request->all(), [
			'title' => 'required|max:255',
			'category' => 'required|max:255',
			'content' => 'required|min:10',
			'image' => 'required|image|max:1024',
		], [
			'title.required' => 'Title is required',
			'title.max' => 'Title is too long',
			'category.required' => 'Category is required',
			'category.max' => 'Category is too long',
			'content.required' => 'Content is required',
			'content.min' => 'Content is too short',
			'image.required' => 'Image is required',
			'image.image' => 'File must be an image (jpeg, png, bmp, gif, or svg)',
			'image.max' => 'Image is too large',
		]);

		if ($validator->fails()) {
			return response()->json([
				'error' => true,
				'message' => $validator->errors(),
			], 200);
		}
		// Hello World
		$data = [
			'title' => $request->title,
			'category' => $request->category,
			'content' => $request->content,
		];

		$file = $request->file('image');
		$fileName = time() . '.' . $file->extension();
		if ($file->move(public_path('images'), $fileName)) {
			$data['image'] = $fileName;
			Post::create($data);

			return response()->json([
				'error' => false,
				'type' => 'success',
				'message' => 'Post created successfully',
			], 201);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		try {
			return response()->json([
				'error' => false,
				'post' => Post::findOrFail($id),
			], 200);
		} catch (Exception $e) {
			return response()->json([
				'error' => true,
				'type' => 'danger',
				'message' => 'Post not found',
			], 200);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		try {
			$post = Post::find($id);
			$validator = Validator::make($request->all(), [
				'title' => 'required|max:255',
				'category' => 'required|max:255',
				'content' => 'required|min:10',
			], [
				'title.required' => 'Title is required',
				'title.max' => 'Title is too long',
				'category.required' => 'Category is required',
				'category.max' => 'Category is too long',
				'content.required' => 'Content is required',
				'content.min' => 'Content is too short',
			]);

			if ($validator->fails()) {
				return response()->json([
					'error' => true,
					'message' => $validator->errors(),
				], 200);
			}

			$data = [
				'title' => $request->title,
				'category' => $request->category,
				'content' => $request->content,
			];

			if ($request->hasFile('image')) {
				$file = $request->file('image');
				$fileName = time() . '.' . $file->extension();
				if ($file->move(public_path('images'), $fileName)) {
					$data['image'] = $fileName;
					File::delete(public_path('images/' . $post->image));
				}
			}

			$post->update($data);
			return response()->json([
				'error' => false,
				'type' => 'success',
				'message' => 'Post updated successfully',
			], 200);
		} catch (Exception $e) {
			return response()->json([
				'error' => true,
				'type' => 'danger',
				'message' => 'Post not found',
			], 404);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		try {
			$post = Post::find($id);
			File::delete(public_path('images/' . $post->image));
			$post->delete();
			return response()->json([
				'error' => false,
				'type' => 'success',
				'message' => 'Post deleted successfully',
			], 200);
		} catch (Exception $e) {
			return response()->json([
				'error' => true,
				'type' => 'danger',
				'message' => 'Post not found',
			], 404);
		}
	}
}
