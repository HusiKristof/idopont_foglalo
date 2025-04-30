const alerts = [];
    const ALERT_HEIGHT = 70;

    function showAlert(message, type) {
            //create new alert
            const alert = document.createElement('div');
            alert.className = `alert ${type}`;
            alert.textContent = message;

            //uj alert hozzáadása a DOM-hoz
            document.body.appendChild(alert);
            alerts.push(alert);

            //animation trigger
            setTimeout(() => {
                repositionAlerts();
            }, 10);

            //4mp utan remove
            setTimeout(() => {
                removeAlert(alert);
            }, 4000);
    }

    function repositionAlerts() {
        alerts.forEach((alert, index) => {
            const topPosition = 20 + (index * ALERT_HEIGHT);
            alert.style.top = topPosition + 'px';
            alert.classList.add('show');
         });
    }

    function removeAlert(alert) {
        const index = alerts.indexOf(alert);
        if (index > -1) {
            //animacio slide
            alert.classList.remove('show');
            alert.style.top = '-100px';

            //tombbol való eltávolítás
            alerts.splice(index, 1);

            //eltavolítás a DOM-ból
            setTimeout(() => {
                alert.remove();
                repositionAlerts();
            }, 500);
        }
    }