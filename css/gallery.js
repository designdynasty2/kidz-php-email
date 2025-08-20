// ===== GALLERY LIGHTBOX FUNCTIONALITY =====
document.addEventListener("DOMContentLoaded", function () {
  initGalleryLightbox();
  initAccessibility();
});

function initGalleryLightbox() {
  const galleryItems = document.querySelectorAll(".gallery-item");
  const lightbox = document.getElementById("lightbox");
  const lightboxImg = document.getElementById("lightbox-img");
  const lightboxCaption = document.getElementById("lightbox-caption");
  const lightboxClose = document.querySelector(".lightbox-close");

  // Open lightbox
  galleryItems.forEach((item) => {
    item.addEventListener("click", function () {
      const img = this.querySelector(".gallery-img");
      const caption = this.querySelector(".gallery-content h4").textContent;
      const description = this.querySelector(".gallery-content p").textContent;

      lightboxImg.src = img.src;
      lightboxImg.alt = img.alt;
      lightboxCaption.innerHTML = `
        <h4>${caption}</h4>
        <p>${description}</p>
      `;

      lightbox.style.display = "block";
      document.body.style.overflow = "hidden";

      // Add fade-in animation
      setTimeout(() => {
        lightbox.style.opacity = "1";
      }, 10);
    });
  });

  // Close lightbox
  function closeLightbox() {
    lightbox.style.opacity = "0";
    setTimeout(() => {
      lightbox.style.display = "none";
      document.body.style.overflow = "auto";
    }, 300);
  }

  // Close lightbox when clicking the close button
  if (lightboxClose) {
    lightboxClose.addEventListener("click", closeLightbox);
  }

  // Close lightbox when clicking outside the image
  lightbox.addEventListener("click", function (e) {
    if (e.target === lightbox) {
      closeLightbox();
    }
  });

  // Close lightbox with Escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && lightbox.style.display === "block") {
      closeLightbox();
    }
  });
}

// ===== ACCESSIBILITY ENHANCEMENTS =====
function initAccessibility() {
  // Keyboard navigation for gallery
  document.addEventListener("keydown", function (e) {
    const galleryItems = document.querySelectorAll(".gallery-item");
    const focusedElement = document.activeElement;

    if (e.key === "Enter" || e.key === " ") {
      if (focusedElement.classList.contains("gallery-item")) {
        e.preventDefault();
        focusedElement.click();
      }
    }
  });

  // Make gallery items focusable
  document.querySelectorAll(".gallery-item").forEach((item) => {
    item.setAttribute("tabindex", "0");
    item.setAttribute("role", "button");
    item.setAttribute("aria-label", "Open image in lightbox");
  });
}

// ===== PERFORMANCE OPTIMIZATIONS =====
// Throttle function for better performance
function throttle(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// ===== ERROR HANDLING =====
window.addEventListener("error", function (e) {
  console.error("Gallery JavaScript error:", e.error);
});

// ===== FEATURE DETECTION =====
// Check for IntersectionObserver support
if (!("IntersectionObserver" in window)) {
  // Fallback for older browsers - show all gallery items
  const galleryItems = document.querySelectorAll(".gallery-item");
  galleryItems.forEach((item) => item.classList.add("visible"));
}
