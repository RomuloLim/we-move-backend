<?php

namespace Modules\Operation\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RequisitionDocument extends Pivot
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'requisition_id',
        'document_id',
    ];
}
