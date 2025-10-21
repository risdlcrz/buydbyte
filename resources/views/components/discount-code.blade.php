@props(['showLabel' => true, 'size' => 'md'])

@php
    $inputClass = match($size) {
        'sm' => 'form-control-sm',
        'lg' => 'form-control-lg',
        default => ''
    };
@endphp

<div class="discount-code-section">
    @if($showLabel)
        <label class="form-label fw-semibold">
            <i class="bi bi-percent me-1"></i>
            Discount Code
        </label>
    @endif
    
    <div class="input-group">
        <input type="text" 
               class="form-control {{ $inputClass }}" 
               id="discount-code-input"
               placeholder="Enter discount code" 
               maxlength="50"
               style="text-transform: uppercase;">
        <button class="btn btn-outline-primary" 
                type="button" 
                id="apply-discount-btn">
            <i class="bi bi-check-circle me-1"></i>
            Apply
        </button>
    </div>
    
    <!-- Applied discount display -->
    <div id="applied-discount" class="mt-2 d-none">
        <div class="alert alert-success py-2 mb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-check-circle-fill me-1"></i>
                    <strong id="discount-code-text"></strong> applied
                    <span class="badge bg-success ms-2" id="discount-value"></span>
                </div>
                <button type="button" 
                        class="btn btn-sm btn-outline-danger"
                        id="remove-discount-btn">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Error display -->
    <div id="discount-error" class="mt-2 d-none">
        <div class="alert alert-danger py-2 mb-0">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <span id="discount-error-text"></span>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountInput = document.getElementById('discount-code-input');
    const applyBtn = document.getElementById('apply-discount-btn');
    const removeBtn = document.getElementById('remove-discount-btn');
    const appliedSection = document.getElementById('applied-discount');
    const errorSection = document.getElementById('discount-error');
    
    // Check for existing discount on page load
    checkCurrentDiscount();
    
    // Apply discount
    applyBtn.addEventListener('click', function() {
        const code = discountInput.value.trim().toUpperCase();
        
        if (!code) {
            showError('Please enter a discount code');
            return;
        }
        
        applyDiscount(code);
    });
    
    // Remove discount
    removeBtn.addEventListener('click', function() {
        removeDiscount();
    });
    
    // Apply on Enter key
    discountInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyBtn.click();
        }
    });
    
    // Auto-uppercase input
    discountInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    function applyDiscount(code) {
        setLoading(true);
        hideMessages();
        
        fetch('{{ route("discount.apply") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ discount_code: code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAppliedDiscount(data.discount);
                discountInput.value = '';
                showToast('Discount code applied successfully!', 'success');
                
                // Trigger custom event for cart/checkout updates
                window.dispatchEvent(new CustomEvent('discountApplied', { 
                    detail: data.discount 
                }));
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Failed to apply discount code. Please try again.');
        })
        .finally(() => {
            setLoading(false);
        });
    }
    
    function removeDiscount() {
        setLoading(true);
        
        fetch('{{ route("discount.remove") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hideAppliedDiscount();
                showToast('Discount code removed', 'info');
                
                // Trigger custom event for cart/checkout updates
                window.dispatchEvent(new CustomEvent('discountRemoved'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            setLoading(false);
        });
    }
    
    function checkCurrentDiscount() {
        fetch('{{ route("discount.current") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.discount) {
                showAppliedDiscount({
                    code: data.discount.code,
                    text: data.discount.type === 'percentage' ? 
                          data.discount.value + '% OFF' : 
                          '$' + data.discount.value + ' OFF',
                    type: data.discount.type,
                    value: data.discount.value
                });
            }
        })
        .catch(error => {
            console.error('Error checking discount:', error);
        });
    }
    
    function showAppliedDiscount(discount) {
        document.getElementById('discount-code-text').textContent = discount.code;
        document.getElementById('discount-value').textContent = discount.text;
        appliedSection.classList.remove('d-none');
        discountInput.disabled = true;
        applyBtn.disabled = true;
    }
    
    function hideAppliedDiscount() {
        appliedSection.classList.add('d-none');
        discountInput.disabled = false;
        applyBtn.disabled = false;
    }
    
    function showError(message) {
        document.getElementById('discount-error-text').textContent = message;
        errorSection.classList.remove('d-none');
        setTimeout(() => {
            errorSection.classList.add('d-none');
        }, 5000);
    }
    
    function hideMessages() {
        errorSection.classList.add('d-none');
    }
    
    function setLoading(loading) {
        applyBtn.disabled = loading;
        if (loading) {
            applyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Applying...';
        } else {
            applyBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Apply';
        }
    }
});
</script>
@endpush