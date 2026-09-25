function toast(message, type = 'success') {
    const container = document.getElementById('toast-container');

    // Create the toast element
    const toast = document.createElement('div');
    toast.classList.add('toast', type);

    // Message
    const messageElement = document.createElement('span');
    messageElement.classList.add('toast-message');
    messageElement.textContent = message;

    // Close button
    const closeButton = document.createElement('button');
    closeButton.classList.add('toast-close');
    closeButton.innerHTML = '&times;';

    closeButton.addEventListener('click', () => {
        toast.remove();
    });

    // Assemble toast
    toast.appendChild(messageElement);
    toast.appendChild(closeButton);

    // Add to container
    container.appendChild(toast);

    // Automatically remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 3000);
}