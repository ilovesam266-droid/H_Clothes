<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\Address\Resource;
use App\Services\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $id, Request $request, AddressService $addressService)
    {
        $addresses = $addressService->getAddress($request, $id);

        return Resource::collection($addresses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $id, AddressRequest $request, AddressService $addressService)
    {
        $address = $addressService->storeAddress($request->all(), $id);

        return new Resource($address);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id, AddressService $addressService)
    {
        $address = $addressService->getAddressById($id);

        return new Resource($address);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, AddressRequest $request, AddressService $addressService)
    {
        $address = $addressService->updateAddress($id, $request->validated());

        return new Resource($address);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, AddressService $addressService)
    {
        $ids = $request->all();
        $address = $addressService->deleteAddress($ids);

        return response()->json([
            'message' => 'Deleted succesfully',
            'deleted_count' => $address,
        ]);
    }
}
