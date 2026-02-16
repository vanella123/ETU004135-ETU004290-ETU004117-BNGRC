<script>
function actualiser() {
    fetch('/resumeBesoinsAjax')
        .then(response => response.json())
        .then(data => {

            document.getElementById("total").innerHTML =
                parseFloat(data.besoins_totaux).toFixed(2) + " €";

            document.getElementById("satisfait").innerHTML =
                parseFloat(data.besoins_satisfaits).toFixed(2) + " €";

            document.getElementById("restant").innerHTML =
                parseFloat(data.besoins_restants).toFixed(2) + " €";
        })
}
</script>
