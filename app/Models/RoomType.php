<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'max_occupancy'])]
class RoomType extends Model
{
    /**
     * The hotel room type relations for this room type.
     *
     * @return HasMany<HotelRoomType, $this>
     */
    public function hotelRoomTypes(): HasMany
    {
        return $this->hasMany(HotelRoomType::class);
    }

    /**
     * The hotels that offer this room type.
     *
     * @return BelongsToMany<Hotel, $this>
     */
    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'hotel_room_types')
            ->withPivot('quantity', 'price');
    }
}
