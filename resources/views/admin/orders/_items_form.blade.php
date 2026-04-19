<div class="table-wrap">
    <table>
        <thead>
        <tr>
            <th>ID позиции</th>
            <th>Товар</th>
            <th>SKU</th>
            <th>Цена</th>
            <th>Количество</th>
            <th>Подытог</th>
            <th>Удаление</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $index => $item)
            <tr>
                <td>
                    {{ $item->id }}
                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                </td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->product?->sku ?? '—' }}</td>
                <td>{{ number_format((float) $item->product_price, 2, '.', ' ') }}</td>
                <td style="width: 180px;">
                    <input
                        type="number"
                        min="0"
                        name="items[{{ $index }}][quantity]"
                        class="form-control"
                        value="{{ old("items.$index.quantity", $item->quantity) }}"
                        required
                    >
                </td>
                <td>{{ number_format((float) $item->subtotal, 2, '.', ' ') }}</td>
                <td>Укажи 0</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@error('items')
<div class="text-danger" style="margin-top: 10px;">{{ $message }}</div>
@enderror

<div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary">Отмена</a>
</div>
