<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Sales;
use App\Models\SalesItem;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sales = Sales::orderBy('created_at', 'desc')->get();
        foreach($sales as &$row){
            $row->items = SalesItem::join('items', 'sales_items.item_id', '=', 'items.id')
            ->where("sales_id", $row->id)
            ->get([
                'sales_items.*', 
                'items.nama_item', 
                'items.harga_satuan', 
                'items.unit',
                'items.barang']);
            $row->customer = Customer::where("id", $row->customer_id)->first();
        }
    
        $response = [
            'message' => 'List sales',
            'data' => $sales
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'code_transaksi' => ['required'],
            'customer_id' => ['required'],
            'items' => ['required']
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 402);
        }

        $item_ids = array_column($input['items'], 'item_id');
        $items = Item::whereIn('id', $item_ids)->get()->toArray();

        $items = array_combine(array_column($items, "id"), $items);

        $customer = Customer::findOrFail($input['customer_id']);
        
        $salesData = [
            'code_transaksi' => $input['code_transaksi'],
            'tanggal_transaksi' => now(),
            'customer_id' => $input['customer_id'],
            'total_bayar' => 0,
            'total_diskon' => 0
        ];

        $total_bayar = 0;
        foreach($input['items'] as &$item){
            if($item['qty'] > $items[$item['item_id']]['stok']){
                return response()->json([
                    'message' => 'stok ' .$items[$item['item_id']]['nama_item'].' unavailable',
                ]);
            }

            $total_harga = $item['qty'] * $items[$item['item_id']]['harga_satuan'];
            $items[$item['item_id']]['stok'] -= $item['qty'];
            $total_bayar += $total_harga;
            $item['total_harga'] = $total_harga;
        }

        try {
            $sales = Sales::create($salesData);

            foreach($input['items'] as &$item){
                $item['sales_id'] = $sales['id'];
            }

            $total_diskon = $customer->tipe_diskon == 'fix' ? $customer->diskon : $total_bayar * $customer->diskon / 100; 

            $sales->update([
                'total_bayar' => $total_bayar - $total_diskon,
                'total_diskon' => $total_diskon
            ]);

            $sales_item = SalesItem::insert($input['items']);

            //update stok
            foreach($items as $item){
                // var_dump($item);
                Item::where('id', $item['id'])->update(['stok' => $item['stok']]);
            }

            $sales['items'] = $input['items'];

            $response = [
                'message' => 'Sales saved',
                'data' => $sales
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'failed ',
                'detail' => $e->errorInfo 
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function show(Sales $sales)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function edit(Sales $sales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sales $sales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sales = Sales::findOrFail($id);
        try {
            $sales->delete();
            $response = [
                'message' => 'sales deleted',
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'failed ',
                'detail' => $e->errorInfo
            ]);
        }
    }
}
