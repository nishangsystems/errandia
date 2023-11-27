<?php

namespace App\Http\Controllers\BAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Currency;
use App\Models\ItemSubCategory;
use App\Models\Manager;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Region;
use App\Models\Shop;
use App\Models\Street;
use App\Models\SubCategory;
use App\Models\Town;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    const PRODUCT_IMAGE_PATH = "uploads/products/";
    public function home(){
        // dd(1231231230);
        return view('b_admin.dashboard');
    }

    public function businesses(){
        $shops = Shop::join('shop_managers', 'shop_managers.shop_id', '=', 'shops.id')->where('shop_managers.user_id', auth()->id())->select('shops.*')->get();
        $data['businesses'] = $shops;
        return view('b_admin.businesses.index', $data);
    }

    public function create_business(){
        $data['user'] = auth()->user();
        $data['categories'] = SubCategory::orderBy('name')->get();
        $data['regions'] = Region::orderBy('name')->get();
        $data['towns'] = Town::orderBy('name')->get();
        $data['streets'] = Street::orderBy('name')->get();
        return view('b_admin.businesses.create', $data);
    }

    public function create_business_branch($slug){
        $data['user'] = auth()->user();
        $data['parent'] = Shop::whereSlug($slug)->first();
        $data['categories'] = SubCategory::orderBy('name')->get();
        $data['regions'] = Region::orderBy('name')->get();
        $data['towns'] = Town::orderBy('name')->get();
        $data['streets'] = Street::orderBy('name')->get();
        return view('b_admin.businesses.branches.create', $data);
    }

    
    public function save_business(Request $request){
        
        $validity = Validator::make($request->all(), [
            'name'=>'required', 'category'=>'required', 'region'=>'required', 
            'town'=>'required', 'street'=>'required', 'website'=>'url|nullable',
            'phone'=>'required|integer', 'phone_code'=>'required_with:phone', 
            'whatsapp_phone_code'=>'required_with:whatsapp_phone', 'whatsapp_phone'=>'integer|nullable', 'email'=>'email|required',
        ]);

        if($validity->fails()){
            return back()->with('error', $validity->errors()->first())->withInput();
        }

        $business = new \App\Models\Shop();


        $shop_data = ['name'=>$request->name, 'category_id'=>$request->category, 'description'=>$request->description,  'user_id'=>auth()->id(),  'slug'=>'bDC'.time().'swI'.random_int(100000, 999999).'fgUfre', 
                    'status'=>false, ];
        if(Shop::where(['name'=>$request->name])->count() > 0){
            return redirect(route('business_admin.businesses.index'))->with('error', 'Business with same name already exist');
        }
        // SAVE BUSINESS DATA
        $business->fill($shop_data);
        if(($file = $request->file('logo')) != null){
            $path = public_path('uploads/logos/');
            $fname = 'logo_'.time().'_'.random_int(1000, 9999).'.'.$file->getClientOriginalExtension();
            $file->move($path, $fname);
            $business->image_path = $path.$fname;
        }
        $business->save();

        // SAVE BUSINESS CONTACT INFO
        $contact_data = ['shop_id'=>$business->id, 'street_id'=>$request->street, 'phone'=>$request->phone_code.$request->phone, 'whatsapp'=>$request->whatsapp_phone != null ? $request->whatsapp_phone_code.$request->whatsapp_phone : null, 'website'=>$request->website, 'email'=>$request->email];
        \App\Models\ShopContactInfo::updateOrInsert(['shop_id'=>$business->id], $contact_data);
        
        // SET DEFAULT BUSINESS MANAGER
        $manager_data = ['shop_id'=>$business->id, 'user_id'=>auth()->id(), 'is_owner'=>true, 'status'=>true];
        \App\Models\ShopManager::updateOrInsert(['shop_id'=>$business->id, 'user_id'=>auth()->id()], $manager_data);

        return redirect(route('business_admin.businesses.index'))->with('success', 'Business successfully created');
    }

    
    public function save_business_branch(Request $request, $slug){
        
        $validity = Validator::make($request->all(), [
            'town'=>'required', 'street'=>'required', 'website'=>'url|nullable',
            'name'=>'required', 'category'=>'required', 'region'=>'required', 
            'phone'=>'required|integer', 'phone_code'=>'required_with:phone', 
            'whatsapp_phone'=>'integer|nullable', 'email'=>'email|nullable',
            'whatsapp_phone_code'=>'required_with:whatsapp_phone', 
            'fb_link'=>'url|nullable', 'ins_link'=>'url|nullable',
        ]);

        if($validity->fails()){
            return back()->with('error', $validity->errors()->first())->withInput();
        }
        
        $parent = Shop::whereSlug($slug)->first();
        if(($parent->street_id == $request->street) && ($parent->address == $request->address)){
            return back()->with('error', 'You already have a branch of this business in the specified location with the same address.');
        }
        $business = new \App\Models\Shop();
        $data = [
            'name'=>$request->name, 'category_id'=>$request->category, 'description'=>$request->description, 'region_id'=>$request->region, 'user_id'=>auth()->id(), 
            'town_id'=>$request->town, 'street_id'=>$request->street, 'website'=>$request->website, 'phone'=>$request->phone_code.$request->phone, 'slug'=>'bDC'.time().'swI'.random_int(100000, 999999).'fgUfre',
            'whatsapp_phone'=>$request->whatsapp_phone != null ? $request->whatsapp_phone_code.$request->whatsapp_phone : null, 'email'=>$request->email, 'status'=>$request->status, 'is_branch'=>$request->is_branch,
            'fb_link'=>$request->fb_link, 'ins_link'=>$request->ins_link, 'manager_id'=>$request->manager, 'address'=>$request->address, 'parent_slug'=>$request->parent_slug??null
        ];

        // if(Shop::where(['name'=>$request->name])->count() > 0){
        //     return back()->with('error', 'Business with same name already exist');
        // }
        $business->fill($data);
        if(($file = $request->file('logo')) != null){
            $path = public_path('uploads/logos/');
            $fname = 'logo_'.time().'_'.random_int(1000, 9999).'.'.$file->getClientOriginalExtension();
            $file->move($path, $fname);
            $business->image_path = $path.$fname;
        }
        // dd($request->all());
        $business->save();
        return redirect(route('business_admin.businesses.index'))->with('success', 'Business successfully created');
    }


    public function edit_business($slug){
        $data['business'] = Shop::whereSlug($slug)->first();
        if($data['business'] != null){
            $data['title'] = "Edit Business";
            $data['categories'] = SubCategory::orderBy('name')->get();
            $data['regions'] = Region::orderBy('name')->get();
            $data['towns'] = Town::orderBy('name')->get();
            $data['streets'] = Street::orderBy('name')->get();
            return view('admin.businesses.edit', $data);
        }
    }


    public function update_business(Request $request, $slug){
        
        $validity = Validator::make($request->all(), [
            'name'=>'required', 'category'=>'required', 'region'=>'required', 
            'town'=>'required', 'street'=>'required', 'website'=>'url',
            'business_type'=>'required', 'verification_status'=>'required', 'phone'=>'required|integer',
            //  'phone_code'=>'required_with:phone', 
            'whatsapp_phone_code'=>'required_with:whatsapp_phone', 'whatsapp_phone'=>'integer|nullable', 'email'=>'email|required',
        ]);

        if($validity->fails()){
            return back()->with('error', $validity->errors()->first());
        }

        $business = \App\Models\Shop::whereSlug($slug)->first();

        if($business != null){
            $data = [
                'name'=>$request->name, 'category_id'=>$request->category, 'description'=>$request->description, 'region_id'=>$request->region, 'user_id'=>auth()->id(), 
                'town_id'=>$request->town, 'street_id'=>$request->street, 'website'=>$request->website, 'phone'=>$request->phone_code.$request->phone, 'slug'=>'bDC'.time().'swI'.random_int(100000, 999999).'fgUfre',
                'whatsapp_phone'=>$request->whatsapp_phone != null ? $request->whatsapp_phone_code.$request->whatsapp_phone : null, 'email'=>$request->email, 'type'=>$request->business_type, 'status'=>$request->verification_status, 
            ];
            if(Shop::where(['name'=>$request->name])->where('slug', '!=', $slug)->count() > 0){
                return redirect(route('admin.businesses.index'))->with('error', 'Business with same name already exist');
            }
            $business->fill($data);
            
            $business->save();
            return redirect(route('admin.businesses.index'))->with('success', 'Business successfully created');
        }
        return redirect(route('admin.businesses.index'))->with('error', 'Business not found');
    }

    public function show_business ($slug){
        $data['shop'] = Shop::whereSlug($slug)->first();
        return view('b_admin.businesses.show', $data);
    }

    public function managers(){
        $data['managers'] = auth()->user()->managers;
        return view('b_admin.businesses.managers.index', $data);
    }

    public function create_manager(Request $request){
        $data['user'] = auth()->user();
        $data['businesses'] = $data['user']->shops;
        return view('b_admin.businesses.managers.create', $data);
    }


    public function save_manager(Request $request){
        // dd($request->all());
        $validity = Validator::make($request->all(), [
            'name'=>'required', 'email'=>'email|required',
            'confirm_password'=>'required|min:6', 'password'=>'required|same:confirm_password'
        ]);

        if($validity->fails()){
            return back()->with('error', $validity->errors()->first())->withInput();
        }

        if(Manager::where(['email'=>$request->email])->count() > 0){
            return back()->with('error', "A manager already exist with this email");
        }
        $data = ['name'=>$request->name, 'email'=>$request->email, 'password'=>Hash::make($request->password), 'user_id'=>auth()->id(), 'slug'=>'mana'.time().'ger'.random_int(1000000, 9999999)];
        $instance = new Manager($data);
        $instance->save();

        return redirect(route('business_admin.managers.index'))->with('success', 'Manager successfully created.');
    }


    public function business_branches($slug){
        $business = Shop::whereSlug($slug)->first();
        $data['business'] = $business;
        $data['branches'] = $business->branches;
        return view('b_admin.businesses.branches.index', $data);
    }


    public function enquiries(){
        $data['enquiries'] = [];
        return view('b_admin.enquiries.index', $data);
    }


    public function show_enquiry(Request $request, $slug){
        return view('b_admin.enquiries.create');
    }

    public function products(Request $request){
        $user = auth()->user();

        if($request->shop_slug == null)
            $data['products'] = Product::whereIn('shop_id', $user->shops()->pluck('id')->toArray())->get();
        else {
            $data['shop'] = Shop::whereSlug($request->shop_slug)->first();
            $data['products'] = $data['shop']->products??[];
        }
        return view('b_admin.products.index', $data);
    }

    public function create_products($slug){
        $user = auth()->user();
        $data['shop'] = Shop::whereSlug($slug)->first();
        $data['currencies'] = Currency::all();
        return view('b_admin.products.create', $data);
    }

    public function save_products(Request $request, $slug){

        $valid_data =$request->validate([
            'name'          =>'required',
            'tags'          =>'required',
            'image'         =>'required|image|mimes:jpeg,png,jpg|max:2048',
            'description'   =>'required',
            'price'         => 'required|numeric',
        ]);
        $shop               = Shop::whereSlug($slug)->first();
        $imageName          = time() . '.' . $valid_data['image']->extension();

        $createdProduct = Product::updateOrCreate([
            'shop_id'           => $shop->id,
            'slug'              => random_int(100, 500),
        ],[
            'description'       => $valid_data['description'],
            'featured_image'    => self::PRODUCT_IMAGE_PATH.'/'.$valid_data['name'].'/images/'.$imageName,
            'tags'              => $valid_data['tags'],
            'name'              => $valid_data['name'],
            'unit_price'        => $valid_data['price'],
        ]);

        $data['categories'] = SubCategory::orderBy('name')->get();
        $data['shop']       = $shop;
        $data['product']    = $createdProduct;

        $valid_data['image']->move(public_path(self::PRODUCT_IMAGE_PATH.'/'.$valid_data['name'].'/images/'), $imageName);
        Session::put('product', $createdProduct);

        return view('b_admin.products.create_categ_images', $data);
    }

    public function update_save_products(Request $request, $product)
    {
        $savedProduct = Product::findOrFail($product);
        $this->saveProductSubCategories($request->all()['categories'], $savedProduct);
        return redirect()->route("business_admin.products.index", ['shop_slug' => $savedProduct->shop->slug])->with("success", "Product Added Successfully");
    }

    public function services(Request $request, $slug=null){
        $user = auth()->user();
        if($request->shop_slug == null)
            $data['products'] = Product::whereIn('shop_id', $user->shops()->pluck('id')->toArray())->where('is_service', 1)->get();
        else {
            $data['shop'] = Shop::whereSlug($request->shop_slug)->first();
            $data['products'] = $data['shop']->products->where('is_service', 1)??[];
        }
        return view('b_admin.services.index', $data);
    }
    
    public function create_service($slug){
        $user = auth()->user();
        $data['shop'] = Shop::whereSlug($slug)->first();
        $data['currencies'] = Currency::all();
        return view('b_admin.services.create', $data);
    }

    public function save_service(Request $request, $slug){

        $validity = Validator::make($request->all(), ['name'=>'required', 'tags'=>'required', 'image'=>'required', 'description'=>'required']);
        if($validity->fails()){
            return back()->withInput(request()->all())->with('error', $validity->errors()->first());
        }
        $data['categories'] = SubCategory::orderBy('name')->get();
        $data['shop'] = Shop::whereSlug($slug)->first();

        return view('b_admin.services.create_categ_images', $data);
    }
    
    public function update_save_service(Request $request, $slug)
    {
        dd($request->all());
    }

    public function errands(Request $request){
        $data['errands'] = \App\Models\Errand::take(100)->get();
        return view('b_admin.errands.index', $data);
    }

    public function create_errand(){
        $data['regions'] = Region::orderBy('name')->get();
        $data['towns'] = Town::orderBy('name')->get();
        $data['streets'] = Street::orderBy('name')->get();
        return view('b_admin.errands.create', $data);
    }

    public function save_errand(Request $request){
        // save and forward errand for image update
        $data['errand'] = $request->all();
        $data['categories'] = SubCategory::orderBy('name')->get();
        return view('b_admin.errands.create_categ_images', $data);
    }

    public function update_save_errand(Request $request){
        // save and forward errand for image update
        return back()->with('success', 'Done');
    }

    public function show_errand ($slug){
        $data['errand'] = \App\Models\Errand::whereSlug($slug)->first();
        return view('b_admin.errands.show', $data);
    }

    public function edit_errand($slug){
        $data['errand'] = \App\Models\Errand::whereSlug($slug)->first();
        $data['regions'] = Region::orderBy('name')->get();
        $data['towns'] = Town::orderBy('name')->get();
        $data['categories'] = SubCategory::orderBy('name')->get();
        $data['streets'] = Street::orderBy('name')->get();
        return view('b_admin.errands.edit', $data);
    }

    public function update_errand($slug){
        $data['errand'] = \App\Models\Errand::whereSlug($slug)->first();
        $data['regions'] = Region::orderBy('name')->get();
        $data['towns'] = Town::orderBy('name')->get();
        $data['categories'] = SubCategory::orderBy('name')->get();
        $data['streets'] = Street::orderBy('name')->get();
        return view('b_admin.errands.edit', $data);
    }

    private function saveProductSubCategories($subCategories, $product)
    {
        return $product->subCategories()->attach($subCategories);

    }

    public function saveProductImages(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $image = time().'.'.$request['image']->extension;
        $product_id = Session::get('product');
        $product = Product::find($product_id);
        ProductImage::create([
            'item_id'       => $product->id,
            'image'         =>  self::PRODUCT_IMAGE_PATH.'/'.$product->name.'/images/'.$image,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now()
        ]);
        $request['image']->move(public_path(self::PRODUCT_IMAGE_PATH.'/'.$product->name.'/images/'), $image);

        return response()->json(["message" => "success"]);
    }
}
