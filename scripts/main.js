document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete").forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            let userId = this.getAttribute("data-id");

            fetch("../admin/delete-user.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest("tr").remove();
                }
            })
            .catch(error => console.error("Request failed:", error));
        });
    });
});
