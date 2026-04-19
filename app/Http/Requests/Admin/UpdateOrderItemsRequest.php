<?php

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Order $order */
        $order = $this->route('order');

        return $this->user()?->can('updateItems', $order) ?? false;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:order_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var Order $order */
            $order = $this->route('order');

            $itemIds = collect($this->input('items', []))
                ->pluck('id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->values();

            $validIds = $order->items()
                ->whereIn('id', $itemIds)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            foreach ($itemIds as $id) {
                if (!in_array($id, $validIds, true)) {
                    $validator->errors()->add('items', "Позиция заказа #{$id} не принадлежит текущему заказу.");
                }
            }
        });
    }
}
