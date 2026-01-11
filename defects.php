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
  <meta charset="UTF-8">
  <title>Defects – Tunisair DDS</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="darkmode.css">
  <link rel = "icon" type = "image/png" href = "tunisairlogo.png">
  <style>
     body.dark-mode a{
      color:white
    }
    .filter-bar{margin:20px auto;max-width:900px;display:flex;gap:10px;flex-wrap:wrap}
    .filter-bar select,.filter-bar input{padding:6px 8px}
    table{width:100%;border-collapse:collapse;margin:0 auto;max-width:1200px}
    th,td{border:1px solid #ccc;padding:6px 10px;text-align:center}
    th.sortable{cursor:pointer;color:#d71920}
    th.sortable:after{content:' ⬍';font-size:12px;color:#999}
    #pagination{margin:15px;text-align:center}
    #pagination button{background-color:rgb(255, 255, 255); color: black; margin:0 5px;padding:5px 10px;cursor:pointer;}
    #pagination button:hover{background-color: #a31416;}
    #pagination button.active{font-weight: bold;background-color: white;}
  </style>
</head>
<body>
<header>
  <h1 style="padding:15px;">📋 Defect List</h1>
  <a href="index.php" style="margin-left:15px;text-decoration:none;">⬅️ Dashboard</a>
  <div><button onclick="toggleDarkMode()" class="dm" style="margin:0;float:right;">🌓</button></div>
</header>

<!-- search / filter bar -->
<div class="filter-bar">
  <select id="aircraftSel">
    <option value="">All Aircraft</option>
    <option>IFM</option><option>IFN</option><option>IMA</option>
    <option>IMB</option><option>IMR</option><option>IMX</option>
    <option>IMY</option><option>IMZ</option>
  </select>

  <select id="statusSel">
    <option value="">All Status</option>
    <option value="open">Open only</option>
    <option value="closed">Closed only</option>
  </select>

  <input type="text" id="searchBox" placeholder="Search DDS or defect description">
  <button onclick="loadDefects()">Search</button>
</div>

<table id="defectsTbl">
  <thead>
    <tr>
      <th class="sortable" data-sort="numero_dds">🆔 DDS #</th>
      <th>✈️ Aircraft</th>
      <th class="sortable" data-sort="date_signalement">📅 Report Date</th>
      <th>📝 Defect Desc</th>
      <th>Status</th>
      <th>⚠️ Situation</th>
      <th>📍 Zone</th>
      <th>⏱️ Flight Hours</th>
      <th>✈️ Flight Cycles</th>
      <th>📅 Closure Date</th>
      <th>👷 Technician</th>
      <th>🔧 OE Ref</th>
      <th>🗂️ Work Order</th>
      <th>🗂️ Closure WO</th>
      <th>🔢 Part Number</th>
      <th>⚠️ Expiry Condition</th>
      <th>✏️</th>
      <th>🗑️</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>
<div id="pagination" style="text-align: center; margin-top: 15px;"></div>
<footer>
    <p>© 2025 Tunisair – DDS System</p>
</footer>

<script>
let sortBy='date_signalement', order='desc';
let currentPage = 1;
const rowsPerPage = 10;
let allDefects = [];

function renderTable(data){
  const tb=document.querySelector('#defectsTbl tbody');
  tb.innerHTML='';
  const start = (currentPage - 1) * rowsPerPage;
  const pageData = data.slice(start, start + rowsPerPage);
  pageData.forEach(r=>{
    const status = r.date_cloture ? 'Closed' : 'Open';
    tb.innerHTML+=`
      <tr>
        <td>${r.numero_dds}</td>
        <td>${r.code_avion}</td>
        <td>${r.date_signalement}</td>
        <td>${r.defect.substring(0,40)}…</td>
        <td>${status}</td>
        <td>${r.situation}</td>
        <td>${r.zone_}</td>
        <td>${r.flight_hours}</td>
        <td>${r.flight_cycles}</td>
        <td>${r.date_cloture || ''}</td>
        <td>${r.technicien}</td>
        <td>${r.oe_reference}</td>
        <td>${r.work_order}</td>
        <td>${r.closure_work_order}</td>
        <td>${r.part_number}</td>
        <td>${r.expiry_condition}</td>
        <td><a href="ED/edit_defect.php?id=${r.id_defaut}" style="text-decoration:none" >✏️</a></td>
        <td><a href="ED/delete_defect.php?id=${r.id_defaut}" onclick="return confirm('Delete this defect?')" style="text-decoration:none" >🗑️</a></td>
      </tr>`;
  });
  renderPagination(data.length);
}

function renderPagination(total){
  const div = document.getElementById('pagination');
  div.innerHTML = '';
  const pages = Math.ceil(total / rowsPerPage);
  if (pages <= 1) return;

  if (currentPage > 1) {
    const prev = document.createElement('button');
    prev.textContent = '⬅️ Prev';
    prev.onclick = () => { currentPage--; renderTable(allDefects); };
    div.appendChild(prev);
  }

  for (let i = 1; i <= pages; i++) {
    const btn = document.createElement('button');
    btn.textContent = i;
    if (i === currentPage) btn.style.fontWeight = 'bold';
    btn.onclick = () => { currentPage = i; renderTable(allDefects); };
    div.appendChild(btn);
  }

  if (currentPage < pages) {
    const next = document.createElement('button');
    next.textContent = 'Next ➡️';
    next.onclick = () => { currentPage++; renderTable(allDefects); };
    div.appendChild(next);
  }
}

function loadDefects(){
  const params=new URLSearchParams({
    aircraft: document.getElementById('aircraftSel').value,
    status:   document.getElementById('statusSel').value,
    q:        document.getElementById('searchBox').value.trim(),
    sort:     sortBy,
    order:    order
  });
  fetch('API/get_defects.php?'+params.toString())
    .then(r=>r.json())
    .then(rows=>{
      allDefects = rows;
      currentPage = 1;
      renderTable(rows);
    })
    .catch(e=>console.error(e));
}

document.querySelectorAll('th.sortable').forEach(th=>{
  th.addEventListener('click',()=>{
    const s=th.dataset.sort;
    if (sortBy===s){ order = order==='asc'?'desc':'asc'; }
    else { sortBy=s; order='asc'; }
    loadDefects();
  });
});

loadDefects(); // first 
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
