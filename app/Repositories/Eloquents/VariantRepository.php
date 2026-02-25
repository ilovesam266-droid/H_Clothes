<?php

namespace App\Repositories\Eloquents;

use App\Models\Variant;
use App\Repositories\Constracts\VariantRepositoryInterface;

class VariantRepository extends BaseRepository implements VariantRepositoryInterface
{
    public function getModel()
    {
        return Variant::class;
    }
}
