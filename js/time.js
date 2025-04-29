async function getCurrentTime() {
    try {
        const response = await fetch("https://timeapi.io/api/Time/current/zone?timeZone=Europe/Budapest");
        if (!response.ok) {
            throw new Error("Hiba történt az idő lekérése során.");
        }
        const data = await response.json();
        const currentTime = `${data.date} ${data.time}`;
        document.getElementById("time").textContent = currentTime;
    } catch (error) {
        document.getElementById("time").textContent = "Hiba: " + error.message;
    }
}

window.onload = getCurrentTime;
setInterval(getCurrentTime, 1000);
