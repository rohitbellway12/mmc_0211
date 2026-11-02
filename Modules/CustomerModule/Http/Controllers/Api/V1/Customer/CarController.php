<?php

namespace Modules\CustomerModule\Http\Controllers\Api\V1\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Entities\UserCar;

class CarController extends Controller
{
    /**
     * Display a listing of customer cars.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || !in_array($user->user_type, CUSTOMER_USER_TYPES)) {
            return response()->json(response_formatter(DEFAULT_403), 401);
        }

        $query = UserCar::where('user_id', $user->id)->latest();

        if ($request->filled('limit') && $request->filled('offset')) {
            $cars = $query->paginate($request->get('limit', 20), ['*'], 'offset', $request->get('offset', 1))->withPath('');
        } else {
            $cars = $query->get();
        }

        return response()->json(response_formatter(DEFAULT_200, $cars), 200);
    }

    /**
     * Store a newly created customer car.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || !in_array($user->user_type, CUSTOMER_USER_TYPES)) {
            return response()->json(response_formatter(DEFAULT_403), 401);
        }

        $validator = Validator::make($request->all(), [
            'brand' => 'nullable|string|max:191',
            'car_brand' => 'nullable|string|max:191',
            'model' => 'nullable|string|max:191',
            'car_model' => 'nullable|string|max:191',
            'manufacture_year' => 'nullable|integer',
            'registration_number' => 'nullable|string|max:191',
            'car_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10000',
            'car_images' => 'nullable|array',
            'car_images.*' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ], [
            'car_image.image' => translate('Car image must be a valid image file.'),
            'car_image.mimes' => translate('Invalid car image format! Only JPEG, JPG, PNG, and GIF formats are allowed.'),
            'car_images.*.image' => translate('Each car image must be a valid image file.'),
            'car_images.*.mimes' => translate('Invalid car image format! Only JPEG, JPG, PNG, and GIF formats are allowed.'),
        ]);

        $brand = $request->brand ?? $request->car_brand;
        $model = $request->model ?? $request->car_model;

        if (empty($brand) && empty($model) && empty($request->registration_number)) {
            $validator->after(function ($validator) {
                $validator->errors()->add('brand', translate('At least car brand, model or registration number is required'));
            });
        }

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $car = new UserCar();
        $car->user_id = $user->id;
        $car->brand = $brand;
        $car->model = $model;
        $car->manufacture_year = $request->manufacture_year;
        $car->registration_number = $request->registration_number;

        $uploadedImages = [];
        if ($request->hasFile('car_images')) {
            foreach ($request->file('car_images') as $img) {
                if ($img) {
                    $uploadedImages[] = file_uploader('user/car/', 'png', $img);
                }
            }
        }

        if ($request->hasFile('car_image')) {
            $singleImg = file_uploader('user/car/', 'png', $request->file('car_image'));
            if (!in_array($singleImg, $uploadedImages)) {
                array_unshift($uploadedImages, $singleImg);
            }
        }

        if (!empty($uploadedImages)) {
            $car->car_image = $uploadedImages[0];
            $car->car_images = $uploadedImages;
        }

        $car->save();

        return response()->json(response_formatter(DEFAULT_STORE_200, $car), 200);
    }

    /**
     * Display the specified car.
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user || !in_array($user->user_type, CUSTOMER_USER_TYPES)) {
            return response()->json(response_formatter(DEFAULT_403), 401);
        }

        $car = UserCar::where('user_id', $user->id)->find($id);

        if (!$car) {
            return response()->json(response_formatter(DEFAULT_404), 404);
        }

        return response()->json(response_formatter(DEFAULT_200, $car), 200);
    }

    /**
     * Update the specified car.
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user || !in_array($user->user_type, CUSTOMER_USER_TYPES)) {
            return response()->json(response_formatter(DEFAULT_403), 401);
        }

        $car = UserCar::where('user_id', $user->id)->find($id);

        if (!$car) {
            return response()->json(response_formatter(DEFAULT_404), 404);
        }

        $validator = Validator::make($request->all(), [
            'brand' => 'nullable|string|max:191',
            'car_brand' => 'nullable|string|max:191',
            'model' => 'nullable|string|max:191',
            'car_model' => 'nullable|string|max:191',
            'manufacture_year' => 'nullable|integer',
            'registration_number' => 'nullable|string|max:191',
            'car_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10000',
            'car_images' => 'nullable|array',
            'car_images.*' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ], [
            'car_image.image' => translate('Car image must be a valid image file.'),
            'car_image.mimes' => translate('Invalid car image format! Only JPEG, JPG, PNG, and GIF formats are allowed.'),
            'car_images.*.image' => translate('Each car image must be a valid image file.'),
            'car_images.*.mimes' => translate('Invalid car image format! Only JPEG, JPG, PNG, and GIF formats are allowed.'),
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        if ($request->has('brand') || $request->has('car_brand')) {
            $car->brand = $request->brand ?? $request->car_brand;
        }
        if ($request->has('model') || $request->has('car_model')) {
            $car->model = $request->model ?? $request->car_model;
        }
        if ($request->has('manufacture_year')) {
            $car->manufacture_year = $request->manufacture_year;
        }
        if ($request->has('registration_number')) {
            $car->registration_number = $request->registration_number;
        }

        $uploadedImages = $car->car_images ?? [];
        if (is_string($uploadedImages)) {
            $uploadedImages = json_decode($uploadedImages, true) ?? [];
        }

        if ($request->hasFile('car_images')) {
            $newImages = [];
            foreach ($request->file('car_images') as $img) {
                if ($img) {
                    $newImages[] = file_uploader('user/car/', 'png', $img);
                }
            }
            if (!empty($newImages)) {
                $uploadedImages = $newImages;
                $car->car_images = $uploadedImages;
                $car->car_image = $uploadedImages[0];
            }
        }

        if ($request->hasFile('car_image')) {
            $singleImg = file_uploader('user/car/', 'png', $request->file('car_image'), $car->car_image);
            $car->car_image = $singleImg;
            if (empty($uploadedImages)) {
                $car->car_images = [$singleImg];
            } else {
                $uploadedImages[0] = $singleImg;
                $car->car_images = $uploadedImages;
            }
        }

        $car->save();

        return response()->json(response_formatter(DEFAULT_UPDATE_200, $car), 200);
    }

    /**
     * Remove the specified car.
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user || !in_array($user->user_type, CUSTOMER_USER_TYPES)) {
            return response()->json(response_formatter(DEFAULT_403), 401);
        }

        $car = UserCar::where('user_id', $user->id)->find($id);

        if (!$car) {
            return response()->json(response_formatter(DEFAULT_404), 404);
        }

        $car->delete();

        return response()->json(response_formatter(DEFAULT_DELETE_200), 200);
    }
}
