/**
 * OCNJ Neighborhoods JavaScript
 * 
 * Implements:
 * 1. Key handlers on card elements (Enter/Space to open modal)
 * 2. Modal focus trap
 * 3. Return focus when modal closes
 * 4. "18th-34th" helper
 * 5. CTA links inside modal and detail pages
 * 6. Accessibility features (ESC key, click outside, screen-reader isolation)
 */

(() => {
  /**
   * Simple HTML sanitizer to prevent XSS attacks
   * @param {string} html - The HTML string to sanitize
   * @returns {string} - The sanitized HTML
   */
  function sanitizeHTML(html) {
    if (!html) return '';
    
    // Create a new div element
    const tempDiv = document.createElement('div');
    
    // Set the HTML content with DOMPurify if available, otherwise use textContent as fallback
    if (window.DOMPurify) {
      tempDiv.innerHTML = DOMPurify.sanitize(html, {
        ALLOWED_TAGS: ['p', 'br', 'b', 'i', 'em', 'strong', 'a', 'ul', 'ol', 'li', 'h3', 'h4', 'h5', 'h6'],
        ALLOWED_ATTR: ['href', 'target', 'rel']
      });
    } else {
      // Fallback to a more restrictive approach if DOMPurify isn't available
      // Convert to plain text and then replace line breaks with <br> tags
      tempDiv.textContent = html;
    }
    
    return tempDiv.innerHTML;
  }

  document.addEventListener('DOMContentLoaded', () => {
    /* Check if we're on the right page */
    const root = document.getElementById('ocnj-root');
    if (!root) return;
    
    /* Format price values to prevent line breaks */
    document.querySelectorAll('.ocnj-stat-value').forEach(el => {
      const txt = (el.textContent || '').replace(/\s+/g,' ').trim();
      if (!txt.startsWith('$')) return;               // only format money rows
      const raw = txt.replace(/[$,]/g, '');
      const n = Number(raw);
      if (Number.isFinite(n)) el.textContent = '$' + n.toLocaleString('en-US');
    });
    
    /* Smooth scroll for in-page anchors */
    document.querySelectorAll('#ocnj-root a.ocnj-anchor[href^="#"]').forEach(a => {
      a.addEventListener('click', e => {
        const t = document.querySelector(a.getAttribute('href'));
        if (!t) return;
        e.preventDefault();
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        t.scrollIntoView({behavior: reduce ? 'auto' : 'smooth', block: 'start'});
        if (!t.hasAttribute('tabindex')) t.setAttribute('tabindex','-1');
        t.focus({ preventScroll: true });
        if (history.pushState) history.pushState(null,'',a.getAttribute('href')); else location.hash = a.getAttribute('href');
        setTimeout(()=>{ if (t.getAttribute('tabindex')==='-1') t.removeAttribute('tabindex'); },0);
      });
    });

    /* Modal wiring (no duplicate data; reads from the clicked card) */
    const modal = document.getElementById('neighborhoodModal');
    if (!modal) return;

    // --- Backdrop (create once, click-to-close) ---
    let backdrop = document.getElementById('ocnj-modal-backdrop');
    if (!backdrop) {
      backdrop = document.createElement('div');
      backdrop.id = 'ocnj-modal-backdrop';
      backdrop.className = 'ocnj-modal-backdrop';
      backdrop.setAttribute('aria-hidden', 'true');
      document.body.appendChild(backdrop);
      backdrop.addEventListener('click', closeModal);
    }

    const header = modal.querySelector('#ocnj-modal-header');
    const title = modal.querySelector('#ocnj-modal-title');
    const sub = modal.querySelector('#ocnj-modal-subtitle');
    const desc = modal.querySelector('#ocnj-modal-desc');
    const guideLink = modal.querySelector('#ocnj-modal-guide');
    const ctaGroup = modal.querySelector('.ocnj-cta-group');
    const faqsContainer = modal.querySelector('.ocnj-modal-faq-container');
    const faqsList = modal.querySelector('#ocnj-modal-faqs');
    const cta = {
      condoSale: modal.querySelector('#ocnj-modal-condo-sale'),
      sfSale: modal.querySelector('#ocnj-modal-sf-sale'),
      condoSold: modal.querySelector('#ocnj-modal-condo-sold'),
      sfSold: modal.querySelector('#ocnj-modal-sf-sold'),
    };
    
    // Hide IDX buttons by default
    Object.values(cta).forEach(btn => {
      if (btn) btn.style.display = 'none';
    });
    const closeBtn = modal.querySelector('.ocnj-modal-close');
    const modalAnnouncement = document.getElementById('modal-announcement');
    let lastFocus = null;
    
    // Limit inert to #ocnj-root children (store for restore)
    let hiddenEls = [];
    
    function hidePageForModal() {
      hiddenEls = Array.from(root.children).filter(el => el !== modal);
      hiddenEls.forEach(el => {
        el.setAttribute('aria-hidden', 'true');
        if ('inert' in el) {
          el.inert = true;
        } else {
          // Fallback for browsers without inert support
          const focusableEls = el.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
          focusableEls.forEach(focusEl => {
            // Store original tabindex to restore later
            if (focusEl.hasAttribute('tabindex')) {
              focusEl.dataset.originalTabindex = focusEl.getAttribute('tabindex');
            } else {
              focusEl.dataset.originalTabindex = '0';
            }
            focusEl.setAttribute('tabindex', '-1');
          });
        }
      });
    }
    
    function unhidePageAfterModal() {
      hiddenEls.forEach(el => {
        el.removeAttribute('aria-hidden');
        if ('inert' in el) {
          el.inert = false;
        } else {
          // Restore original tabindex values
          const focusableEls = el.querySelectorAll('[data-original-tabindex]');
          focusableEls.forEach(focusEl => {
            const originalValue = focusEl.dataset.originalTabindex;
            if (originalValue === '0') {
              focusEl.removeAttribute('tabindex');
            } else {
              focusEl.setAttribute('tabindex', originalValue);
            }
            delete focusEl.dataset.originalTabindex;
          });
        }
      });
      hiddenEls = [];
    }

    /**
     * Helper function to normalize the "18th-34th" slug to "18th-34th-street"
     * @param {string} s - The slug to normalize
     * @returns {string} - The normalized slug
     */
    function normalizeSlug(s) {
      if (s === '18th-34th') return '18th-34th-street';
      return s;
    }

    /**
     * Convert a neighborhood slug to its detail URL
     * @param {string} slug - The neighborhood slug
     * @returns {string} - The full URL for the neighborhood detail page
     */
    function slugToUrl(slug) {
      slug = normalizeSlug(slug);
      return `/ocean-city-neighborhoods/${slug}/`;
    }

    /**
     * Open the modal with content from the selected card
     * @param {HTMLElement} card - The card element that was clicked
     */
    function openModalFromCard(card) {
      const img = card.querySelector('.ocnj-card-image');
      const name = card.querySelector('.ocnj-card-title')?.textContent?.trim() || 'Neighborhood';
      const text = card.querySelector('.ocnj-card-description')?.textContent?.trim() || '';
      const price = card.querySelector('.ocnj-price-badge')?.textContent?.trim() || '';
      const slug = normalizeSlug(card.dataset.neighborhood || '');

      // Set aria-expanded for better accessibility
      card.setAttribute('aria-expanded', 'true');
      
      header.style.backgroundImage = `linear-gradient(rgba(0,0,0,.25), rgba(0,0,0,.25)), url('${img?.getAttribute('src') || ''}')`;
      title.textContent = name;
      
      // Format price for modal subtitle
      if (price) {
        const txt = price.replace(/\s+/g,' ').trim();
        const raw = txt.replace(/[$,]/g, '');
        const n = Number(raw);
        if (Number.isFinite(n)) {
          sub.textContent = `Typical asking prices: $${n.toLocaleString('en-US')}`;
        } else {
          sub.textContent = `Typical asking prices: ${price}`;
        }
      } else {
        sub.textContent = '';
      }
      
      // Check if OCNJ_DATA is available
      const hasData = window.OCNJ_DATA && OCNJ_DATA.sections && OCNJ_DATA.sections[slug];
      
      // Prefer Markdown excerpt/html when available
      if (hasData) {
        const content = OCNJ_DATA.sections[slug].excerpt || OCNJ_DATA.sections[slug].html;
        desc.innerHTML = sanitizeHTML(content);
        
        // Add facts to modal if available
        const facts = OCNJ_DATA.sections[slug].facts;
        const pointsList = modal.querySelector('#ocnj-modal-points');
        if (facts && facts.length > 0 && pointsList) {
          pointsList.innerHTML = '';
          facts.forEach(fact => {
            const li = document.createElement('li');
            li.textContent = fact;
            pointsList.appendChild(li);
          });
          pointsList.closest('.ocnj-modal-points-container').style.display = 'block';
        } else if (pointsList) {
          pointsList.closest('.ocnj-modal-points-container').style.display = 'none';
        }
        
        // Add FAQs to modal if available
        const faqs = OCNJ_DATA.sections[slug].faq;
        if (faqs && faqs.length > 0 && faqsList) {
          faqsList.innerHTML = '';
          // Display up to 7 FAQs
          const faqsToShow = faqs.slice(0, 7);
          faqsToShow.forEach(faq => {
            if (faq.q && faq.a) {
              const faqItem = document.createElement('div');
              faqItem.className = 'ocnj-modal-faq-item';
              
              const question = document.createElement('h4');
              question.textContent = faq.q;
              
              const answer = document.createElement('p');
              answer.textContent = faq.a;
              
              faqItem.appendChild(question);
              faqItem.appendChild(answer);
              faqsList.appendChild(faqItem);
            }
          });
          faqsContainer.style.display = 'block';
        } else if (faqsContainer) {
          faqsContainer.style.display = 'none';
        }
      } else {
        desc.textContent = text;
        if (faqsContainer) {
          faqsContainer.style.display = 'none';
        }
      }
      
      // Get IDX data from global OCNJ_IDX if available
      const idx = window.OCNJ_IDX || {};
      const map = idx[slug] || {};
      
      // Main CTA - View Listings
      if (guideLink && map.default) {
        guideLink.setAttribute('href', map.default);
        guideLink.setAttribute('target', '_blank');
        guideLink.setAttribute('rel', 'noopener noreferrer');
        guideLink.style.display = 'inline-block';
      } else if (guideLink) {
        guideLink.style.display = 'none';
      }
      
      // Secondary CTAs
      if (cta.condoSale && map.for_sale?.condos) {
        cta.condoSale.href = map.for_sale.condos;
        cta.condoSale.style.display = 'inline-block';
        cta.condoSale.setAttribute('target', '_blank');
        cta.condoSale.setAttribute('rel', 'noopener noreferrer');
      } else if (cta.condoSale) {
        cta.condoSale.style.display = 'none';
      }
      
      if (cta.sfSale && map.for_sale?.sf) {
        cta.sfSale.href = map.for_sale.sf;
        cta.sfSale.style.display = 'inline-block';
        cta.sfSale.setAttribute('target', '_blank');
        cta.sfSale.setAttribute('rel', 'noopener noreferrer');
      } else if (cta.sfSale) {
        cta.sfSale.style.display = 'none';
      }
      
      if (cta.condoSold && map.sold?.condos) {
        cta.condoSold.href = map.sold.condos;
        cta.condoSold.style.display = 'inline-block';
        cta.condoSold.setAttribute('target', '_blank');
        cta.condoSold.setAttribute('rel', 'noopener noreferrer');
      } else if (cta.condoSold) {
        cta.condoSold.style.display = 'none';
      }
      
      if (cta.sfSold && map.sold?.sf) {
        cta.sfSold.href = map.sold.sf;
        cta.sfSold.style.display = 'inline-block';
        cta.sfSold.setAttribute('target', '_blank');
        cta.sfSold.setAttribute('rel', 'noopener noreferrer');
      } else if (cta.sfSold) {
        cta.sfSold.style.display = 'none';
      }

      // Store the last focused element to return focus when modal closes
      lastFocus = card;
      
      // Show the modal
      modal.classList.add('active');
      backdrop.classList.add('active');
      modal.setAttribute('aria-hidden', 'false');
      closeBtn.focus();
      document.body.style.overflow = 'hidden';
      
      // Announce modal opening to screen readers
      if (modalAnnouncement) {
        modalAnnouncement.textContent = `${name} neighborhood details opened. Press Escape to close.`;
      }
      
      // Make the rest of the page inert for screen readers
      hidePageForModal();
    }

    /**
     * Close the modal and restore the page state
     */
    function closeModal() {
      modal.classList.remove('active');
      if (backdrop) backdrop.classList.remove('active');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      
      // Clear the announcement
      if (modalAnnouncement) {
        modalAnnouncement.textContent = '';
      }
      
      // Remove inert state from the rest of the page
      unhidePageAfterModal();
      
      // Reset aria-expanded on the last focused card and return focus
      if (lastFocus) {
        lastFocus.setAttribute('aria-expanded', 'false');
        lastFocus.focus();
      }
    }

    // Add event listeners to all neighborhood cards
    root.querySelectorAll('.ocnj-neighborhood-card').forEach(card => {
      // Add aria-haspopup attribute for better accessibility
      card.setAttribute('aria-haspopup', 'dialog');
      card.setAttribute('aria-expanded', 'false');
      card.setAttribute('tabindex', '0'); // Make cards focusable
      
      // Add fact chips to cards
      const slug = normalizeSlug(card.dataset.neighborhood || '');
      const facts = window.OCNJ_DATA?.sections?.[slug]?.facts || [];
      if (facts.length) {
        // guard: don't add twice
        if (!card.querySelector('.ocnj-card-chips')) {
          const chips = document.createElement('div');
          chips.className = 'ocnj-card-chips';
          chips.innerHTML = facts.slice(0,3)
            .map(f => `<span class="ocnj-chip">${f}</span>`)
            .join('');
          card.querySelector('.ocnj-card-content').appendChild(chips);
        }
      }
      
      // Click handler to open the modal
      card.addEventListener('click', () => openModalFromCard(card));
      
      // Keyboard handler for Enter/Space to open the modal
      card.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          openModalFromCard(card);
        }
      });
    });
    
    // Optional: avoid double tab-stops when JS is on
    root.querySelectorAll('.ocnj-neighborhood-card .ocnj-card-link').forEach(a => {
      a.tabIndex = -1;
      a.setAttribute('aria-hidden', 'true');
    });

    // Modal close button
    closeBtn.addEventListener('click', closeModal);
    
    // Click outside the modal to close
    modal.addEventListener('click', e => {
      if (e.target === modal) closeModal();
    });
    
    // ESC key to close the modal
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
    });
    
    // Trap focus inside the modal when it's open
    function trapFocus(e) {
      if (!modal.classList.contains('active')) return;
      if (e.key !== 'Tab') return;
      
      // Get all focusable elements within the modal
      const focusables = modal.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
      const first = focusables[0];
      const last = focusables[focusables.length - 1];
      
      // If focus is outside the modal, force it back to the first focusable element
      if (!modal.contains(document.activeElement)) {
        e.preventDefault();
        first.focus();
        return;
      }
      
      // Handle circular tab navigation within the modal
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
    
    document.addEventListener('keydown', trapFocus);
    
    // Additional check to ensure focus stays in modal
    // This handles cases where focus might move outside the modal through other means
    document.addEventListener('focusin', function(e) {
      if (modal.classList.contains('active') && !modal.contains(e.target)) {
        // If focus somehow moves outside the modal while it's active, pull it back
        const focusables = modal.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (focusables.length > 0) {
          e.preventDefault();
          focusables[0].focus();
        }
      }
    });
    
    // Special handling for "18th-34th" links outside of cards
    document.querySelectorAll('a[href*="18th-34th"]').forEach(link => {
      link.addEventListener('click', e => {
        const href = link.getAttribute('href');
        if (href === '18th-34th' ||
            href === '/ocean-city-neighborhoods/18th-34th/' ||
            href.endsWith('/18th-34th/')) {
          e.preventDefault();
          window.location.href = '/ocean-city-neighborhoods/18th-34th-street/';
        }
      });
    });
  });
})();