<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Customer;
use App\User;
use App\Order;
use Excel;
use App\Address;
use App\Exports\CustomersExport;
use App\Exports\UsersExport;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $customers = Customer::orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'customer')->where(function($user) use ($sort_search){
                $user->where('name', 'like', '%'.$sort_search.'%')->orWhere('email', 'like', '%'.$sort_search.'%');
            })->pluck('id')->toArray();
            $customers = $customers->where(function($customer) use ($user_ids){
                $customer->whereIn('user_id', $user_ids);
            });
        }
        $customers = $customers->paginate(15);

        if($request->button == 'export'){
            return back() ;
           
        }
        return view('backend.customer.customers.index', compact('customers', 'sort_search'));
    }
     public function export() 
        {
            return Excel::download(new UsersExport,'users.xlsx');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $customer = User::where('id',$id)->select('id','name','email','address','phone')->get();
        return view('backend.customer.customers.edit' , compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
          $request->validate([
            'name' => 'required',
            'id' => 'required',
        ]);
        $user= User::findOrFail($request->id);
        $user->update($request->all());
        
        $user_id= $request->id;
        $set_default =1 ;
        Address::where(function($query) use ($user_id, $set_default) {
            $query->where('user_id', $user_id)
        ->where('set_default', $set_default);
        })->update(['address' => $request->address,'phone'=>$request->phone]);
        
        flash(translate('Customer has been updated successfully'))->success();
        return redirect()->route('customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Order::where('user_id', Customer::findOrFail($id)->user->id)->delete();
        User::destroy(Customer::findOrFail($id)->user->id);
        if(Customer::destroy($id)){
            flash(translate('Customer has been deleted successfully'))->success();
            return redirect()->route('customers.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function login($id)
    {
        $customer = Customer::findOrFail(decrypt($id));

        $user  = $customer->user;

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }

    public function ban($id) {
        $customer = Customer::findOrFail($id);

        if($customer->user->banned == 1) {
            $customer->user->banned = 0;
            flash(translate('Customer UnBanned Successfully'))->success();
        } else {
            $customer->user->banned = 1;
            flash(translate('Customer Banned Successfully'))->success();
        }

        $customer->user->save();

        return back();
    }
}
