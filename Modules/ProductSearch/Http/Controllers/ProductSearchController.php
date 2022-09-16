<?php
namespace Modules\ProductSearch\Http\Controllers;

use App\Jobs\SendProductQuoteByEmail;
use App\Jobs\SendProductQuoteBySMS;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Product\Services\ProductService;
use Modules\ProductCategory\Services\CategoryService;
use Modules\Utility\Services\UtilityService;
use Modules\ProductSearch\Entities\ProductSearch;
use Modules\ProductCategory\Entities\SubCategory;
use Modules\Regions\Entities\Regions;
use Modules\Street\Entities\Street;
use Modules\Product\Http\Requests\AddQuoteRequest;
use Modules\Product\Services\ProductQuoteService;

use Modules\Utility\Services\ImageUploadService;

class ProductSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function __construct(ProductSearch $ProductSearch, CategoryService $categoryService, UtilityService $utilityService, SubCategory $SubCategory, Regions $Regions, Street $Street)
    {
        $this->ProductSearch = $ProductSearch;
        $this->utilityService = $utilityService;
        $this->categoryService = $categoryService;
        $this->SubCategory = $SubCategory;
        $this->Regions = $Regions;
        $this->Street = $Street;
    }

    public function index()
    {
        
        if (empty($_REQUEST['search']) || $_REQUEST['search'] == "") return redirect()->route('general_home');
        $keyword = $_REQUEST['search'];
        $data['keyword'] = $keyword;
        $data['request']['category'] = $_REQUEST['subcategory'] = 0;
        $data['request']['sub_category'] = '';
        $data['request']['region'] = '';
        $data['request']['town'] = '';
        $data['request']['street'] = '';
        $data['request']['MinPrice'] = '';
        $data['request']['MaxPrice'] = '';
        $data['products'] = $this->ProductSearch->getAllSearchProduct($keyword);
        $data['TotalProducts'] = $this->ProductSearch->getTotalSearchProduct($keyword);
        $data['currencies'] = $this->utilityService->getCurrencies();

        // FOR SORT CATEGORY SUB CATEGORY
        $data['categories'] = $this->categoryService->getAllCategories();
        $data['SubCategories'] = $this->SubCategory->getAllSubCategories();

        $data['regions'] = $this->Regions->getAllRegions();
        $data['towns'] = $this->Regions->getTowns();
        $data['streets'] = $this->Street->getAllStreets();

        return view('productsearch::index')->with($data);
    }

    public function productsort()
    {
        $keyword = $_REQUEST['keyword'];
        $data['keyword'] = $keyword;
        $data['request'] = $_REQUEST;
        $data['products'] = $this->ProductSearch->getAllSortProduct($keyword, $_REQUEST);
        $data['TotalProducts'] = $this->ProductSearch->getTotalSortProduct($keyword, $_REQUEST);
        $data['currencies'] = $this->utilityService->getCurrencies();
        // FOR SORT CATEGORY SUB CATEGORY
        $data['categories'] = $this->categoryService->getAllCategories();
        $data['SubCategories'] = $this->SubCategory->getAllSubCategories();

        $data['regions'] = $this->Regions->getAllRegions();
        $data['towns'] = $this->Regions->getTowns();
        $data['streets'] = $this->Street->getAllStreets();
        return view('productsearch::index')->with($data);
    }

    /**
     * Post product quote action
     */
    public function sendProductQuote(ProductQuoteService $ProductQuoteService, AddQuoteRequest $request, ImageUploadService $imageUploadService, ProductService $productService)
    {
        if (!auth()->check()) return redirect()->route("login_page", ['redirectTo' => route('run_errand_page')])->withErrors([trans('general.errands_custom_view_request_auth_msg')]);
        $user = Auth::user();
        //$_POST['PhoneNumber']
        $quoteData['title'] = $_POST['Title'];
        $quoteData['phone_number'] = $user->tel;
        $quoteData['description'] = $_POST['Description'];
        $quoteData['UserID'] = $user->id;
        $quoteData['sub_category_id'] = $_POST["dialogCategory"];
        $quoteData['created_at'] = date("Y-m-d h:i:s");
        $quoteData['updated_at'] = date("Y-m-d h:i:s");

        $quoteID = $ProductQuoteService->saveProductQuote($quoteData);

        // FOR ENQUIRY IMAGE
        if ($quoteID) {
            $extraProductImages = $request->getProductQuoteImages();
            $totalImages = count($extraProductImages);
            $counter = 0;
            if ($totalImages > 0) {
                foreach ($extraProductImages as $image) {
                    //upload image then save to db
                    $imagePath = $imageUploadService->uploadFile($image, key($image), "productquote");
                    $ProductQuoteService->saveQuoteImages($quoteID->id, ['image_path' => $imagePath, 'quote_id' => $quoteID->id]);
                    $counter++;
                }
                $quoteUrl = Str::random(5) . $quoteID->id;
                $updateQuote = array('slug' => $quoteUrl);
                $ProductQuoteService->updateQuote($updateQuote, $quoteID->id);
                //get all active shop owners' contact and send an sms to them about
                $regionFilter = $_POST['region'];
                $townFilter = $_POST['town'];
                $streetFilter = $_POST['street'];
                $searchCriteria = array('subCategory' => $quoteData['sub_category_id'], 'region' => $regionFilter, 'town' => $townFilter, 'street' => $streetFilter);
                $shopContacts = $productService->getShopsBySubCategory($searchCriteria);
                $shopsTels = $shopContacts['tel'];
                if (!$shopsTels->isEmpty()) {
                    //send sms to all contacts
                    $shopContactsList = $shopsTels->map(function ($tel) {
                        return "237" . $tel;
                    });
                    $data = $shopContactsList->toArray();
                    $quoteLink = route('showCustomQuotePage', ['url' => $quoteUrl]);
                    $message = trans('general.product_quote_sms_msg', ['link' => $quoteLink]);
                    //send sms notification to show owners
                    SendProductQuoteBySMS::dispatchSync(array('message' => $message, 'contacts' => $data));
                    //send email notifications to show owners as well.
                    $shopEmailList = $shopContacts['email'];
                    $emailData = $shopEmailList->toArray();
                    $quoteID['image'] = collect($ProductQuoteService->getQuoteImages($quoteID->id))->first();
                    $quoteObj = array('link' => $quoteLink, 'quote' => $quoteID);
                    SendProductQuoteByEmail::dispatchSync(array('quote' => $quoteObj, 'emails' => $emailData));
                } else {
                    session()->flash('message', trans('general.errands_not_sent_msg'));
                    return redirect()->back()->withErrors([trans('general.errands_not_sent_msg')]);
                }
            }
        } else {
            session()->flash('message', trans('general.errands_not_sent_msg'));
            return redirect()->back()->withErrors([trans('general.errands_not_sent_msg')]);
        }
        session()->flash('message', trans('Product Quote successfully sent !'));
        return redirect()->route('productsearch', ['search' => $quoteData['title']])->with(['success' => trans('Product Quote successfully sent !')]);
    }

    public function showCustomQuotePage($quoteUrl, ProductQuoteService $productQuoteService)
    {
        $quoteExist = $productQuoteService->findQuoteBySlugUrl($quoteUrl);
        if (empty($quoteExist)) {
            return redirect()->route('general_home')->withErrors([trans('general.quote_not_found')]);
        }

        $data['featured_image'] = $quoteExist->images->shift();
        $data['quote'] = $quoteExist;
        return view('productsearch::custom_quote')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function showCustomProductSearchPage()
    {
        if (!auth()->check()) return redirect()->route("login_page", ['redirectTo' => route('run_errand_page')])->withErrors([trans('general.errands_custom_view_request_auth_msg')]);
        return view('productsearch::search_errands');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('productsearch::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('productsearch::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}

?>
