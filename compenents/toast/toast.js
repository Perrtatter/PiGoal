function toast(message, type = 'success') {
      const container = document.getElementById('toast-container');
      
      // Create the toast element
      const toast = document.createElement('div');
      toast.classList.add('toast', type);
      
      // Inject text message and functional close button
      toast.innerHTML = `
        <span>${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
      `;
      
      // Append to screen container
      container.appendChild(toast);
      
      // Automatically remove from DOM after CSS fadeOut completes (3000ms total)
      setTimeout(() => {
        // Check if element still exists (user might have clicked close button)
        if (toast.parentNode) {
          toast.remove();
        }
      }, 3000);
}