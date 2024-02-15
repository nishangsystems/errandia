<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\SubCategoryResource;
use App\Models\Category;
use App\Models\Shop;
use App\Models\ShopContactInfo;
use App\Models\SubCategory;
use App\Models\User;
use App\Services\ShopService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Nette\Utils\Paginator;

class ShopController extends Controller
{

    protected $shopService;

    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    public function index(Request $request)
    {
        $shops_page = Shop::orderBy('created_at', 'desc')->paginate(15);
        return $this->build_success_response(
            response(),
            'shops loaded',
            self::convert_paginated_result(
                $shops_page,
                ShopResource::collection($shops_page)
            )
        );
    }

    public function featured_shops(Request $request)
    {
        $shops = Shop::orderBy('created_at', 'desc')->take(10)->get();
        return $this->build_success_response(
            response(),
            'shops loaded',
            [
                'items' => ShopResource::collection($shops)
            ]
        );
    }

    public function getCategories()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return response()->json(['data' => CategoryResource::collection($categories)]);
    }

    public function getSubCategories(Request $request)
    {
        $categories = SubCategory::query();
        $categories = $categories->when($request->name, function ($query, $name) {
            $query->where('name', 'like', '%' . $name . '%')
                ->orWhere('description', 'like', '%' . $name . '%');
        });
        $categories = $categories->orderBy('name', 'asc')->get();
        if (empty($categories)) $categories = SubCategory::orderBy('name', 'asc')->get();

        return response()->json(['data' => SubCategoryResource::collection($categories)]);
    }

    public function store(Request $request) {
        try {
            $shopData = $request->all();
            $user = auth('api')->user();
            $shopData['user'] = $user;

            $created = $this->shopService->save($shopData);


            return $this->build_success_response(
                response(),
                'Shop created successfully',
                [
                    'item' => new ShopResource($created)
                ]
            );
        } catch (\Exception $e) {
            logger()->error('Error creating shop: ' . $e->getMessage());
            return response()->json(['data' => [
                'error' => $e->getMessage(),
                'message' => 'Sorry, we encountered an error'
            ]], 400);
        }
    }

    // get a shop by id
    public function show($slug) {
        try {
            $shop = $this->shopService->getBySlug($slug);
            return $this->build_success_response(
                response(),
                'Shop loaded',
                [
                    'item' => new ShopResource($shop)
                ]
            );
        } catch (\Exception $e) {
            logger()->error('Error loading shop: ' . $e->getMessage());
            return response()->json(['data' => [
                'error' => $e->getMessage(),
                'message' => 'Sorry, we encountered an error'
            ]], 400);
        }
    }

    // get user shops
    public function getUserShops(Request $request) {
        try {
            $user = auth('api')->user();
            $shops = $this->shopService->getUserShops($user);
            return $this->build_success_response(
                response(),
                'Shops loaded',
               self::convert_paginated_result(
                    $shops,
                    ShopResource::collection($shops)
                )
            );
        } catch (\Exception $e) {
            logger()->error('Error loading user shops: ' . $e->getMessage());
            return response()->json(['data' => [
                'error' => $e->getMessage(),
                'message' => 'Sorry, we encountered an error'
            ]], 400);
        }
    }

    public function update(Request $request, $slug) {
        try {
            $shop = $this->shopService->getBySlug($slug);

            $authenticatedUser = auth('api')->user();

            if ($shop->user_id !== $authenticatedUser->id) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'You are not authorized to update this shop.'
                ], 403);
            }

            // Handle text data
            $shopData = $request->except(['image']);
            foreach ($shopData as $key => $value) {
                if ($request->has($key)) {
                    $shop->$key = $value;
                }
            }

            // Handle file upload
            if ($request->hasFile('image')) {
                $shopImageLogo = $request->file('image');
                // TODO: Save the file to disk and update the shop's image_path
            }

            $shop->update($shopData);
            $shop->refresh();

            return $this->build_success_response(
                response(),
                'Shop updated successfully', [
                    'item' => new ShopResource($shop)
                ]
            );
        } catch (\Exception $e) {
            logger()->error('Error updating shop: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Sorry, we encountered an error while updating the shop.'
            ], 400);
        }
    }
}
