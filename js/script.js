document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.matches('[data-confirm-delete]')) {
        return;
    }

    if (!window.confirm('Are you sure you want to delete this task?')) {
        event.preventDefault();
    }
});
