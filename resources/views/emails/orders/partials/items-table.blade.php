<table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; border-color: #d1d5db;">
    <thead>
    <tr style="background: #f3f4f6;">
        <th align="left">Позиция</th>
        <th align="right">Количество</th>
        <th align="right">Стоимость за единицу</th>
        <th align="right">Сумма</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($order->items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td align="right">{{ $item->quantity }}</td>
            <td align="right">{{ number_format((float) $item->product_price, 2, ',', ' ') }} ₽</td>
            <td align="right">{{ number_format((float) $item->subtotal, 2, ',', ' ') }} ₽</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th colspan="3" align="right">Итоговая сумма</th>
        <th align="right">{{ number_format((float) $order->total_amount, 2, ',', ' ') }} ₽</th>
    </tr>
    <tr>
        <th colspan="3" align="right">Способ оплаты</th>
        <th align="right">{{ $order->payment_method_label }}</th>
    </tr>
    </tfoot>
</table>
