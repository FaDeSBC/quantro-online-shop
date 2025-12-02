class AlertNotification {
    constructor() {
        this.createContainer();
    }

    createContainer() {
        if (!document.getElementById('alert-container')) {
            const container = document.createElement('div');
            container.id = 'alert-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                display: flex;
                flex-direction: column;
                gap: 10px;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }
    }

    show(message, type = 'info', duration = 4000, buttons = null) {
        const alert = document.createElement('div');
        alert.className = `custom-alert alert-${type}`;
        
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        const colors = {
            success: { bg: '#10b981', border: '#059669' },
            error: { bg: '#ef4444', border: '#dc2626' },
            warning: { bg: '#f59e0b', border: '#d97706' },
            info: { bg: '#3b82f6', border: '#2563eb' }
        };

        const color = colors[type] || colors.info;

        alert.style.cssText = `
            background: white;
            border-left: 4px solid ${color.border};
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
            min-width: 300px;
            max-width: 400px;
        `;

        const icon = document.createElement('div');
        icon.style.cssText = `
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: ${color.bg};
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            flex-shrink: 0;
        `;
        icon.textContent = icons[type] || icons.info;

        const content = document.createElement('div');
        content.style.cssText = `
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        `;

        const messageEl = document.createElement('div');
        messageEl.style.cssText = `
            color: #1e293b;
            font-size: 14px;
            line-height: 1.5;
            font-weight: 500;
        `;
        messageEl.innerHTML = message;

        content.appendChild(messageEl);
        if (buttons && buttons.length > 0) {
            const buttonContainer = document.createElement('div');
            buttonContainer.style.cssText = `
                display: flex;
                gap: 8px;
                margin-top: 8px;
            `;

            buttons.forEach(btn => {
                const button = document.createElement('button');
                button.textContent = btn.text;
                button.style.cssText = `
                    padding: 6px 16px;
                    border: none;
                    border-radius: 6px;
                    font-size: 13px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s;
                    ${btn.primary ? 
                        `background: ${color.bg}; color: white;` : 
                        `background: #f1f5f9; color: #64748b;`
                    }
                `;
                button.onmouseover = () => {
                    button.style.transform = 'translateY(-1px)';
                    button.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                };
                button.onmouseout = () => {
                    button.style.transform = 'translateY(0)';
                    button.style.boxShadow = 'none';
                };
                button.onclick = () => {
                    if (btn.callback) btn.callback();
                    this.close(alert);
                };
                buttonContainer.appendChild(button);
            });

            content.appendChild(buttonContainer);
        }

        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            background: none;
            border: none;
            font-size: 24px;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: color 0.2s;
        `;
        closeBtn.onmouseover = () => closeBtn.style.color = '#64748b';
        closeBtn.onmouseout = () => closeBtn.style.color = '#94a3b8';
        closeBtn.onclick = () => this.close(alert);

        alert.appendChild(icon);
        alert.appendChild(content);
        if (!buttons || buttons.length === 0) {
            alert.appendChild(closeBtn);
        }

        const container = document.getElementById('alert-container');
        container.appendChild(alert);
        if ((!buttons || buttons.length === 0) && duration > 0) {
            setTimeout(() => this.close(alert), duration);
        }

        return alert;
    }

    close(alert) {
        alert.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 300);
    }

    success(message, duration = 4000) {
        return this.show(message, 'success', duration);
    }

    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration = 5000) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration = 4000) {
        return this.show(message, 'info', duration);
    }

    confirm(message, onConfirm, onCancel = null) {
        return this.show(message, 'warning', 0, [
            {
                text: 'Cancel',
                primary: false,
                callback: onCancel
            },
            {
                text: 'OK',
                primary: true,
                callback: onConfirm
            }
        ]);
    }
}
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .custom-alert {
        pointer-events: auto;
    }

    @media (max-width: 640px) {
        #alert-container {
            left: 10px;
            right: 10px;
            max-width: none;
        }
    }
`;
document.head.appendChild(style);
window.Alert = new AlertNotification();
window.showPasswordBreachWarning = function() {
    Alert.show(
        '<strong>Change your password.</strong><br>The password you just used was found in a data breach. We recommend changing your password now.',
        'warning',
        0,
        [{
            text: 'OK',
            primary: true,
            callback: () => {}
        }]
    );
};

window.showOrderConfirmation = function(orderId, total) {
    Alert.show(
        `<strong>Order placed successfully!</strong><br>Order ID: #${orderId}<br>Order Total: $${total}`,
        'success',
        0,
        [{
            text: 'OK',
            primary: true,
            callback: () => {
                window.location.href = 'myorders.php';
            }
        }]
    );
};

window.confirmDelete = function(itemType, onConfirm) {
    Alert.show(
        `Are you sure you want to delete this ${itemType}?`,
        'warning',
        0,
        [
            {
                text: 'Cancel',
                primary: false,
                callback: () => {}
            },
            {
                text: 'OK',
                primary: true,
                callback: onConfirm
            }
        ]
    );
    return false; 
};