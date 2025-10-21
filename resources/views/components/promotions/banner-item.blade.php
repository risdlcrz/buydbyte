@props(['promotion'])

<div class="modern-promotion-banner position-relative overflow-hidden" 
     style="background: linear-gradient(135deg, {{ $promotion->background_color }}15 0%, {{ $promotion->background_color }} 100%); 
            min-height: 140px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
    
    <!-- Animated Background Elements -->
    <div class="position-absolute top-0 end-0 opacity-25">
        <svg width="200" height="140" viewBox="0 0 200 140" fill="none">
            <circle cx="160" cy="30" r="40" fill="{{ $promotion->text_color }}" opacity="0.1"/>
            <circle cx="180" cy="80" r="25" fill="{{ $promotion->text_color }}" opacity="0.15"/>
            <circle cx="140" cy="110" r="30" fill="{{ $promotion->text_color }}" opacity="0.08"/>
        </svg>
    </div>
    
    <div class="container-fluid h-100">
        <div class="row align-items-center h-100 py-4 px-4">
            <!-- Icon/Image Section -->
            @if($promotion->banner_image)
                <div class="col-lg-2 col-md-3 col-4 text-center">
                    <div class="promotion-image-wrapper position-relative">
                        <img src="{{ asset('storage/' . $promotion->banner_image) }}" 
                             alt="{{ $promotion->title }}" 
                             class="img-fluid rounded-3 shadow-sm promotion-image" 
                             style="max-height: 80px; object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                </div>
            @else
                <div class="col-lg-1 col-md-2 col-3 text-center">
                    <div class="promotion-icon-wrapper">
                        <i class="bi bi-gift-fill fs-1 text-white opacity-90"></i>
                    </div>
                </div>
            @endif
            
            <!-- Content Section -->
            <div class="col-lg-{{ $promotion->banner_image ? '7' : '8' }} col-md-{{ $promotion->banner_image ? '6' : '7' }} col-{{ $promotion->banner_image ? '5' : '6' }}">
                <div class="promotion-content" style="color: {{ $promotion->text_color }};">
                    <h4 class="fw-bold mb-2 promotion-title" style="font-size: 1.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        {{ $promotion->title }}
                    </h4>
                    
                    @if($promotion->description)
                        <p class="mb-3 opacity-90 promotion-description" style="font-size: 0.95rem; line-height: 1.4;">
                            {{ $promotion->description }}
                        </p>
                    @endif
                    
                    <div class="promotion-badges d-flex flex-wrap gap-2 align-items-center">
                        @if($promotion->discount_text)
                            <span class="badge promotion-discount-badge px-3 py-2 fs-6 fw-bold d-inline-flex align-items-center" 
                                  style="background: linear-gradient(45deg, #ff6b6b, #feca57); color: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(255,107,107,0.3);">
                                <i class="bi bi-percent me-1"></i>
                                {{ $promotion->discount_text }}
                            </span>
                        @endif
                        
                        @if($promotion->discount_code)
                            <span class="badge promotion-code-badge px-3 py-2 fs-6 fw-bold position-relative cursor-pointer" 
                                  style="background: rgba(255,255,255,0.95); color: {{ $promotion->background_color }}; border-radius: 12px; border: 2px dashed {{ $promotion->background_color }}40;"
                                  onclick="copyPromotionCode('{{ $promotion->discount_code }}')"
                                  title="Click to copy code">
                                <i class="bi bi-tag-fill me-1"></i>
                                <strong>{{ $promotion->discount_code }}</strong>
                                <i class="bi bi-clipboard ms-1 small"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Action Button Section -->
            @if($promotion->button_text && $promotion->button_link)
                <div class="col-lg-{{ $promotion->banner_image ? '3' : '3' }} col-md-3 col-3 text-center">
                    <a href="{{ $promotion->button_link }}" 
                       class="btn btn-promotion fw-bold text-decoration-none d-inline-flex align-items-center justify-content-center px-4 py-3" 
                       style="background: linear-gradient(45deg, {{ $promotion->button_color }}, {{ $promotion->button_color }}CC); 
                              color: white; 
                              border-radius: 25px; 
                              border: none; 
                              box-shadow: 0 6px 20px rgba(0,0,0,0.15);
                              transition: all 0.3s ease;
                              transform: translateY(0);"
                       target="{{ str_starts_with($promotion->button_link, 'http') ? '_blank' : '_self' }}"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.2)'"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'">
                        <span class="me-2">{{ $promotion->button_text }}</span>
                        @if(str_starts_with($promotion->button_link, 'http'))
                            <i class="bi bi-box-arrow-up-right"></i>
                        @else
                            <i class="bi bi-arrow-right"></i>
                        @endif
                    </a>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Decorative Elements -->
    <div class="position-absolute bottom-0 start-0 w-100 h-2" 
         style="background: linear-gradient(90deg, {{ $promotion->button_color ?? $promotion->background_color }}, transparent);"></div>
</div>

<style>
.modern-promotion-banner {
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.modern-promotion-banner:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
}

.promotion-image:hover {
    transform: scale(1.05);
}

.promotion-title {
    animation: fadeInUp 0.8s ease-out;
}

.promotion-description {
    animation: fadeInUp 0.8s ease-out 0.2s both;
}

.promotion-badges {
    animation: fadeInUp 0.8s ease-out 0.4s both;
}

.promotion-discount-badge {
    animation: pulse 2s infinite;
}

.promotion-code-badge {
    transition: all 0.3s ease;
}

.promotion-code-badge:hover {
    transform: scale(1.05);
    background: rgba(255,255,255,1) !important;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.02);
    }
}

.cursor-pointer {
    cursor: pointer;
}
</style>