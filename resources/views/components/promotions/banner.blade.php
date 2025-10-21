@props(['promotions', 'page' => 'default'])

@if($promotions && $promotions->count() > 0)
    <!-- Modern Promotional Banners -->
    <section class="modern-promotional-banners mb-5">
        @if($promotions->where('type', 'banner')->count() > 1)
            <!-- Carousel for Multiple Banners -->
            <div id="promotionCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach($promotions->where('type', 'banner') as $index => $promotion)
                        <button type="button" data-bs-target="#promotionCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="true" aria-label="Promotion {{ $index + 1 }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @foreach($promotions->where('type', 'banner') as $index => $promotion)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            @include('components.promotions.banner-item', ['promotion' => $promotion])
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#promotionCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#promotionCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        @else
            <!-- Single Banner -->
            @foreach($promotions as $promotion)
                @if($promotion->type === 'banner')
                    @include('components.promotions.banner-item', ['promotion' => $promotion])
                @endif
            @endforeach
        @endif
    </section>

    <!-- Popup Promotions -->
    @foreach($promotions as $promotion)
        @if($promotion->type === 'popup')
            <div class="modal fade promotion-popup" 
                 id="promotion-{{ $promotion->promotion_id }}" 
                 tabindex="-1" 
                 aria-labelledby="promotionLabel-{{ $promotion->promotion_id }}"
                 aria-hidden="true"
                 data-promotion-id="{{ $promotion->promotion_id }}">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 overflow-hidden" 
                         style="background: linear-gradient(135deg, {{ $promotion->background_color }} 0%, {{ $promotion->background_color }}DD 100%);">
                        <div class="modal-header border-0 pb-0" style="color: {{ $promotion->text_color }};">
                            <h5 class="modal-title fw-bold" id="promotionLabel-{{ $promotion->promotion_id }}">
                                {{ $promotion->title }}
                            </h5>
                            <button type="button" 
                                    class="btn-close btn-close-white" 
                                    data-bs-dismiss="modal" 
                                    aria-label="Close"
                                    style="filter: invert(1);"></button>
                        </div>
                        <div class="modal-body text-center" style="color: {{ $promotion->text_color }};">
                            @if($promotion->banner_image)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $promotion->banner_image) }}" 
                                         alt="{{ $promotion->title }}" 
                                         class="img-fluid rounded-3" 
                                         style="max-height: 200px; object-fit: contain;">
                                </div>
                            @endif
                            
                            @if($promotion->description)
                                <p class="mb-3 opacity-90">{{ $promotion->description }}</p>
                            @endif
                            
                            @if($promotion->discount_text)
                                <div class="mb-3">
                                    <h3 class="fw-bold text-white">{{ $promotion->discount_text }}</h3>
                                    @if($promotion->discount_code)
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark fs-6 px-3 py-2">
                                                Use code: <strong>{{ $promotion->discount_code }}</strong>
                                            </span>
                                            <button class="btn btn-sm btn-outline-light ms-2" 
                                                    onclick="copyToClipboard('{{ $promotion->discount_code }}')"
                                                    title="Copy code">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @if($promotion->button_text && $promotion->button_link)
                        <div class="modal-footer border-0 pt-0 justify-content-center">
                            <a href="{{ $promotion->button_link }}" 
                               class="btn btn-lg fw-bold px-4" 
                               style="background-color: {{ $promotion->button_color }}; color: white; border-radius: 25px;"
                               target="{{ str_starts_with($promotion->button_link, 'http') ? '_blank' : '_self' }}">
                                {{ $promotion->button_text }}
                                @if(str_starts_with($promotion->button_link, 'http'))
                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                @endif
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize promotion carousel with advanced settings
    const carousel = document.querySelector('#promotionCarousel');
    if (carousel) {
        const bsCarousel = new bootstrap.Carousel(carousel, {
            interval: 5000, // Auto-advance every 5 seconds
            pause: 'hover',
            wrap: true,
            touch: true
        });
        
        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                bsCarousel.prev();
            } else if (e.key === 'ArrowRight') {
                bsCarousel.next();
            }
        });
    }
    
    // Handle popup promotions with enhanced animations
    const popupPromotions = document.querySelectorAll('.promotion-popup');
    
    popupPromotions.forEach(function(popup) {
        const promotionId = popup.dataset.promotionId;
        const storageKey = 'promotion_shown_' + promotionId;
        
        // Check if this promotion was already shown today
        const lastShown = localStorage.getItem(storageKey);
        const today = new Date().toDateString();
        
        if (lastShown !== today) {
            // Show popup after a short delay with entrance animation
            setTimeout(function() {
                const modal = new bootstrap.Modal(popup);
                popup.classList.add('animate__animated', 'animate__fadeInDown');
                modal.show();
                
                // Mark as shown today
                localStorage.setItem(storageKey, today);
            }, 2000); // 2 second delay
        }
    });
    
    // Add intersection observer for banner animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const bannerObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe all promotion banners
    document.querySelectorAll('.modern-promotion-banner').forEach(banner => {
        bannerObserver.observe(banner);
    });
});

// Enhanced copy discount code to clipboard function
function copyPromotionCode(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success feedback with enhanced animation
        const btn = event.target.closest('button');
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i>';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-light');
        
        setTimeout(function() {
            btn.innerHTML = originalContent;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-light');
        }, 1500);
        
        // Show toast notification
        showToast('Discount code copied to clipboard!', 'success');
    }).catch(function() {
        showToast('Failed to copy code. Please copy manually.', 'error');
    });
}

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast element if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-primary';
    
    const toastHTML = `
        <div id="${toastId}" class="toast ${bgClass} text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
    
    // Remove toast element after hiding
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}
</script>
@endpush