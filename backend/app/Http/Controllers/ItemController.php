<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($query = null)
    {
        if(!empty($query)){
            $items = Item::whereRaw('nama_item like ?', ["%{$query}%"])->orderBy('created_at', 'desc')->get();
        }else{
            $items = Item::orderBy('created_at', 'desc')->get();
        }
        
        $response = [
            'message' => 'List',
            'data' => $items
        ];
        return response()->json($response, Response::HTTP_OK);
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
        $input = $request->all();
        $validator = Validator::make($input, [
            'nama_item' => ['required'],
            'stok' => ['required', 'numeric'],
            'unit' => ['required', 'in:kg,pcs'],
            'harga_satuan' => ['required', 'numeric'],
            'barang' => ['required'],
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 402);
        }

        $input['barang'] = $this->saveImage($input['barang']);


        try {
            $item = Item::create($input);
            $response = [
                'message' => 'item saved',
                'data' => $item
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'failed ' ,
                'detail' => $e->errorInfo
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $item = Item::findOrFail($id);
            $response = [
                'message' => 'Detail item',
                'data' => $item
            ];
    
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Item not found '
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $input = $request->all();
        $validator = Validator::make($input, [
            'nama_item' => ['required'],
            'stok' => ['required', 'numeric'],
            'unit' => ['required', 'in:kg,pcs'],
            'harga_satuan' => ['required', 'numeric'],
            'harga_satuan' => ['required'],
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 402);
        }

        $input['barang'] = $this->saveImage($input['barang']);

        try {
            $item->update($input);
            $response = [
                'message' => 'Item updated',
                'data' => $item
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'failed ',
                'detail' => $e->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        try {
            $item->delete();
            $response = [
                'message' => 'Item deleted',
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'failed ',
                'detail' => $e->errorInfo
            ]);
        }
        
    }

    private function saveImage($img){
        $base64_image = $img;
        $url = '';
        //decode base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
            $data = substr($base64_image, strpos($base64_image, ',') + 1);
        
            $data = base64_decode($data);
            $safeName = 'uploads/barang/'.md5($img).".jpg";
            Storage::disk('public')->put($safeName, $data);
            $url = Storage::url($safeName);
        }
        return $url;
    }
}
