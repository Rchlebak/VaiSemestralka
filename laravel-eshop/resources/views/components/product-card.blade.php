@props(['product'])

<div class="product-card-modern h-100">
    <!-- Obrázok -->
    <div class="product-image">
        {{-- Gender Badge --}}
        @if($product->gender == 'men')
            <span class="product-badge badge-men">Muži</span>
        @elseif($product->gender == 'women')
            <span class="product-badge badge-women">Ženy</span>
        @endif

        {{-- Sales Badge (voliteľné) --}}
        {{-- Stock Status Badge --}}
        @php
            $totalStock = $product->variants->sum(function ($variant) {
                return $variant->inventory->stock_qty ?? 0;
            });
        @endphp

        @if($totalStock == 0)
            <span class="product-badge bg-dark text-white ms-5">Vypredané</span>
        @elseif($totalStock <= 5)
            <span class="product-badge bg-warning text-dark ms-5">Čoskoro vypredané</span>
        @endif

        @php
            $rawImg = $product->main_image ?? null;
            $cardImage = $rawImg
                ? (Str::startsWith($rawImg, 'http') ? $rawImg : '/' . ltrim($rawImg, '/'))
                : 'https://via.placeholder.com/400x300?text=No+Image';
        @endphp
        <img src="{{ $cardImage }}" alt="{{ $product->name }}"
            onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'" loading="lazy">

        <div class="product-overlay">
            <a href="{{ route('product.show', $product->product_id) }}" class="btn-view">
                <i class="bi bi-eye"></i> Zobraziť
            </a>
        </div>
    </div>

    <div class="product-info">
        <span class="product-brand">{{ $product->brand ?? 'Tenisky' }}</span>
        <h3 class="product-name">
            <a href="{{ route('product.show', $product->product_id) }}" class="text-decoration-none text-dark">
                {{ $product->name }}
            </a>
        </h3>

        <!-- Veľkosti -->
        @if($product->available_sizes)
            <div class="product-sizes">
                @foreach(array_slice($product->available_sizes, 0, 5) as $size)
                    <span class="size-tag">{{ $size }}</span>
                @endforeach
                @if(count($product->available_sizes) > 5)
                    <span class="size-tag more">+{{ count($product->available_sizes) - 5 }}</span>
                @endif
            </div>
        @endif

        <div class="product-footer">
            <div class="product-price">
                <span class="price-current">{{ number_format($product->base_price, 2) }} €</span>
                @if(isset($product->old_price))
                    <span
                        class="text-muted text-decoration-line-through small ms-1">{{ number_format($product->old_price, 2) }}
                        €</span>
                @endif
            </div>

            {{-- Add to Cart Button (zatiaľ smeruje na detail, ale môže byť AJAX) --}}
            {{-- Add to Cart Button --}}
            <button class="btn-add-cart btn-add-to-cart {{ $totalStock == 0 ? 'disabled btn-secondary' : '' }}"
                onclick="window.location.href='{{ route('product.show', $product->product_id) }}'"
                data-product-id="{{ $product->product_id }}" data-product-name="{{ $product->name }}"
                data-product-price="{{ $product->base_price }}" {{ $totalStock == 0 ? 'disabled' : '' }}>
                <i class="bi bi-cart-plus"></i>
            </button>
        </div>
    </div>
</div>