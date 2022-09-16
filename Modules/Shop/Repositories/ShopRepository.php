<?php
/**
 * Created by PhpStorm.
 * User: Dengun_Guru
 * Date: 2/10/2021
 * Time: 9:49 PM
 */

namespace Modules\Shop\Repositories;


use Illuminate\Support\Facades\DB;
use Modules\Shop\Entities\Shop;
use Modules\Shop\Entities\ShopContactInfo;
use Modules\Shop\Entities\ShopSubscriber;
use Modules\Shop\Entities\ShopSubscription;

class ShopRepository
{
    protected $model;

    public function __construct(Shop $shop)
    {
        $this->model = $shop;
    }

    public function create(array $shop)
    {
        return $this->model->create($shop);
    }

    public function findById($shop_id)
    {
        return $this->model->where('id', $shop_id)->with(['user', 'category.category', 'shopContactInfo', 'products'])->first();
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->with(['category.category', 'user', 'shopContactInfo.street.town.region'])->first();
    }

    public function update($shop_id, array $shop)
    {
        return $this->model->find($shop_id)->update($shop);
    }

    public function getAll()
    {
        return $this->model->with(['category.category', 'user', 'shopContactInfo'])->orderBy('name', 'asc')->get();
    }

    public function getRandomActiveShops()
    {
        //return $this->model->where('status', 0)->inRandomOrder()->limit(6)->get();
        return $this->model->where('status', 1)->inRandomOrder()->take(18)->get();
    }

    public function delete($shop_id)
    {
        $status = false;
        DB::transaction(function () use ($shop_id, $status) {
            //find shop details and related data
            $shopModel = $this->findById($shop_id);
            //delete shop contact info
            $shopModel->shopContactInfo()->delete();
            //delete shop info
            $shopModel->delete();
            //delete shop owner account
            $status = $shopModel->user()->delete();
        });
        return $status;
    }

    public function getProductsByShop($shop_id)
    {
        return $this->model->find($shop_id)->products;
    }

    public function getPaginatedProductsByShop($shop_id)
    {
        return $this->model->find($shop_id)->products()->paginate(20);
    }

    public function saveShopRegistrationInfo($shop_id, array $reg_info)
    {
        return $this->model->find($shop_id)->shopRegistrationInfo()->create($reg_info);
    }

    public function getShopRegistrationInfo($shop_id)
    {
        return $this->model->find($shop_id)->shopRegistrationInfo;
    }

    public function saveShopContactInfo($shop_id, array $contact_info)
    {
        return $this->model->find($shop_id)->shopContactInfo()->create($contact_info);
    }

    public function updateShopContactInfo($shop_contact_id, $contact_info)
    {
        return ShopContactInfo::find($shop_contact_id)->update($contact_info);
    }

    public function getShopContactInfo($shop_id)
    {
        return $this->model->find($shop_id)->shopContactInfo;
    }

    public function saveShopBusinessProfile($shop_id, array $profile)
    {
        return $this->model->find($shop_id)->shopBusinessProfile()->create($profile);
    }

    public function getShopBusinessProfile($shop_id)
    {
        return $this->model->find($shop_id)->shopBusinessProfile;
    }

    public function subscribeToShopNotification(array $subscription)
    {
        return ShopSubscriber::create($subscription);
    }

    public function findShopSubscriptionByUserId($user_id, $shop_id)
    {
        return ShopSubscriber::where('shop_id', $shop_id)->where('user_id', $user_id)->get();
    }

    public function unSubscribeToShopNotification($user_id, $shop_id)
    {
        //check if user had subscribed before
        if ($this->findShopSubscriptionByUserId($user_id, $shop_id)->isNotEmpty()) {
            return ShopSubscriber::where('shop_id', $shop_id)->where('user_id', $user_id)->delete();
        }
    }

    public function getAllShopSubscribers($shop_id)
    {
        return ShopSubscriber::where('shop_id', $shop_id)->with('user')->get();
    }

    public function getPaginatedUserShopSubscription($userId)
    {
        return ShopSubscriber::where('user_id', $userId)->with('shop')->paginate(20);
    }

    public function getShopsByIds(array $shop_ids)
    {
        return $this->model->where('status', 1)->whereIn('id', $shop_ids)->inRandomOrder()->limit(6)->get();
    }

    public function savePackageSubscriptionInfo(array $subscriptionDatails)
    {
        return ShopSubscription::create($subscriptionDatails);
    }

    public function updateShopSubscription($subscriptionId, array $subscription)
    {
        return ShopSubscription::find($subscriptionId)->update($subscription);
    }

    public function getShopSubcription()
    {
        //return $this->model->with(['shop_subscriptions', 'subscriptions'])->get();
        $Slider = ShopSubscription::join('subscriptions', 'shop_subscriptions.subscription_id', '=', 'subscriptions.id')
            ->join('shops', 'shops.id', '=', 'shop_subscriptions.shop_id')
            ->with('shop')
            ->get(['shop_subscriptions.shop_id as shop_id','shop_subscriptions.id as shop_subscription_id', 'shops.name as ShopName', 'subscriptions.name as SubName', 'subscriptions.duration', 'shop_subscriptions.start_date', 'shop_subscriptions.end_date'])->all();

        return $Slider;
    }

    public function hasActiveSubscriptionShop($shopId)
    {
        $response = false;
        $query = ShopSubscription::where('shop_id', $shopId)->first();
        if (!empty($query)) {
            //check if the subscription is still active
            if (strtotime($query->end_date) > strtotime(date("Y-m-d"))) $response = true;
        }
        return $response;
    }
}
