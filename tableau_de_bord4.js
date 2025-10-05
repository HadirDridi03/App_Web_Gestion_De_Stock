document.querySelector('.csv-btn').addEventListener('click', function () {
    const rows = document.querySelectorAll("table tr");
    let csvContent = "";
  
    rows.forEach(row => {
      const cols = row.querySelectorAll("td, th");
      const rowData = Array.from(cols).map(col => col.innerText.trim()).join(";");
      csvContent += rowData + "\n";
    });
  
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "utilisateurs.csv";
    link.click();
  });
  

// Fonction Export CSV
document.querySelector('.csv-btn').addEventListener('click', function () {
    const rows = document.querySelectorAll("table tr");
    let csvContent = "";
  
    rows.forEach(row => {
      const cols = row.querySelectorAll("td, th");
      const rowData = Array.from(cols).map(col => col.innerText.trim()).join(";");
      csvContent += rowData + "\n";
    });
  
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "utilisateurs.csv";
    link.click();
  });
  
  // Fonction Recherche Dynamique
  document.getElementById("searchInput").addEventListener("input", function () {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll("#utilisateurTable tr");
  
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchValue) ? "" : "none";
    });
  });
  