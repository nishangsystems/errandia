<?php

namespace App\Services;

use App\Repositories\ShopSubscriptionRepository;
use Exception;
use \Illuminate\Support\Facades\Http;

class ShopSubscriptionService{

    private $shopSubscriptionRepository;
    private $validationService;

    public function __construct(ShopSubscriptionRepository $shopSubscriptionRepository, ValidationService $validationService){
        $this->shopSubscriptionRepository = $shopSubscriptionRepository;
        $this->validationService = $validationService;
    }

    public function get($size = null, $shop_id = null)
    {
        # code...
        return $this->shopSubscriptionRepository->get($size, $shop_id);
    }

    public function getOne($id)
    {
        # code...
        return $this->shopSubscriptionRepository->getById($id);
    }

    public function save($data)
    {
        # code...
        $validationRules = [];
        $this->validationService->validate($data, $validationRules);
        return $this->shopSubscriptionRepository->store($data);
    }

    public function update($id, $data)
    {
        # code...
        $validationRules = [];
        $this->validationService->validate($data, $validationRules);
        return $this->shopSubscriptionRepository->update($id, $data);
    }

    public function delete($id, $user_id)
    {
        # code...
        $shop = $this->shopSubscriptionRepository->getById($id)->shop;
        if($user_id != $shop->user_id)
            throw new Exception("Permission denied. Can only be deleted by the shop owner.");
        return $this->shopSubscriptionRepository->delete($id);
    }

}