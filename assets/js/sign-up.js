document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#ckn-signup-form');
    
    form?.addEventListener('submit', function(e) {
        e.preventDefault();
        document.querySelector('#ckn-signup').disabled = true;
        const email = document.querySelector('#ckn-email').value;

        if (!email) {
            document.querySelector('#ckn-signup-message').innerHTML = 'Please enter your email address.';
            return;
        }
        fetch(signup_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'handle_character_signup',
                email: email,
                nonce: document.querySelector('#signup_nonce').value,
            }),
        })
        .then(response => response.json())
        .then(data => {
            document.querySelector('#ckn-signup-message').innerHTML = data.message;

            if(data.is_error) {
                document.querySelector('#ckn-signup-message').classList.add('ckn-error');
            }
            else {
                document.querySelector('#ckn-email').value = '';
                document.querySelector('#ckn-signup-message').classList.remove('ckn-error');
            }
            document.querySelector('#ckn-signup').disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});