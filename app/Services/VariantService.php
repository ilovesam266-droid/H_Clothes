<?php

namespace App\Services;

use App\Repositories\Constracts\VariantRepositoryInterface;

class VariantService
{
    protected $variantRepo;

    public function __construct(VariantRepositoryInterface $variantRepo)
    {
        $this->variantRepo = $variantRepo;
    }

    public function createVariant(int $id, $data)
    {
        $data['product_id'] = $id;
        $variant = $this->variantRepo->create($data);

        return $variant;
    }

    public function updateVariant(int $id, $data)
    {
        $variant = $this->variantRepo->update($id, $data);

        return $variant;
    }

    public function deleteVariant(int $id)
    {
        $variant = $this->variantRepo->delete($id);

        return $variant;
    }
}
