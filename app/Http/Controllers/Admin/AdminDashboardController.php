<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderChanged;
use App\Http\Controllers\Controller;
use App\Mail\NewsLetter;
use App\Models\Analyzer;
use App\Models\Blogs;
use App\Models\Contact;
use App\Models\Discount;
use App\Models\Orders;
use App\Models\Payment;
use App\Models\User;
use App\Models\Websites;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminDashboardController extends Controller
{

    public $order_number;
    public $user_number;


    public $discount_token;

    public function __construct()
    {
        $this->discount_token = Str::random(10);
    }

    public function getOrders(Request $request)
    {

        $request->validate([
            'type' => 'required',
        ]);

        $users = User::orderBy('created_at', 'desc')->get();
        $websites = Websites::orderBy('created_at', 'desc')->get();

        if ($request->type == 'all') {
            $orders =  Orders::orderBy('created_at', 'desc')->get();

            return response()->json([
                'message' => 'Success',
                'orders' => $orders,
                'users' => $users,
                'websites' => $websites,
                'request' => $request->type
            ], 200);
        } else {
            $orders = Orders::where('status', $request->type)->orderBy('created_at', 'desc')->get();
            return response()->json([
                'message' => 'Success',
                'orders' => $orders,
                'users' => $users,
                'websites' => $websites,
                'request' => $request->type

            ], 200);
        }
    }


    public function getOrder(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $order = Orders::where('order_number', $request->id)->first();

        if ($order) {

            $user  = User::where('id', $order->user_id)->first();


            if ($user) {

                $website = Websites::where('token', $order->website_token)->first();

                if ($website) {

                    return response()->json([
                        'message' => 'Success',
                        'order' => $order,
                        'client' => $user,
                        'website' => $website,
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Websites not found',
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Something went wrong',
            ], 404);
        }
    }



    public function index()
    {

        $users = User::orderBy('created_at', 'desc')->get();
        $orders = Orders::orderBy('created_at', 'desc')->get();

        $websites = Websites::orderBy('created_at', 'desc')->get();

        $users_array = Analyzer::where('data_name', 'Users')->orderBy('created_at', 'desc')->get();
        $orders_array = Analyzer::where('data_name', 'Orders')->orderBy('created_at', 'desc')->get();


        $canceled_orders = Orders::where('status', 'canceled')->count();
        $pending_orders = Orders::where('status', 'pending')->count();

        $recenetly_orders = Orders::orderBy('created_at', 'desc')->orderBy('created_at', 'desc')->limit('5')->get();
        $recenetly_users = User::orderBy('created_at', 'desc')->orderBy('created_at', 'desc')->limit('5')->get();


        $var_orders = Analyzer::where('data_name', 'orders_total')->orderBy('created_at', 'desc')->get();
        $var_users = Analyzer::where('data_name', 'users_total')->orderBy('created_at', 'desc')->get();


        foreach ($users as $user_for) {
            $order = Orders::where('user_id', $user_for->id)->first();

            if ($user_for->role !== 'admin') {
                if ($order) {
                    $this->order_number++;
                } else {
                    $this->user_number++;
                }
            }
        }

        return response()->json([
            'total_orders' => $orders->count(),
            'total_users' => $users->count(),
            'users' => $this->user_number,
            'orders' => $this->order_number,
            'users_array' => $users_array,
            'orders_array' => $orders_array,
            'recenetly_orders' => $recenetly_orders,
            'recenetly_users' => $recenetly_users,
            'canceled_orders' => $canceled_orders,
            'pending_orders' => $pending_orders,
            'var_users' => $var_users,
            'var_orders' => $var_orders,
            'websites' => $websites,
        ], 200);
    }


    public function users_index(Request $request)
    {

        $request->validate([
            'orderBy' => 'required'
        ]);

        if ($request->orderBy == 'newest') {
            $users = User::orderBy('created_at', 'desc')->get();
        } else if ($request->orderBy == 'name') {
            $users = User::orderBy('full_name')->get();
        } else if ($request->orderBy == 'orders') {
            $users = User::orderBy('orders_total', 'desc')->get();
        } else if ($request->orderBy == 'banned') {
            $users = User::where('banned', 'Yes')->get();
        }


        return response()->json([
            'message' => 'Success',
            'users' => $users,
        ], 200);
    }


    public function user_index($id)
    {

        $user = User::where('id', $id)->first();

        if ($user) {
            return response()->json([
                'user' => $user,
                'message' => 'Success'
            ], 200);
        } else {
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
    }

    public function user_del($id)
    {
        $user = User::where('id', $id)->first();

        if ($user) {

            $user->delete();

            return response()->json([
                'message' => 'Success'
            ], 200);
        } else {
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
    }


    public function setOrderStatus(Request $request)
    {


        $request->validate([
            'id' => 'required',
            'status' => 'required'
        ]);

        $order = Orders::find($request->id);


        if ($order) {
            if ($request->status === $order->status) {
                return response()->json([
                    'message' => "Can't set order status",
                ]);
            } else {
                $order->update([
                    'status' => $request->status
                ]);

                $orders = Orders::where('user_id', $order->user_id)->get();

                event(new OrderChanged($orders));

                return response()->json([
                    'message' => 'Success',
                    'order' => $order
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Unable to find order status',
            ], 404);
        }
    }


    public function website_index(Request $request)
    {
        $request->validate([
            'type' => 'required'
        ]);


        $websites = Websites::orderBy('created_at', 'desc')->get();
        if ($request->type == 'all') {

            $users = User::all();

            return response()->json([
                'message' => 'Success',
                'websites' => $websites,
                'users' => $users
            ], 200);
        } else {
            $websites = $websites->where('type', $request->type)->orderBy('created_at', 'desc')->get();

            return response()->json([
                'message' => 'Success',
                'websites' => $websites
            ], 200);
        }
    }

    public function website_create(Request $request)
    {
        $data = $request->validate([
            'website_name' => 'required|unique:websites,website_name',
            'price' => 'required',
            'image' => 'required|file',
            'description' => 'required',
            'category' => 'required',
            'old_price' => 'required',
            'stars' => 'required',
            'developing_Time' => 'required',
            'speceifications' => 'required',
            'theme_document' => 'required',
        ]);


        try {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move('uploads/websites/images', $filename);

            $doc = $request->file('theme_document');
            $filenameDoc = time() . '.' . $doc->getClientOriginalExtension();
            $doc->move('uploads/websites/themes', $filenameDoc);

            Websites::create([
                'image' => $filename,
                'price' => $data['price'],
                'website_name' => $data['website_name'],
                'description' => $data['description'],
                'old_price' => $data['old_price'],
                'category' => $data['category'],
                'stars' => $data['stars'],
                'developing_Time' => $data['developing_Time'],
                'specifications' => $data['speceifications'],
                'theme_document' => $filenameDoc,
            ]);

            return response()->json([
                'message' => 'Website created successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }




    public function news_letter(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'subject' => 'required',
            'content' => 'required',
            'image' => 'required|image',
        ]);


        $users = User::all();

        $image = $request->file('image');
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $image->move('uploads/newsletter/images', $filename);

        foreach ($users as $user) {

            Mail::to($user->email)->send(new NewsLetter($user->first_name, $request->title, $request->subject, $request->content, $filename));
        }


        return response()->json([
            'message' => 'Newsletter created successfully',
        ], 200);
    }



    public function blogs_index()
    {
        try {
            $blogs = Blogs::orderBy('created_at', 'desc')->get();



            return response()->json([
                'message' => 'Success',
                'blogs' => $blogs
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function blogs_create(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image',
        ]);

        try {
            $image = $request->file('image');
            $fileName = time() . '.' . $image->getClientOriginalExtension();
            $image->move('uploads/blogs/images', $fileName);


            Blogs::create([
                'title' => $request->title,
                'body' => $request->description,
                'image' => $fileName,
            ]);

            return response()->json([
                'message' => 'Success',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function blogs_update(Request $request, $id)
    {
        try {
            $blogs = Blogs::find($id);

            // $image = $blogs->image;
            // $fileName = time() . '.' . $image->getClientOriginalExtension();
            // $image->move('uploads/blogs/images', $fileName);

            $blogs->update([
                'title' => $blogs->title,
                'body' => $blogs->description,
                // 'image' => $fileName,
            ]);

            return response()->json([
                'message' => 'Success',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function blogs_delete($id)
    {
        try {
            $blogs = Blogs::find($id);

            $blogs->delete();

            return response()->json([
                'message' => 'Success',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }


    public function contact_index()
    {
        try {
            $contacts = Contact::orderBy('created_at', 'desc')->get();



            return response()->json([
                'message' => 'Success',
                'contacts' => $contacts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function discount_index()
    {

        try {
            $discounts = Discount::orderBy('created_at', 'desc')->get();

            return response()->json([
                'message' => 'Success',
                'discounts' => $discounts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function discount_create(Request $request)
    {

        $request->validate([
            'amount' => 'required',
            'endDate' => 'required',
            'holder' => 'required'
        ]);


        try {
            Discount::create([
                'amount' => $request->input('amount'),
                'end_data' => $request->input('endDate'),
                'holder' => $request->input('holder'),
                'token' => $this->discount_token
            ]);

            return response()->json([
                'message' => 'Discount created successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }



    public function getPayments(Request $request)
    {

        $request->validate([
            'type' => 'required',
        ]);

        $users = User::orderBy('created_at', 'desc')->get();
        $websites = Websites::orderBy('created_at', 'desc')->get();

        if ($request->type == 'all') {
            $payments =  Payment::orderBy('created_at', 'desc')->get();

            return response()->json([
                'message' => 'Success',
                'payments' => $payments,
                'users' => $users,
                'websites' => $websites,
                'request' => $request->type
            ], 200);
        } else {
            $payments = Payment::where('status', $request->type)->orderBy('created_at', 'desc')->get();
            return response()->json([
                'message' => 'Success',
                'payments' => $payments,
                'users' => $users,
                'websites' => $websites,
                'request' => $request->type

            ], 200);
        }
    }


    public function sendFileOrder(Request $request)
    {

        $request->validate([
            'file' => 'required',
            'order_id' => 'required',
        ]);

        $order = Orders::findOrFail($request->order_id);

        try {

            $file = $request->file('file');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move('uploads/orders/files', $fileName);

            $order->update([
                'file' => $fileName
            ]);

            return response()->json([
                'message' => 'Success',
                'order' => $order
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 404);
        }
    }
}
