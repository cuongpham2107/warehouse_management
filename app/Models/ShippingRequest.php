<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\ModelStates\HasStates;
use App\Models\User;
use App\Models\ShippingRequestItem;
use App\Enums\CrateStatus;
use App\Enums\PalletActivityAction;
use App\Enums\PalletStatus;
use App\Enums\ShippingRequestStatus;

class ShippingRequest extends Model
{
    use HasFactory, HasStates;

    protected $fillable = [
        'request_code',
        'customer_name',
        'customer_contact',
        'delivery_address',
        'requested_date',
        'lifting_time',
        'transport_garage',
        'vehicle_capacity',
        'departure_time',
        'license_plate',
        'driver_name',
        'driver_phone',
        'seal_number',
        'priority',
        'status',
        'notes',
        'status',
        'created_by',
    ];
    protected $primaryKey = 'id';

    protected $casts = [
        'requested_date' => 'datetime',
        'departure_time' => 'datetime',
        'status' => ShippingRequestStatus::class,
        'created_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

   
    /**
     * Get the user who created this shipping request.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all items for this shipping request.
     */
    public function items()
    {
        return $this->hasMany(ShippingRequestItem::class);
    }

    /**
     * Xóa yêu cầu vận chuyển và hoàn các pallet liên quan về trạng thái lưu kho.
     */
    public function delete()
    {
        if ($this->status === ShippingRequestStatus::COMPLETED) {
            throw new \DomainException('Không thể xóa yêu cầu vận chuyển đã hoàn thành.');
        }

        return DB::transaction(function () {
            $this->returnPalletsToStorage();

            return parent::delete();
        });
    }

    /**
     * Hoàn tất cả pallet của yêu cầu về trạng thái "stored".
     * Bỏ qua pallet hư hỏng và pallet đang thuộc yêu cầu khác còn hiệu lực.
     */
    public function returnPalletsToStorage(): void
    {
        $crateIds = $this->items()
            ->pluck('crate_id')
            ->filter()
            ->unique()
            ->values();

        if ($crateIds->isEmpty()) {
            return;
        }

        // Không đụng tới các crate đang nằm trong yêu cầu khác còn hiệu lực.
        $busyCrateIds = ShippingRequestItem::query()
            ->whereIn('crate_id', $crateIds)
            ->where('shipping_request_id', '!=', $this->getKey())
            ->whereHas('shippingRequest', fn ($query) => $query->whereIn('status', [
                ShippingRequestStatus::PENDING->value,
                ShippingRequestStatus::IN_PROGRESS->value,
            ]))
            ->pluck('crate_id');

        $pallets = Pallet::query()
            ->with('crate')
            ->whereIn('crate_id', $crateIds->diff($busyCrateIds))
            ->whereIn('status', [
                PalletStatus::IN_TRANSIT->value,
                PalletStatus::IN_STOCK->value,
                PalletStatus::SHIPPED->value,
            ])
            ->get();

        foreach ($pallets as $pallet) {
            $pallet->update([
                'status' => PalletStatus::STORED->value,
                'checked_out_at' => null,
                'checked_out_by' => null,
            ]);

            if ($pallet->crate) {
                $pallet->crate->update(['status' => CrateStatus::STORED->value]);
            }

            if ($userId = Auth::id()) {
                $pallet->activities()->create([
                    'action' => PalletActivityAction::RETURN_TO_STORED->value,
                    'user_id' => $userId,
                    'description' => 'Hoàn pallet '.$pallet->pallet_id.' về lưu kho do xóa yêu cầu '.$this->request_code,
                    'action_time' => now(),
                ]);
            }
        }
    }


    /**
     * Get total shipped quantity.
     */
    public function getTotalShippedQuantityAttribute()
    {
        return $this->items()->sum('quantity_shipped');
    }

    /**
     * Get days until requested date.
     */
    public function getDaysUntilRequestedAttribute()
    {
        return now()->diffInDays($this->requested_date, false);
    }
}
