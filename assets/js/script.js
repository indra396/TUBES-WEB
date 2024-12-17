// Implementasi validasi form atau interaktivitas lainnya
document.querySelector("form").addEventListener("submit", function(event) {
    let password = document.querySelector("input[name='password']").value;
    if (password.length < 6) {
        alert("Password terlalu pendek!");
        event.preventDefault(); // Mencegah pengiriman form jika password kurang dari 6 karakter
    }
});
