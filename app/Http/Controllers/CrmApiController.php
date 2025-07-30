<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\CrmService;
use Illuminate\Http\Request;

class CrmApiController extends Controller
{
    public function index(CrmService $crmService)
    {
        $categoryData = $crmService->ordersGroupedByCategory();
        // dd($categoryData);
        $all_cities = $crmService->getAllCities();
        $products = Product::all();
        $categories = Category::all();
        // dd($categories);
        $cityData = $crmService->ordersGroupedByCity();

        return response()->json([
            'all_cities' => $all_cities,
            'categories' => $categories,
            'products' => $products,
            'cityData' => $all_cities, 
            'categoryData' => $categoryData
        ]);
    }
    public function index2(CrmService $crmService)
    {
        $products = Product::all();
        $categories = Category::all();
        return response()->json([
            'categories' => $categories,
            'products' => $products
        ]);
    }


    public function customers(){


        $customers = \DB::table('customers')
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->select('customers.*', 'users.*')
            ->distinct('users.email')
            ->get();
     

        
        return response()->json($customers);

        

    }
    public function products(){
        

        $pro = unit::all();
     

        
        return response()->json($pro);

        

    }
}
