document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#ckn-login-form');
    
    form?.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.querySelector('#ckn-email').value;

        fetch(signin_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'handle_character_signin',
                email: email,
                nonce: document.querySelector('#signin_nonce').value,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (!data.is_error) {
                window.location.href = data.redirect_url; // Redirect to character data page
            } else {
                document.querySelector('#ckn-login-message').innerHTML = data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});