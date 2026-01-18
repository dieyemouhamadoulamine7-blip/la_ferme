/**
 * JavaScript principal pour La Ferme E-commerce
 * Gère les interactions utilisateur, le panier, les formulaires
 */

// ============================================
// GESTION DU PANIER (AJAX)
// ============================================

/**
 * Ajoute un produit au panier via AJAX
 */
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('panier.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            showNotification('Produit ajouté au panier !', 'success');
        } else {
            showNotification(data.message || 'Erreur lors de l\'ajout au panier', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    });
}

/**
 * Ajoute un produit au panier depuis un formulaire
 */
function addToCartAjax(productId) {
    const form = document.getElementById('add-to-cart-form');
    const quantityInput = form.querySelector('input[name="quantity"]');
    const quantity = parseInt(quantityInput.value) || 1;
    
    addToCart(productId, quantity);
}

/**
 * Met à jour la quantité dans le panier
 */
function updateCartQuantity(productId, quantity) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('panier.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recharger pour voir le nouveau total
        } else {
            showNotification(data.message || 'Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    });
}

/**
 * Supprime un produit du panier
 */
function removeFromCart(productId) {
    if (!confirm('Êtes-vous sûr de vouloir retirer ce produit du panier ?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('product_id', productId);

    fetch('panier.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification(data.message || 'Erreur lors de la suppression', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    });
}

/**
 * Met à jour le compteur du panier dans la navigation
 */
function updateCartCount(count) {
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        if (count > 0) {
            cartCountElement.textContent = count;
            cartCountElement.style.display = 'flex';
        } else {
            cartCountElement.style.display = 'none';
        }
    }
}

// ============================================
// NOTIFICATIONS
// ============================================

/**
 * Affiche une notification temporaire
 */
function showNotification(message, type = 'success') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Styles inline pour la notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 4px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    `;
    
    if (type === 'success') {
        notification.style.backgroundColor = '#28a745';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#dc3545';
    } else {
        notification.style.backgroundColor = '#ffc107';
        notification.style.color = '#333';
    }
    
    // Ajouter au DOM
    document.body.appendChild(notification);
    
    // Supprimer après 3 secondes
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Ajouter les animations CSS si elles n'existent pas
if (!document.getElementById('notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

// ============================================
// VALIDATION DES FORMULAIRES
// ============================================

/**
 * Valide un formulaire avant soumission
 */
function validateForm(formElement) {
    const inputs = formElement.querySelectorAll('input[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = '#dc3545';
            
            // Retirer le style d'erreur après correction
            input.addEventListener('input', function() {
                this.style.borderColor = '';
            }, { once: true });
        }
    });
    
    // Validation email
    const emailInputs = formElement.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (input.value && !emailRegex.test(input.value)) {
            isValid = false;
            input.style.borderColor = '#dc3545';
            showNotification('Veuillez entrer une adresse email valide', 'error');
        }
    });
    
    // Validation des mots de passe (si présent)
    const passwordInput = formElement.querySelector('input[name="password"]');
    const passwordConfirmInput = formElement.querySelector('input[name="password_confirm"]');
    if (passwordInput && passwordConfirmInput) {
        if (passwordInput.value !== passwordConfirmInput.value) {
            isValid = false;
            passwordConfirmInput.style.borderColor = '#dc3545';
            showNotification('Les mots de passe ne correspondent pas', 'error');
        }
    }
    
    return isValid;
}

// Ajouter la validation à tous les formulaires
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
});

// ============================================
// GESTION DE LA QUANTITÉ
// ============================================

/**
 * Incrémente la quantité
 */
function incrementQuantity(inputElement) {
    const currentValue = parseInt(inputElement.value) || 1;
    const maxValue = parseInt(inputElement.getAttribute('max')) || 999;
    if (currentValue < maxValue) {
        inputElement.value = currentValue + 1;
    }
}

/**
 * Décrémente la quantité
 */
function decrementQuantity(inputElement) {
    const currentValue = parseInt(inputElement.value) || 1;
    const minValue = parseInt(inputElement.getAttribute('min')) || 1;
    if (currentValue > minValue) {
        inputElement.value = currentValue - 1;
    }
}

// Ajouter les boutons +/- aux sélecteurs de quantité
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-selector input[type="number"]');
    quantityInputs.forEach(input => {
        const wrapper = document.createElement('div');
        wrapper.className = 'quantity-controls';
        wrapper.style.cssText = 'display: flex; align-items: center; gap: 0.5rem;';
        
        const minusBtn = document.createElement('button');
        minusBtn.type = 'button';
        minusBtn.textContent = '-';
        minusBtn.className = 'btn-quantity';
        minusBtn.style.cssText = 'width: 30px; height: 30px; border: 1px solid #dee2e6; background: #f8f9fa; cursor: pointer; border-radius: 4px;';
        minusBtn.onclick = () => decrementQuantity(input);
        
        const plusBtn = document.createElement('button');
        plusBtn.type = 'button';
        plusBtn.textContent = '+';
        plusBtn.className = 'btn-quantity';
        plusBtn.style.cssText = 'width: 30px; height: 30px; border: 1px solid #dee2e6; background: #f8f9fa; cursor: pointer; border-radius: 4px;';
        plusBtn.onclick = () => incrementQuantity(input);
        
        input.parentNode.insertBefore(minusBtn, input);
        input.parentNode.insertBefore(plusBtn, input.nextSibling);
    });
});

// ============================================
// AMÉLIORATION UX
// ============================================

// Confirmation avant suppression
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-danger, [data-action="delete"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
                e.preventDefault();
            }
        });
    });
});

// Animation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.product-card, .category-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s, transform 0.5s';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Smooth scroll pour les ancres
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// ============================================
// GESTION DES IMAGES MANQUANTES
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            // Remplacer par une image placeholder si l'image ne charge pas
            this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="300" height="200"%3E%3Crect fill="%23f0f0f0" width="300" height="200"/%3E%3Ctext fill="%23999" font-family="sans-serif" font-size="14" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3EImage non disponible%3C/text%3E%3C/svg%3E';
            this.alt = 'Image non disponible';
        });
    });
});

