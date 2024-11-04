// JavaScript for 3D rotation effect on Add to Cart button
document.addEventListener('DOMContentLoaded', () => {
    const cartButtons = document.querySelectorAll('.cart-btn');

    cartButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();

            // Add the 3D rotation effect when the button is clicked
            button.classList.add('active');

            // Remove the effect after 1 second to reset the button
            setTimeout(() => {
                button.classList.remove('active');
            }, 1000);
        });
    });
});
