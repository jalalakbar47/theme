/* Native Astra navigation and WooCommerce interactions remain authoritative. */
(() => {
 'use strict';
 const button = document.createElement('button');
 button.className = 'zalmi-top';
 button.textContent = '↑';
 button.setAttribute('aria-label', 'Back to top');
 button.hidden = true;
 document.body.append(button);
 window.addEventListener('scroll', () => { button.hidden = window.scrollY < 600; }, {passive:true});
 button.addEventListener('click', () => window.scrollTo({top:0, behavior:window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth'}));
})();
