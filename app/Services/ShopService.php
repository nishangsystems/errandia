<?php

namespace App\Services;

use App\Models\Shop;
use App\Repositories\ShopManagerRepository;
use App\Repositories\ShopRepository;

class ShopService{

    private 
        $shopRepository,
        $validationService,
        $shopManagerRepository;

    public function __construct(
            ShopRepository $shopRepository, 
            ValidationService $validationService,
            ShopManagerRepository $shopManagerRepository
        ){
        $this->shopRepository = $shopRepository;
        $this->validationService = $validationService;
        $this->shopManagerRepository = $shopManagerRepository;
    }

    public function getAll($size = null, $category_id = null)
    {
        # code...
        return $this->shopRepository->get($size, $category_id);
    }

    public function getUserShops($user)
    {
        return $this->shopRepository->getUserShops($user);

    }

    public function getBySlug($slug)
    {
        $shop =  $this->shopRepository->getBySlug($slug);
        if ($shop == null) {
            throw new \Exception("business not found with slug " . $slug);
        }
        return $slug;
    }

    public function getManagers($shop_id)
    {
        # code...
        return $this->shopManagerRepository->get($shop_id);
    }

    public function save($data)
    {
        if(isset($data['image']) && ($file = $data['image']) != null){
            $path = public_path('uploads/logos/');
            if(!file_exists($path))
                mkdir($path, 0777, true);
            $fName = 'logo_'.time().'_'.random_int(1000, 9999).'.'.$file->getClientOriginalExtension();

            $file->move($path, $fName);

            $data['image_path'] = 'uploads/logos/'.$fName;

            logger()->info('Image uploaded for shop: ' . $data['image_path']);
        } else {
            logger()->info('No image provided for shop. Using default image');
            $data['image_path'] = asset('assets/images/logo-default.png');
        }
        return $this->shopRepository->store($data);
    }

    public function saveManager($data)
    {
        # code...
        $validationRules = ['shop_id'=>'required', 'user_id'=>'required'];
        $this->validationService->validate($data, $validationRules);
        return $this->shopManagerRepository->store($data);
    }

    public function updateManager($id, $data)
    {
        # code...
        $validationRules = [];
        $this->validationService->validate($data, $validationRules);
        return $this->shopManagerRepository->update($id, $data);
    }

    public function update($slug, $data)
    {
        # code...
        $validationRules = [];
        $this->validationService->validate($data, $validationRules);
        if(empty($data))
            throw new \Exception("No data provided for update");
        return $this->shopRepository->update($slug, $data);
    }

    public function updateContactInfo($shop_id, $contact_data)
    {
        # code...
    }

    public function updateManagers($shop_id, $manager_data)
    {
        # code...
    }

    public function delete($slug, $user_id)
    {
        # code...
        $shop = $this->shopRepository->getBySlug($slug);
        if($user_id != $shop->user_id)
            throw new \Exception("Permission denied. shop can only be deleted by the owner");
        $this->shopRepository->delete($slug);
        logger()->info("business successfully deleted");
    }

    public function load_featured_businesses($size = 10)
    {
        return Shop::orderBy('created_at', 'desc')->take($size)->get();
    }

    public function update_shop($request, $shop)
    {
        $data = $request->except(['image']);
        foreach ($data as $key => $value) {
            if ($request->has($key)) {
                $shop->$key = $value;
            }
        }

        // If the shop already has an image, delete it
        if ($request->hasFile('image')) {
            if(!empty($shop->image_path)) {
                MediaService::delete_media($shop->image_path);
            }
            $shop->image_path = MediaService::upload_media($request, 'image', 'logos');
        }

        $shop->update($data);
        $shop->refresh();
        return $shop;
    }

}