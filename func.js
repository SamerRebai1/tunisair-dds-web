 function updateClock() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString();
    const dateStr = now.toLocaleDateString();
    document.getElementById("clock").innerText = `${dateStr} – ${timeStr}`;
  }
  setInterval(updateClock, 1000);
  updateClock();

  function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
  }
  function toggleDropdown() {
  const menu = document.getElementById("limitationMenu");
  menu.style.display = (menu.style.display === "none") ? "block" : "none";
}
