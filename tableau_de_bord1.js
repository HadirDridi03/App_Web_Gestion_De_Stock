document.getElementById('searchInput').addEventListener('input', function () {
  const searchQuery = this.value.toLowerCase();
  const rows = document.querySelectorAll('#produitTable tr');
  
  rows.forEach(row => {
    const cells = row.getElementsByTagName('td');
    const productName = cells[0].textContent.toLowerCase();
    const supplierName = cells[1].textContent.toLowerCase();
    
    if (productName.includes(searchQuery) || supplierName.includes(searchQuery)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
});
