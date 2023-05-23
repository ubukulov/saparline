<?php

namespace App\Http\Resources;

use App\Models\Car;
use App\Models\CommentDislike;
use App\Models\CommentLike;
use App\Models\Favorite;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'name' => $this->name,
            'surname' => $this->surname,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'sound' => $this->sound,
            'lang' => $this->lang,
            'push' => $this->push,
            'passport_image' => $this->passport_image,
            'identity_image' => $this->identity_image,
            'confirmation' => $this->confirmation,
            'company_id' => $this->company_id,
            'device_token' => $this->device_token,
            'car' => Car::join('car_types','car_types.id','car_type_id')->where('user_id',$this->id)
                ->select('cars.*','car_types.name as type','car_types.count_places')
                ->first()
        ];

    }
}
