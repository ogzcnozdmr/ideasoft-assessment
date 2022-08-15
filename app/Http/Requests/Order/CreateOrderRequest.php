<?php

namespace App\Http\Requests\Order;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateOrderRequest extends FormRequest
{
    /**
     * Product Model List
     * @var array<Product> $products
     */
    public array $productsModel = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'products' => 'required|array',
            'products.*' => function ($attr, $value, $fail) {
                // product.id required validation
                if (!isset($value['id'])) {
                    $fail($attr . '.id is required.');
                    return;
                }

                // product.id is integer validation
                if (!is_integer($value['id'])) {
                    $fail($attr . '.id is must be integer.');
                    return;
                }

                // product.quantity required validation
                if (!isset($value['quantity'])) {
                    $fail($attr . '.quantity is required.');
                    return;
                }

                // product.quantity is integer validation
                if (!is_integer($value['quantity'])) {
                    $fail($attr . '.quantity is must be integer.');
                    return;
                }

                try {
                    $product = Product::where('id', $value['id'])->firstOrFail();
                } catch (\Throwable $exception) {
                    $fail($attr . ' is not found');
                    return;
                }


                if ($product && $product->stock < $value['quantity']) {
                    $fail('The' . $attr . ' is stocks not enough ');
                } else {
                    $this->productsModel[$value['id']] = $product;
                }
            }
        ];
    }
}
