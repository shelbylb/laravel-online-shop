@extends('layouts.app')

@section('title', 'Оформление заказа')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Оформление заказа</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
                    @csrf

                    <!-- Адрес доставки -->
                    <div class="card mb-4"
                         x-data="{ address: '{{ old('address_id') ?? '' }}' }">

                        <div class="card-body">
                            <h5 class="card-title mb-3">Адрес доставки</h5>

                            @if($addresses->isNotEmpty())
                                <div class="mb-3">
                                    <label class="form-label">Выберите сохраненный адрес</label>

                                    <select class="form-select"
                                            name="address_id"
                                            x-model="address"
                                            x-on:change="
                                            let opt = $event.target.selectedOptions[0];

                                            if(address !== 'new' && address !== '') {
                                                $refs.country.value = opt.dataset.country || '';
                                                $refs.city.value = opt.dataset.city || '';
                                                $refs.street.value = opt.dataset.street || '';
                                                $refs.house.value = opt.dataset.house || '';
                                                $refs.apartment.value = opt.dataset.apartment || '';
                                            } else if(address === 'new') {
                                                $refs.country.value = '';
                                                $refs.city.value = '';
                                                $refs.street.value = '';
                                                $refs.house.value = '';
                                                $refs.apartment.value = '';
                                            }
                                        ">

                                        <option value="">-- Выберите сохраненный адрес --</option>

                                        @foreach($addresses as $addr)
                                            <option value="{{ $addr->id }}"
                                                    data-country="{{ $addr->country }}"
                                                    data-city="{{ $addr->city }}"
                                                    data-street="{{ $addr->street }}"
                                                    data-house="{{ $addr->house }}"
                                                    data-apartment="{{ $addr->apartment }}">
                                                {{ $addr->full_address }}
                                            </option>
                                        @endforeach

                                        <option value="new">+ Новый адрес</option>
                                    </select>
                                </div>

                                <hr>
                            @endif

                            <!-- Новый адрес -->
                            <div x-show="address === 'new' || {{ $addresses->isEmpty() ? 'true' : 'false' }}"
                                 x-transition
                                 style="display: none;">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Страна *</label>
                                        <input type="text"
                                               name="country"
                                               x-ref="country"
                                               class="form-control @error('country') is-invalid @enderror"
                                               value="{{ old('country', 'Россия') }}">
                                        @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Город *</label>
                                        <input type="text"
                                               name="city"
                                               x-ref="city"
                                               class="form-control @error('city') is-invalid @enderror"
                                               value="{{ old('city') }}">
                                        @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">Улица *</label>
                                        <input type="text"
                                               name="street"
                                               x-ref="street"
                                               class="form-control @error('street') is-invalid @enderror"
                                               value="{{ old('street') }}">
                                        @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Дом *</label>
                                        <input type="text"
                                               name="house"
                                               x-ref="house"
                                               class="form-control @error('house') is-invalid @enderror"
                                               value="{{ old('house') }}">
                                        @error('house')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Квартира</label>
                                        <input type="text"
                                               name="apartment"
                                               x-ref="apartment"
                                               class="form-control @error('apartment') is-invalid @enderror"
                                               value="{{ old('apartment') }}">
                                        @error('apartment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if($addresses->isEmpty())
                                    <div class="form-check mb-3">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               name="set_as_default"
                                               value="1"
                                            {{ old('set_as_default') ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            Сохранить как основной адрес
                                        </label>
                                    </div>
                                @endif
                            </div>

                            <!-- Получатель -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Получатель *</label>
                                    <input type="text"
                                           name="recipient_name"
                                           class="form-control @error('recipient_name') is-invalid @enderror"
                                           value="{{ old('recipient_name', auth()->user()->name) }}">
                                    @error('recipient_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Телефон *</label>
                                    <input type="tel"
                                           name="recipient_phone"
                                           class="form-control @error('recipient_phone') is-invalid @enderror"
                                           value="{{ old('recipient_phone', auth()->user()->phone ?? '') }}">
                                    @error('recipient_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Комментарий -->
                            <div class="mb-3">
                                <label class="form-label">Комментарий</label>
                                <textarea name="comment"
                                          class="form-control @error('comment') is-invalid @enderror"
                                          rows="3">{{ old('comment') }}</textarea>
                                @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Способ оплаты -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Способ оплаты</h5>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" value="card"
                                    {{ old('payment_method', 'card') == 'card' ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    <strong>Банковская карта</strong>
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="cash"
                                    {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    <strong>Наличные</strong>
                                </label>
                            </div>

                            @error('payment_method')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </form>
            </div>

            <!-- Итог -->
            <div class="col-lg-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Ваш заказ</h5>

                        @foreach($cart['items'] as $item)
                            @php($product = $item['product'])
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    {{ $product->name }} × {{ $item['quantity'] }}
                                </div>
                                <span>{{ number_format($item['subtotal'], 0, ',', ' ') }} ₽</span>
                            </div>
                        @endforeach

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Итого:</span>
                            <strong>{{ number_format($totalPrice, 0, ',', ' ') }} ₽</strong>
                        </div>

                        <button type="submit" form="checkout-form" class="btn btn-primary w-100">
                            Подтвердить заказ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
