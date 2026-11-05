
document.addEventListener("DOMContentLoaded", function () {

    const contactForm = document.querySelector("#contactForm");

    if (contactForm) {

        contactForm.addEventListener("submit", function () {

            const submitButton = contactForm.querySelector(
                'button[type="submit"]'
            );

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = "Enviando...";
            }

        });

    }

});
