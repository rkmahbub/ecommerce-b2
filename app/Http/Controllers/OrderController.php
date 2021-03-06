<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function index()
    {
        $data['title'] = 'Order List';

        $orders = new Order();
        $orders = $orders->with('customer');
        $orders = $orders->orderBy('id','desc')->paginate(10);

        $data['orders'] = $orders;
        $data['serial'] = managePagination($orders);

        return view('admin.order.index',$data);
    }
    public function show($id)
    {
        $data['title'] = 'Order List';
        $data['order'] = Order::findOrFail($id);
        return view('admin.order.show',$data);
    }

    public function change_status($order_id,$status)
    {
        if($status == 'processing' || $status == 'shipping' || $status == 'delivered' || $status == 'canceled')
        {
            if(auth()->user()->type == 'manager' && $status != 'canceled')
            {
                Order::findOrFail($order_id)->update(['status'=>$status]);
                session()->flash('message','Order status updated successfully');
            }else{
                session()->flash('message','Unauthorized Request');
            }
            if(auth()->user()->type != 'manager')
            {
                Order::findOrFail($order_id)->update(['status'=>$status]);
                session()->flash('message','Order status updated successfully');
            }
        }
        return redirect()->back();
    }

    public function export()
    {
        return Excel::download(new OrdersExport,'order.xlsx');
    }
}
