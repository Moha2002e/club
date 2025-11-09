// contact.js
// Handles anchor offset scrolling and contact form submission via EmailJS.
// Note: Public keys and service/template IDs are client-side by design for EmailJS.
// For stronger security, implement a server-side email endpoint instead.

(function(){
    'use strict';

    /* ===== Offset anchor scrolling (accounts for fixed nav) ===== */
    function getNavHeight(){
        const nav = document.querySelector('nav');
        if(!nav) return 0;
        const style = window.getComputedStyle(nav);
        const h = nav.getBoundingClientRect().height || parseInt(style.height) || 0;
        return Math.ceil(h);
    }

    function offsetScrollTo(hash){
        if(!hash) return;
        const id = hash.replace('#','');
        const el = document.getElementById(id);
        if(!el) return;
        const navH = getNavHeight();
        const rect = el.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const target = rect.top + scrollTop - navH - 12; // small padding
        window.scrollTo({ top: target, behavior: 'smooth' });
    }

    // Handle clicks on anchor links with smooth scroll and offset
    document.addEventListener('click', function(e){
        const a = e.target.closest('a[href^="#"]');
        if(!a) return;
        const href = a.getAttribute('href');
        if(!href || href === '#') return;
        e.preventDefault();
        offsetScrollTo(href);
    }, { passive: false });

    // Handle initial hash on page load
    window.addEventListener('load', function(){
        if(location.hash){
            setTimeout(function(){ offsetScrollTo(location.hash); }, 50);
        }
    });

    /* ===== Contact form submission via EmailJS ===== */
    const form = document.getElementById('contact-form');
    if(!form) return;

    // Ensure EmailJS SDK is loaded and initialized
    function ensureEmailJS(readyCb){
        // If already available, initialize and return
        if(window.emailjs && typeof emailjs.init === 'function'){
            try{ 
                emailjs.init('6WNepAHVTUsPccRF3'); 
            } catch(e) {
                console.warn('EmailJS init error:', e);
            }
            return readyCb(true);
        }

        // Check if script tag already exists
        const existing = document.querySelector('script[src="https://cdn.emailjs.com/sdk/3.2.0/email.min.js"]');
        if(existing){
            // Script exists, wait for it to load
            let attempts = 0;
            const maxAttempts = 40; // 8 seconds total
            const check = setInterval(function(){
                attempts++;
                if(window.emailjs && typeof emailjs.init === 'function'){
                    clearInterval(check);
                    try{ 
                        emailjs.init('6WNepAHVTUsPccRF3'); 
                    } catch(e) {
                        console.warn('EmailJS init error:', e);
                    }
                    readyCb(true);
                } else if(attempts >= maxAttempts) {
                    clearInterval(check);
                    readyCb(false);
                }
            }, 200);
            return;
        }

        // Create and load the script
        const s = document.createElement('script');
        s.src = 'https://cdn.emailjs.com/sdk/3.2.0/email.min.js';
        s.async = true;
        s.onload = function(){
            try{ 
                if(window.emailjs && typeof emailjs.init === 'function') {
                    emailjs.init('6WNepAHVTUsPccRF3'); 
                }
            } catch(e) {
                console.warn('EmailJS init error:', e);
            }
            readyCb(true);
        };
        s.onerror = function(){ 
            readyCb(false); 
        };
        document.head.appendChild(s);
    }

    // Show toast notification
    function showToast(message, type, mailtoLink){
        let toast = document.getElementById('contact-toast');
        if(!toast){
            toast = document.createElement('div');
            toast.id = 'contact-toast';
            Object.assign(toast.style, {
                position: 'fixed', 
                right: '20px', 
                bottom: '20px', 
                padding: '12px 16px',
                borderRadius: '8px', 
                zIndex: '9999', 
                color: '#fff', 
                fontSize: '14px',
                boxShadow: '0 6px 18px rgba(0,0,0,0.2)', 
                opacity: '0', 
                transition: 'opacity 220ms ease, transform 220ms ease',
                transform: 'translateY(10px)'
            });
            document.body.appendChild(toast);
        }

        toast.style.background = (type === 'success') ? '#16a34a' : '#ef4444';
        toast.innerHTML = '';
        
        const text = document.createElement('div'); 
        text.textContent = message; 
        toast.appendChild(text);
        
        if(mailtoLink){
            const btn = document.createElement('button'); 
            btn.textContent = 'Ouvrir client mail';
            Object.assign(btn.style, { 
                marginLeft: '12px', 
                background: 'rgba(255,255,255,0.12)', 
                color: '#fff', 
                border: 'none', 
                padding: '6px 10px', 
                borderRadius: '6px', 
                cursor: 'pointer' 
            });
            btn.addEventListener('click', function(){ 
                window.location.href = mailtoLink; 
            });
            toast.appendChild(btn);
        }

        // Show animation
        requestAnimationFrame(function(){ 
            toast.style.opacity = '1'; 
            toast.style.transform = 'translateY(0)'; 
        });

        // Auto hide after 5 seconds
        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(function(){ 
            toast.style.opacity = '0'; 
            setTimeout(function(){ 
                if(toast.parentNode) toast.parentNode.removeChild(toast); 
            }, 300); 
        }, 5000);
    }

    // Build mailto link for fallback
    function buildMailto(name, email, subject, message, section){
        const bodyLines = [];
        if(name) bodyLines.push('Nom: ' + name);
        if(email) bodyLines.push('Email: ' + email);
        if(section) bodyLines.push('Section: ' + section);
        bodyLines.push('');
        bodyLines.push(message || '');
        const body = encodeURIComponent(bodyLines.join('\n'));
        return `mailto:jerome.focant@student.hepl.be?subject=${encodeURIComponent(subject)}&body=${body}`;
    }

    // Send via EmailJS REST API (fallback when SDK unavailable)
    function sendViaRestAPI(templateParams){
        const payload = {
            service_id: 'service_dobtwbv',
            template_id: 'template_x0hpf9c',
            user_id: '6WNepAHVTUsPccRF3',
            template_params: templateParams
        };
        
        return fetch('https://api.emailjs.com/api/v1.0/email/send', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(function(res){
            if(!res.ok) {
                return res.text().then(function(text) {
                    throw new Error(text || ('HTTP ' + res.status));
                });
            }
            return res.text();
        });
    }

    // Update status element
    function updateStatus(message, isError){
        const status = document.getElementById('contact-status');
        if(status) {
            status.textContent = message;
            status.style.color = isError ? '#ef4444' : '#16a34a';
        }
    }

    // Handle form submission
    form.addEventListener('submit', function(e){
        e.preventDefault();

        // Get form values
        const name = document.getElementById('contact-name').value.trim();
        const email = document.getElementById('contact-email').value.trim();
        const subject = document.getElementById('contact-subject').value.trim() || 'Message depuis le site HEPL Tech Lab';
        const message = document.getElementById('contact-message').value.trim();
        const sectionEl = document.getElementById('contact-section');
        const section = sectionEl ? sectionEl.value.trim() : '';

        // Prepare template parameters
        const templateParams = {
            name: name || 'Anonyme',
            message: message || '(vide)',
            time: new Date().toLocaleString(),
            title: subject,
            email: email || '',
            section: section || ''
        };

        // Try to send email
        ensureEmailJS(function(sdkAvailable){
            if(sdkAvailable && window.emailjs && typeof emailjs.send === 'function'){
                // Use SDK
                emailjs.send('service_dobtwbv', 'template_x0hpf9c', templateParams)
                    .then(function(){
                        showToast('Message envoyé — merci !', 'success');
                        updateStatus('Message envoyé — merci !', false);
                        form.reset();
                    })
                    .catch(function(error){
                        console.warn('EmailJS SDK send failed, trying REST fallback:', error);
                        // Try REST fallback
                        sendViaRestAPI(templateParams)
                            .then(function(){
                                showToast('Message envoyé via API (fallback) — merci !', 'success');
                                updateStatus('Message envoyé via API (fallback) — merci !', false);
                                form.reset();
                            })
                            .catch(function(err){
                                console.error('EmailJS REST fallback error:', err);
                                const mailtoLink = buildMailto(name, email, subject, message, section);
                                showToast('L\'envoi a échoué. Utilisez votre client mail.', 'error', mailtoLink);
                                updateStatus('L\'envoi a échoué. Voir la console pour détails.', true);
                            });
                    });
            } else {
                // SDK not available, use REST API directly
                sendViaRestAPI(templateParams)
                    .then(function(){
                        showToast('Message envoyé via API — merci !', 'success');
                        updateStatus('Message envoyé via API — merci !', false);
                        form.reset();
                    })
                    .catch(function(err){
                        console.error('EmailJS REST send error:', err);
                        const mailtoLink = buildMailto(name, email, subject, message, section);
                        showToast('Impossible d\'envoyer via le service. Utilisez votre client mail.', 'error', mailtoLink);
                        updateStatus('L\'envoi a échoué. Voir la console pour détails.', true);
                    });
            }
        });
    });

})();
