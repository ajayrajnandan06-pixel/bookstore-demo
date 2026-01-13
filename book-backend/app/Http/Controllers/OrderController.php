<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Book;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if Order model exists
        if (class_exists('\App\Models\Order')) {
            // Get orders with customer relationship
            $orders = Order::with('customer')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // If Order model doesn't exist yet, use empty collection
            $orders = collect(); // Empty collection
        }
        
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $books = Book::where('quantity', '>', 0)->orderBy('title')->get();
        
        return view('orders.create', compact('customers', 'books'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_without:customer_id|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Customer handling
            $customerId = $request->customer_id;

            if (!$customerId) {
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'phone' => $request->customer_phone,
                    'address' => $request->customer_address,
                ]);
                $customerId = $customer->id;
            }

            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $itemData) {
                $book = Book::findOrFail($itemData['book_id']);

                if ($book->quantity < $itemData['quantity']) {
                    throw new \Exception("Insufficient stock for {$book->title}");
                }

                $total = $book->price * $itemData['quantity'];
                $subtotal += $total;

                $itemsData[] = [
                    'book' => $book,
                    'quantity' => $itemData['quantity'],
                    'price' => $book->price,
                    'total' => $total,
                ];
            }

            $tax = $subtotal * 0.10;
            $totalAmount = $subtotal + $tax;

            $order = Order::create([
                'customer_id' => $customerId,
                'order_number' => 'ORD' . date('Ymd') . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => 0,
                'total' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            foreach ($itemsData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item['book']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);

                $item['book']->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.book']);
        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'Cannot delete completed orders.');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $item->book->increment('quantity', $item->quantity);
            }

            $order->items()->delete();
            $order->delete();
        });

        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

/**
 * Show the form for editing the specified resource.
 */
public function edit($id)
    {
        $order = Order::with(['customer', 'items.book'])->findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $books = Book::where('quantity', '>', 0)->orderBy('title')->get();
        
        return view('orders.edit', compact('order', 'customers', 'books'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);
        
        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully!');
    }

    
    public function markAsCompleted(Order $order)
    {
        try {
            $order->update(['status' => 'completed']);
            
            // For AJAX requests
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order marked as completed!',
                    'order' => $order
                ]);
            }
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order marked as completed!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update order.'
                ], 500);
            }
            
            return redirect()->route('orders.show', $order)
                ->with('error', 'Failed to update order status.');
        }
    }

    public function markAsPaid(Order $order)
    {
        try {
            $order->update(['payment_status' => 'paid']);
            
            // For AJAX requests
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order marked as paid!',
                    'order' => $order
                ]);
            }
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order marked as paid!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update payment status.'
                ], 500);
            }
            
            return redirect()->route('orders.show', $order)
                ->with('error', 'Failed to update payment status.');
        }
    }

    public function cancel(Order $order)
    {
        try {
            $order->update(['status' => 'cancelled']);
            
            // For AJAX requests
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order cancelled!',
                    'order' => $order
                ]);
            }
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order cancelled!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel order.'
                ], 500);
            }
            
            return redirect()->route('orders.show', $order)
                ->with('error', 'Failed to cancel order.');
        }
    }
    
    /**
     * Display invoice for an order
     */
    public function invoice(Order $order)
    {
        return view('orders.invoice', compact('order'));
    }

    /**
     * Download invoice as PDF
     */
    public function downloadInvoice(Order $order)
    {
        $pdf = Pdf::loadView('orders.invoice', compact('order'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
    
}