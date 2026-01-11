<?php
require_once 'auth_check.php';
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'technician') {
    echo "🚫 Access denied: viewers are not allowed here.";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Limitations List</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="darkmode.css">
  <link rel = "icon" type = "image/png" href = "tunisairlogo.png">
  <style>
    .controls {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 15px;
    }

    .controls select {
      margin-top: 15px;
      padding: 8px;
      font-size: 16px;
      background-color: #d71920; 
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .controls select:hover {
      background-color: #a31416;
    }

    #searchInput {
      width: 200px;
      margin-top: 25px;
      padding: 8px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    /* Pagination buttons style */
    .pagination-button {
      background-color:rgb(255, 255, 255);
      color: black;
      border: none;
      padding: 8px 12px;
      cursor: pointer;
      border-radius: 4px;
      font-weight: normal;
      transition: background-color 0.3s ease;
      margin: 0 5px;
    }

    .pagination-button:hover {
      background-color: #a31416;
    }

    .pagination-button.active {
      font-weight: bold;
      background-color:rgb(255, 255, 255);
    }
     body.dark-mode a{
      color:white
    }
  </style>
</head>
<body>
  <header>
    <div style="display: flex; align-items: center;">
      <img src="tunisairlogo.png" alt="Logo" style="height: 50px;">
      <h1>⏳ Limitations List</h1>
    </div>
    <div>
      <a href="index.php" class="toggle-dark" style="text-decoration:none;">⬅️ Back to Dashboard</a>
    </div>
    <button onclick="toggleDarkMode()" class="dm"style="margin:0;float:right;">🌓</button>
  </header>

  <div class="controls">
    <input type="text" id="searchInput" placeholder="🔍 Search ">
    <select id="aircraftFilter">
      <option value="">✈️ Filter by Aircraft</option>
    </select>
  </div>

  <div class="dashboard" style="overflow-x:auto; max-width: 95%; margin: auto;">
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; background: white;">
      <thead style="background-color: #d71920; color: white;">
        <tr>
          <th>Aircraft</th>
          <th>DDS #</th>
          <th>Lim FH</th>
          <th>Lim FC</th>
          <th>Lim Days</th>
          <th>Reste FH</th>
          <th>Reste FC</th>
          <th>Reste Jours</th>
          <th>FH/Day</th>
          <th>FC/Day</th>
          <th>Date Param</th>
          <th>✏️</th>
          <th>🗑️</th>
        </tr>
      </thead>
      <tbody id="table-body">
        <!-- Data will be populated by JS -->
      </tbody>
    </table>
  </div>
  <div id="pagination" style="text-align: center; margin-top: 15px;"></div>
  <footer>
    <p>© 2025 Tunisair – DDS System</p>
  </footer>

  

<script>
  const searchInput = document.getElementById("searchInput");
  const filterDropdown = document.getElementById("aircraftFilter");
  const tbody = document.getElementById("table-body");
  const paginationDiv = document.getElementById("pagination");

  let fullData = [];
  let currentPage = 1;
  const rowsPerPage = 10;
  let currentSortAsc = true;

  function renderTable(data) {
    tbody.innerHTML = "";
    const pageData = data.slice((currentPage - 1) * rowsPerPage, currentPage * rowsPerPage);
    pageData.forEach(row => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${row.code_avion}</td>
        <td>${row.numero_dds}</td>
        <td>${row.lim_fh}</td>
        <td>${row.lim_fc}</td>
        <td>${row.lim_day}</td>
        <td>${row.reste_fh}</td>
        <td>${row.reste_fc}</td>
        <td>${row.reste_jours}</td>
        <td>${row.fh_jour}</td>
        <td>${row.fc_jour}</td>
        <td>${row.date_param}</td>
        <td><a style="text-decoration:none;" href="ED/edit_limitation.php?id=${row.id_limitation}">✏️</a></td>
        <td><a style="text-decoration:none;" href="ED/delete_limitation.php?id=${row.id_limitation}" onclick="return confirm('Delete this limitation?')">🗑️</a></td>
      `;
      tbody.appendChild(tr);
    });

    renderPagination(data.length);
  }

  function renderPagination(total) {
    const totalPages = Math.ceil(total / rowsPerPage);
    paginationDiv.innerHTML = "";

    if (totalPages <= 1) return;

    if (currentPage > 1) {
      const prev = document.createElement("button");
      prev.textContent = "⬅️ Prev";
      prev.classList.add("pagination-button");
      prev.onclick = () => { currentPage--; filterAndRender(); };
      paginationDiv.appendChild(prev);
    }

    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement("button");
      btn.textContent = i;
      btn.classList.add("pagination-button");

      if (i === currentPage) btn.classList.add("active");
      btn.onclick = () => { currentPage = i; filterAndRender(); };
      paginationDiv.appendChild(btn);
    }

    if (currentPage < totalPages) {
      const next = document.createElement("button");
      next.textContent = "Next ➡️";
      next.classList.add("pagination-button");
      next.onclick = () => { currentPage++; filterAndRender(); };
      paginationDiv.appendChild(next);
    }
  }

  function filterAndRender() {
    const search = searchInput.value.toLowerCase();
    const aircraft = filterDropdown.value.toLowerCase();

    const filtered = fullData.filter(row => {
      const matchAircraft = aircraft === "" || row.code_avion.toLowerCase() === aircraft;
      const matchSearch =
        row.lim_fh.toString().includes(search) ||
        row.lim_fc.toString().includes(search) ||
        row.lim_day.toString().includes(search) ||
        row.reste_fh.toString().includes(search) ||
        row.reste_fc.toString().includes(search) ||
        row.reste_jours.toString().includes(search);

      return matchAircraft && matchSearch;
    });

    renderTable(filtered);
  }

  searchInput.addEventListener("input", () => {
    currentPage = 1;
    filterAndRender();
  });

  filterDropdown.addEventListener("change", () => {
    currentPage = 1;
    filterAndRender();
  });

  fetch("API/get_limitations.php")
    .then(res => res.json())
    .then(data => {
      fullData = data;
      populateAircraftDropdown(data);
      sortById();
      filterAndRender();
    });

  function sortById() {
    fullData.sort((a, b) => currentSortAsc ? a.id_limitation - b.id_limitation : b.id_limitation - a.id_limitation);
  }

  function populateAircraftDropdown(data) {
    const aircraftSet = new Set(data.map(r => r.code_avion));
    filterDropdown.innerHTML = '<option value="">✈️ Filter by Aircraft</option>';
    aircraftSet.forEach(code => {
      const option = document.createElement("option");
      option.value = code;
      option.textContent = code;
      filterDropdown.appendChild(option);
    });
  }

  // Optional: Clickable sort toggle on ID column
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelector("th").style.cursor = "pointer";
    document.querySelector("th").addEventListener("click", () => {
      currentSortAsc = !currentSortAsc;
      sortById();
      currentPage = 1;
      filterAndRender();
    });
  });
  function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("darkMode", document.body.classList.contains("dark-mode"));
  }

  window.onload = function () {
    if (localStorage.getItem("darkMode") === "true") {
      document.body.classList.add("dark-mode");
    }
  };
</script>
</body>
</html>
