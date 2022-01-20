<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')->get();
        $response = [
            'message' => 'List Customer',
            'data' => $customers
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
            'nama' => ['required'],
            'contact' => ['required'],
            'alamat' => ['required'],
            'diskon' => ['required', 'numeric'],
            'tipe_diskon' => ['required', 'in:fix,percent'],
            'ktp' => ['required'],
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 402);
        }

        $input['ktp'] = $this->saveImage($input['ktp']);
        
        try {
            $customer = Customer::create($input);
            $response = [
                'message' => 'Customer saved',
                'data' => $customer
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
            $customer = Customer::findOrFail($id);
            $response = [
                'message' => 'Detail customer',
                'data' => $customer
            ];
    
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Customer not found '
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $input = $request->all();
        $validator = Validator::make($input, [
            'nama' => ['required'],
            'contact' => ['required'],
            'alamat' => ['required'],
            'diskon' => ['required', 'numeric'],
            'tipe_diskon' => ['required', 'in:fix,percent'],
            'ktp' => ['required'],
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 402);
        }

        $input['ktp'] = $this->saveImage($input['ktp']);

        try {
            $customer->update($input);
            $response = [
                'message' => 'Customer updated',
                'data' => $customer
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Customer::findOrFail($id);
        try {
            $item->delete();
            $response = [
                'message' => 'Customer deleted',
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'failed ' . $e->errorInfo
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
            $safeName = 'uploads/'.md5($img).".jpg";
            Storage::disk('public')->put($safeName, $data);
            $url = Storage::url($safeName);
        }
        return $url;
    }
}
