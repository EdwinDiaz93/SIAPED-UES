window.confirmAction = function (message, action) {
    window.dispatchEvent(new CustomEvent('confirm-action', { detail: { message, action } }));
};
