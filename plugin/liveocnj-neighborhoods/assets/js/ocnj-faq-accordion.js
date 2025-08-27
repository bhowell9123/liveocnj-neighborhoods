/**
 * OCNJ FAQ Accordion - FIXED VERSION
 * This prevents multiple initialization and duplicate icons
 */

// GLOBAL guard - prevent multiple execution of entire script
if (window.ocnjFAQInitialized) {
  console.log('OCNJ FAQ already initialized globally, skipping entire script...');
} else {
  window.ocnjFAQInitialized = true;
  
  (function() {
    console.log('OCNJ FAQ Accordion - FIXED version loaded');
    
    function initFAQ() {
      console.log('Initializing FAQ accordion...');
      
      const faqItems = document.querySelectorAll('.ocnj-faq-item');
      console.log('Found FAQ items:', faqItems.length);
      
      if (faqItems.length === 0) {
        console.log('No FAQ items found');
        return;
      }
      
      faqItems.forEach((item, index) => {
        const question = item.querySelector('h3');
        const answer = item.querySelector('div');
        
        if (!question || !answer) {
          console.warn(`FAQ item ${index} missing question or answer`);
          return;
        }
        
        // CHECK if already processed - CRITICAL FIX
        if (question.dataset.faqProcessed === 'true') {
          console.log(`FAQ ${index} already processed, skipping...`);
          return;
        }
        question.dataset.faqProcessed = 'true';
        
        console.log(`Processing FAQ ${index}:`, { 
          question: question.textContent.substring(0, 30) + '...',
          answer: 'found'
        });
        
        // Remove any existing icons first - CRITICAL FIX
        const existingIcons = question.querySelectorAll('span');
        existingIcons.forEach(span => {
          if (span.textContent === '+' || span.textContent === '−') {
            span.remove();
          }
        });
        
        // Style the question
        question.style.cursor = 'pointer';
        question.style.userSelect = 'none';
        question.style.position = 'relative';
        question.style.paddingRight = '40px';
        question.style.margin = '0';
        question.style.padding = '15px 40px 15px 0';
        question.style.borderBottom = '1px solid #e0e0e0';
        
        // Add ONE icon only
        const icon = document.createElement('span');
        icon.className = 'faq-icon';
        icon.textContent = '+';
        icon.style.position = 'absolute';
        icon.style.right = '15px';
        icon.style.top = '50%';
        icon.style.transform = 'translateY(-50%)';
        icon.style.fontSize = '24px';
        icon.style.fontWeight = 'bold';
        icon.style.color = '#03989e';
        question.appendChild(icon);
        
        // Add click handler
        question.addEventListener('click', function(e) {
          console.log(`Clicked FAQ ${index}`);
          e.preventDefault();
          e.stopPropagation();
          
          // Toggle answer visibility
          const isVisible = answer.style.display === 'block';
          answer.style.display = isVisible ? 'none' : 'block';
          
          // Update icon
          icon.textContent = isVisible ? '+' : '−';
          
          // Toggle open class
          item.classList.toggle('open', !isVisible);
          
          console.log(`FAQ ${index} is now ${isVisible ? 'closed' : 'open'}`);
        });
        
        // Initially hide all answers
        answer.style.display = 'none';
        answer.style.padding = '0 0 15px 0';
        answer.style.margin = '0';
      });
    }
    
    // Run initialization
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initFAQ);
    } else {
      initFAQ();
    }
  })();
}